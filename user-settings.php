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

if (!panel_access) {
  session_unset();
  header('Location: ' . $url['login'] . '?unverified=true');
  exit();
}

if (isset($_POST['updateUserBtn'])) {
  //Pull the variables from the form
  $update_email_form = $_POST['update_email'] ? trim($_POST['update_email']) : null;
  $update_password_form = $_POST['update_password'] ? trim($_POST['update_password']) : null;

  //Sanitize the variables, prevents xss, etc.
  $update_email        = strip_tags($update_email_form);
  $update_password        = strip_tags($update_password_form);

  if (empty($update_password)) {
    $sql     = "UPDATE `users` SET `email`=:email WHERE user_id=:userid";
    $stmt    = $pdo->prepare($sql);
    $stmt->bindValue(':username', $update_username);
    $stmt->bindValue(':email', $update_email);
    $stmt->bindValue(':usergroup', $update_usergroup);
    $stmt->bindValue(':userid', $user_id);
    $updateUser = $stmt->execute();
    if ($updateUser) {
      header('Location: ' . $url['logout'] . '?settings=updated');
      exit();
    }
  } else {
    $passwordHash = password_hash($update_password, PASSWORD_BCRYPT, array("cost" => 12));
    $sql     = "UPDATE `users` SET `email`=:email, `password`=:password WHERE user_id=:userid";
    $stmt    = $pdo->prepare($sql);
    $stmt->bindValue(':username', $update_username);
    $stmt->bindValue(':email', $update_email);
    $stmt->bindValue(':password', $passwordHash);
    $stmt->bindValue(':userid', $user_id);
    $updateUser = $stmt->execute();
    if ($updateUser) {
      header('Location: ' . $url['logout'] . '?settings=updated');
      exit();
    }
  }
}
?>
<!DOCTYPE html>
<html>
<?php
$page_name = "Settings";
include('includes/header.php')
?>
<body>
   <div class="container">
      <div class="main">
         <img src="assets/imgs/los_santos.png" class="main-logo" draggable="false"/>
         <div class="main-header">
            Hello, <?php echo $user_username ?> <?php if (staff_access) {
              echo '<a href="staff.php"><i class="fas fa-fingerprint"></i></a>';
            } ?> <a href="user-settings.php"><i class="fas fa-cog"></i></a> <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
         </div>
         <?php
         if ($update_in_progress === "Yes") {
           echo '<strong><font color="red">ALL PANELS ARE CURRENTLY OFFLINE.</font></strong>';
         } ?>
         <?php print($message); ?>
         <div class="edit-users">
           <form method="post" action="user-settings.php">
         <div class="form-group">
           <label>Email</label>
           <input type="text" class="form-control" name="update_email" placeholder="<?php echo $user_email; ?>" value="<?php echo $user_email; ?>" aria-label="Email" aria-describedby="basic-addon2">
         </div>
         <div class="form-group">
           <label>Password</label>
           <input type="password" class="form-control" name="update_password" placeholder="New Password...." aria-label="Password" aria-describedby="basic-addon2">
         </div>
         <div class="row">
           <div class="col">
             <div class="form-group">
               <button class="btn btn-success btn-block" name="updateUserBtn" id="updateUserBtn" type="submit" type="button">Update Settings</button>
             </div>
           </div>
         </div>
       </form>
       </div>
         <?php echo $ftter; ?>
      </div>
   </div>
   <?php include('includes/js.php'); ?>
</body>
</html>
