<?php
/**
    Hydrid CAD/MDT - Computer Aided Dispatch / Mobile Data Terminal for use in GTA V Role-playing Communities.
    Copyright (C) 2018 - Hydrid Development Team

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
**/
require 'includes/connect.php';
include 'includes/config.php';
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in'])) {
    header('Location: ' . $url['login'] . '');
    exit();
}
include 'includes/isLoggedIn.php';

if (!staff_access) {
  header('Location: ' . $url['index'] . '');
  exit();
}

if (!staff_editUsers) {
  header('Location: ' . $url['index'] . '');
  exit();
}

if (isset($_GET['user']) && filter_var($_GET['user'], FILTER_VALIDATE_INT)) {
    $id   = strip_tags($_GET['user']);
    $sql  = "SELECT * FROM users WHERE user_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $userTable = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($userTable === false) {
       header('Location: ' . $url['civ_index'] . '');
       exit();
    } elseif ($id == 1) {
      if ($user_id != $id) {
        header('Location: ' . $url['staff_index'] . '?np=rootuser');
        exit();
      } else {
        $_SESSION['edit_user_id'] = $userTable['user_id'];
        $edit_username    = $userTable['username'];
        $edit_usergroup    = $userTable['usergroup'];
        $edit_join_date    = $userTable['join_date'];
        $edit_discord    = $userTable['discord'];
      }
    } else {
       $_SESSION['edit_user_id'] = $userTable['user_id'];
       $edit_username    = $userTable['username'];
       $edit_usergroup    = $userTable['usergroup'];
       $edit_join_date    = $userTable['join_date'];
       $edit_discord    = $userTable['discord'];
    }
 }

if (isset($_POST['updateUserBtn'])) {
    //Pull the variables from the form
    $update_username_form = $_POST['update_username'] ? trim($_POST['update_username']) : null;
    $update_password_form = $_POST['update_password'] ? trim($_POST['update_password']) : null;
    $update_usergroup_form = $_POST['update_usergroup'] ? trim($_POST['update_usergroup']) : null;
    $update_discord_form = $_POST['update_discord'] ? trim($_POST['update_discord']) : null;

    //Sanitize the variables, prevents xss, etc.
    $update_username        = strip_tags($update_username_form);
    $update_password        = strip_tags($update_password_form);
    $update_usergroup        = strip_tags($update_usergroup_form);
    $update_discord        = strip_tags($update_discord_form);

    if (discordModule_isInstalled) {
      if (empty($update_password)) {
        $sql     = "UPDATE `users` SET `username`=:username, `usergroup`=:usergroup, `discord`=:discord WHERE user_id=:userid";
        $stmt    = $pdo->prepare($sql);
        $stmt->bindValue(':username', $update_username);
        $stmt->bindValue(':usergroup', $update_usergroup);
        $stmt->bindValue(':userid', $_SESSION['edit_user_id']);
        $stmt->bindValue(':discord', $update_discord);
        $updateUser = $stmt->execute();
        if ($updateUser) {
          header('Location: ' . $url['staff_index'] . '?user=edited');
          exit();
        }
      } else {
        $passwordHash = password_hash($update_password, PASSWORD_BCRYPT, array("cost" => 12));
        $sql     = "UPDATE `users` SET `username`=:username, `password`=:password, `usergroup`=:usergroup, `discord`=:discord WHERE user_id=:userid";
        $stmt    = $pdo->prepare($sql);
        $stmt->bindValue(':username', $update_username);
        $stmt->bindValue(':password', $passwordHash);
        $stmt->bindValue(':usergroup', $update_usergroup);
        $stmt->bindValue(':userid', $_SESSION['edit_user_id']);
        $stmt->bindValue(':discord', $update_discord);
        $updateUser = $stmt->execute();
        if ($updateUser) {
          header('Location: ' . $url['staff_index'] . '?user=edited');
          exit();
        }
      }
    } else {
      if (empty($update_password)) {
        $sql     = "UPDATE `users` SET `username`=:username, `usergroup`=:usergroup WHERE user_id=:userid";
        $stmt    = $pdo->prepare($sql);
        $stmt->bindValue(':username', $update_username);
        $stmt->bindValue(':usergroup', $update_usergroup);
        $stmt->bindValue(':userid', $_SESSION['edit_user_id']);
        $updateUser = $stmt->execute();
        if ($updateUser) {
          header('Location: ' . $url['staff_index'] . '?user=edited');
          exit();
        }
      } else {
        $passwordHash = password_hash($update_password, PASSWORD_BCRYPT, array("cost" => 12));
        $sql     = "UPDATE `users` SET `username`=:username, `password`=:password, `usergroup`=:usergroup WHERE user_id=:userid";
        $stmt    = $pdo->prepare($sql);
        $stmt->bindValue(':username', $update_username);
        $stmt->bindValue(':password', $passwordHash);
        $stmt->bindValue(':usergroup', $update_usergroup);
        $stmt->bindValue(':userid', $_SESSION['edit_user_id']);
        $updateUser = $stmt->execute();
        if ($updateUser) {
          header('Location: ' . $url['staff_index'] . '?user=edited');
          exit();
        }
      }
    }
}

if (isset($_POST['deleteUserBtn'])) {
        $user_del = $_SESSION['edit_user_id'];
        $stmt = $pdo->prepare( "DELETE FROM users WHERE user_id ='$user_del'" );
        $deleteUser = $stmt->execute();
          header('Location: ' . $url['staff_index'] . '?user=deleted');
          exit();
    }
?>
<!DOCTYPE html>
<html>
<?php
$page_name = "Staff Edit User";
include('includes/header.php')
?>
<body>
   <div class="container-staff">
      <div class="main-staff">
         <a href="<?php echo "staff.php" ?>"><img src="assets/imgs/los_santos.png" class="main-logo" draggable="false"/></a>
         <div class="main-header-staff">
            Editing <?php echo $edit_username ?>
         </div>
         <?php print($message); ?>
         <div class="edit-users">
           <form method="post" action="staff-edituser.php">
         <div class="form-group">
           <label>Username</label>
           <input type="text" class="form-control" name="update_username" placeholder="<?php echo $edit_username ?>" value="<?php echo $edit_username ?>" aria-label="Username" aria-describedby="basic-addon2">
         </div>
         <div class="form-group">
           <label>Password</label>
           <input type="password" class="form-control" name="update_password" placeholder="New Password...." aria-label="Password" aria-describedby="basic-addon2">
         </div>
         <div class="form-group">
           <label>Usergroup</label>
           <select class="form-control" name="update_usergroup">
             <option value="<?php echo $edit_usergroup;?>" selected="true"><?php echo $edit_usergroup;?></option>
             <option value="Banned">BANNED</option>
             <option value="Unverified">Unverified</option>
             <option value="User">User</option>
             <option value="Moderator">Moderator</option>
             <option value="Admin">Admin</option>
             <?php if ($user_usergroup === "Management"): ?>
               <option value="Management">Management</option>
             <?php endif; ?>
           </select>
         </div>
         <?php if (discordModule_isInstalled): ?>
           <div class="form-group">
             <label>Discord</label>
             <input type="text" class="form-control" name="update_discord" placeholder="<?php echo $edit_discord ?>" value="<?php echo $edit_discord ?>" aria-label="Discord" aria-describedby="basic-addon2">
           </div>
         <?php endif; ?>
         <div class="form-group">
           <label>Joined On</label>
           <input type="text" class="form-control" value="<?php echo $edit_join_date ?>" aria-label="Join Date" readonly="" aria-describedby="basic-addon2" disabled>
         </div>
         <small><strong><font color="red">SETTING SOMEONE AS MANAGEMENT MEANS THEY HAVE FULL ACCESS TO THE ENTIRE SYSTEM.</font></strong></small>
         <div class="row">
           <div class="col">
             <div class="form-group">
               <button class="btn btn-success btn-block" name="updateUserBtn" id="updateUserBtn" type="submit" type="button">Update <?php echo $edit_username ?></button>
             </div>
           </div>
           <div class="col">
             <div class="form-group">
               <button class="btn btn-danger btn-block" name="deleteUserBtn" id="deleteUserBtn" type="submit" type="button">Delete <?php echo $edit_username ?></button>
             </div>
           </div>
         </div>
       </form>
       </div>
         <?php echo $ftter; ?>
      </div>
   </div>

   <!-- javascript -->
   <?php include('includes/js.php'); ?>
   <script>
   $(document).ready(function () {
     $("#dismiss").delay(3000).fadeOut("slow");
   });
   </script>
   <!-- end javascript -->
</body>
</html>
