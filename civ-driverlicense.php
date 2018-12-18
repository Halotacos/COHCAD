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
include 'functions/refreshCivVariables.php';
 if (isset($_POST['deleteCharbtn'])) {
   $sql     = "UPDATE `characters` SET `status` = :status WHERE character_id = :charid";
   $stmt    = $pdo->prepare($sql);
   $status  = 'Disabled';
   $stmt->bindValue(':charid', $_SESSION['character_id']);
   $stmt->bindValue(':status', $status);
   $deleteChar = $stmt->execute();
   //Continue
   if ($deleteChar) {
      logAction('Deleted Character', $user_username);
      header('Location: ' . $url['civ_index'] . '?character=deleted');
   }
}

if (isset($_POST['updateLicenseStatus'])) {
  $license_driver_form = !empty($_POST['license_driver']) ? trim($_POST['license_driver']) : null;
  $license_driver        = strip_tags($license_driver_form);

  $sql     = "UPDATE `characters` SET `license_driver` = :status WHERE character_id = :charid";
  $stmt    = $pdo->prepare($sql);
  $stmt->bindValue(':charid', $_SESSION['character_id']);
  $stmt->bindValue(':status', $license_driver);
  $updateLicense = $stmt->execute();
  //Continue
  if ($updateLicense) {
     logAction('Updated Characters Drivers License', $user_username);
     header('Location: ' . $url['civ_driverlicense'] . '?license=updated');
  }
}

//Alerts
if (isset($_GET['license']) && strip_tags($_GET['license']) === 'updated') {
   $message = '<div class="alert alert-success" role="alert">License Status Updated!</div>';
}
?>
<!DOCTYPE html>
<html>
<?php
$page_name = "Viewing Civilian";
include('includes/header.php')
?>
   <body>
      <div class="container">
         <div class="main">
            <a href="<?php print $url['civ_view'] ?>?id=<?php echo $_SESSION['character_id'] ?>"><img src="assets/imgs/dmv.png" class="main-logo" draggable="false"/></a>
            <div class="main-header">
               Hello, <?php echo $_SESSION['character_first_name'] ?>
            </div>
            <?php print($message); ?>
            <strong>Drivers License Status: <?php if ($_SESSION['character_license_driver'] === "None") {
              echo '<font color="red">None</font>';
            } elseif ($_SESSION['character_license_driver'] === "Expired") {
              echo '<font color="red">Expired</font>';
            } elseif ($_SESSION['character_license_driver'] === "Invalid") {
              echo '<font color="red">Invalid</font>';
            } elseif ($_SESSION['character_license_driver'] === "Fake") {
              echo '<font color="maroon">Fake</font>';
            } elseif ($_SESSION['character_license_driver'] === "Suspended") {
              echo '<font color="maroon">Suspended</font>';
            } elseif ($_SESSION['character_license_driver'] === "Valid") {
              echo '<font color="green">Valid</font>';
            }
            ?></strong>
            <form method="post" action="civ-driverlicense.php">
              <div class="form-group">
                 <select class="form-control" name="license_driver" required>
                    <option value="" disabled selected>License Status...</option>
                    <option value="None">None</option>
                    <option value="Expired">Expired</option>
                    <option value="Invalid">Invalid</option>
                    <option value="Fake">Fake</option>
                    <option value="Suspended">Suspended</option>
                    <option value="Valid">Valid</option>
                 </select>
                 <button class="btn btn-info btn-block" name="updateLicenseStatus">Save</button>
              </div>
            </form>
            <?php echo $ftter; ?>
         </div>
      </div>
      <?php include('includes/js.php'); ?>
   </body>
</html>
