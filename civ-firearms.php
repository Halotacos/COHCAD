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

if (isset($_POST['updateCCWStatusBtn'])) {
  $license_firearm_form = !empty($_POST['license_firearm']) ? trim($_POST['license_firearm']) : null;
  $license_firearm        = strip_tags($license_firearm_form);



  $sql     = "UPDATE `characters` SET `license_firearm` = :status WHERE character_id = :charid";
  $stmt    = $pdo->prepare($sql);
  $stmt->bindValue(':charid', $_SESSION['character_id']);
  $stmt->bindValue(':status', $license_firearm);
  $updateLicense = $stmt->execute();
  //Continue
  if ($updateLicense) {
    logAction('Updated Characters CCW License', $user_username);
     header('Location: ' . $url['civ_firearms'] . '?license=updated');
  }
}

if (isset($_POST['registerFirearmbtn'])) {
    $weapon_form = !empty($_POST['weapon']) ? trim($_POST['weapon']) : null;
    $weapon            = strip_tags($weapon_form);

    $rpstatus_form = !empty($_POST['rpstatus']) ? trim($_POST['rpstatus']) : null;
    $rpstatus            = strip_tags($rpstatus_form);
    //check if has valid ccw
    if ($_SESSION['character_license_firearm'] === "Invalid" || $_SESSION['character_license_firearm'] === "Suspended") {
      header('Location: ' . $url['civ_firearms'] . '?license=invalid');
      exit();
    }
    //
    //generations for serial #
    function generate3LTRSerial($length = 3) {
    return substr(str_shuffle(str_repeat($x='ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }
    function generate7NUMSerial($length2 = 7) {
    return substr(str_shuffle(str_repeat($x1='0123456789', ceil($length2/strlen($x1)) )),1,$length2);
    }
    $serial_1 = generate3LTRSerial();
    $serial_2 = generate7NUMSerial();
    $serial = $serial_1 . '' . $serial_2;
    //
    $sql          = "INSERT INTO weapons (wpn_type, wpn_serial, wpn_owner, wpn_ownername, wpn_rpstatus) VALUES (:wpn_type, :wpn_serial, :wpn_owner, :wpn_ownername, :wpn_rpstatus)";
    $stmt         = $pdo->prepare($sql);
    $stmt->bindValue(':wpn_type', $weapon);
    $stmt->bindValue(':wpn_serial', $serial);
    $stmt->bindValue(':wpn_ownername', $_SESSION['character_first_name'] . ' ' . $_SESSION['character_last_name']);
    $stmt->bindValue(':wpn_owner', $_SESSION['character_id']);
    $stmt->bindValue(':wpn_rpstatus', $rpstatus);
    $result = $stmt->execute();
    if ($result) {
        //redirect
        logAction('Registered Firearm For Character', $user_username);
        header('Location: ' . $url['civ_firearms'] . '?id='. $_SESSION['character_id'] .'&firearm=registered');
    }
}

//Alerts
if (isset($_GET['firearm']) && strip_tags($_GET['firearm']) === 'registered') {
   $message = '<div class="alert alert-success" role="alert">Your Firearm Has Been Registered!</div>';
} elseif (isset($_GET['license']) && strip_tags($_GET['license']) === 'updated') {
   $message = '<div class="alert alert-success" role="alert">License Status Updated!</div>';
} elseif (isset($_GET['license']) && strip_tags($_GET['license']) === 'invalid') {
   $message = '<div class="alert alert-danger" role="alert">It Appears You Do Not Have A CCW.</div>';
} elseif (isset($_GET['firearm']) && strip_tags($_GET['firearm']) === 'deleted') {
   $message = '<div class="alert alert-danger" role="alert">Firearm Deleted.</div>';
}
?>
<!DOCTYPE html>
<html>
   <?php
   $page_name = "Civilian Firearm Management";
   include('includes/header.php')
   ?>
   <script>
   function showWpn(str) {
       if (str == "") {
           document.getElementById("txtHint").innerHTML = "";
           return;
       } else {
           if (window.XMLHttpRequest) {
               // code for IE7+, Firefox, Chrome, Opera, Safari
               xmlhttp = new XMLHttpRequest();
           } else {
               // code for IE6, IE5
               xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
           }
           xmlhttp.onreadystatechange = function() {
               if (this.readyState == 4 && this.status == 200) {
                   document.getElementById("txtHint").innerHTML = this.responseText;
               }
           };
           xmlhttp.open("GET","functions/getwpn.php?q="+str,true);
           xmlhttp.send();
       }
   }
   </script>
   <body>
      <div class="container">
         <div class="main">
            <a href="<?php print $url['civ_view'] ?>?id=<?php echo $_SESSION['character_id'] ?>"><img src="assets/imgs/los_santos.png" class="main-logo" draggable="false"/></a>
            <div class="main-header">
               Hello, <?php echo $_SESSION['character_first_name'] ?>
            </div>
            <?php print($message); ?>
              <a data-toggle="modal" href="#ccwmodal" class="btn btn-primary btn-block btn-sb">CCW License</a>

              <a data-toggle="modal" href="#firearmreg" class="btn btn-primary btn-block btn-sb">Register New Firearm</a>

              <a data-toggle="modal" href="#myfirearmsmodal" class="btn btn-primary btn-block btn-sb">Owned Firearms</a>

            <div class="modal fade" id="myfirearmsmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Owned Firearms</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form>
                      <select class="form-control" name="vehicles" onchange="showWpn(this.value)">
                        <option selected="true" disabled="disabled">Select Firearm</option>
                        <?php
                        $status = 'Enabled';
                        $char_id = $_SESSION['character_id'];
                        $getFirearms = "SELECT * FROM weapons WHERE wpn_owner='$char_id' AND wpn_status='$status'";
                        $result = $pdo->prepare($getFirearms);
                        $result->execute();
                        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                          echo '<option value="'. $row['wpn_id'] .'">'. $row['wpn_type'] .' - '. $row['wpn_serial'] .'</option>';
                        }
                         ?>
                      </select>
                    </form>

                    <div id="txtHint"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal fade" id="ccwmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CCW License</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form method="post" action="civ-firearms.php">
                      <div class="form-group">
                        <strong>CCW License Status: <?php if ($_SESSION['character_license_firearm'] === "None") {
                          echo '<font color="red">None</font>';
                        } elseif ($_SESSION['character_license_firearm'] === "Invalid") {
                          echo '<font color="red">Invalid</font>';
                        } elseif ($_SESSION['character_license_firearm'] === "Suspended") {
                          echo '<font color="maroon">Suspended</font>';
                        } elseif ($_SESSION['character_license_firearm'] === "Valid") {
                          echo '<font color="green">Valid</font>';
                        }
                        ?></strong>
                         <select class="form-control" name="license_firearm" required>
                            <option selected="true" disabled="disabled">CCW License Status...</option>
                            <option value="None">None</option>
                            <option value="Valid">Valid</option>
                            <option value="Invalid">Invalid</option>
                            <option value="Suspended">Suspended</option>
                         </select>
                      </div>
                  </div>
                  <div class="modal-footer">
                    <button class="btn btn-primary btn-block" name="updateCCWStatusBtn">Update</button>
                  </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="modal fade" id="firearmreg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Firearm Registration</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form method="post" action="civ-firearms.php">
                      <div class="form-group">
                         <select class="form-control" name="weapon" required>
                            <option value="" disabled selected>Weapon...</option>
                            <option value="AP Pistol">AP Pistol</option>
                            <option value="Combat Pistol">Combat Pistol</option>
                            <option value="Heavy Pistol">Heavy Pistol</option>
                            <option value="Heavy Revolver">Heavy Revolver</option>
                            <option value="Heavy Revolver Mk II">Heavy Revolver Mk II</option>
                            <option value="Marksman Pistol">Marksman Pistol</option>
                            <option value="Pistol">Pistol</option>
                            <option value="Pistol Mk II">Pistol Mk II</option>
                            <option value="Pistol .50">Pistol .50</option>
                            <option value="SNS Pistol">SNS Pistol</option>
                            <option value="SNS Pistol Mk II">SNS Pistol Mk II</option>
                            <option value="Vintage Pistol">Vintage Pistol</option>
                            <option value="Double-Action Revolver">Double-Action Revolver</option>
                            <option value="Assault Shotgun">Assault Shotgun</option>
                            <option value="Bullpup Shotgun">Bullpup Shotgun</option>
                            <option value="Double Barrel Shotgun">Double Barrel Shotgun</option>
                            <option value="Heavy Shotgun">Heavy Shotgun</option>
                            <option value="Musket">Musket</option>
                            <option value="Pump Shotgun">Pump Shotgun</option>
                            <option value="Pump Shotgun Mk II">Pump Shotgun Mk II</option>
                            <option value="Sawed-Off Shotgun">Sawed-Off Shotgun</option>
                            <option value="Sweeper Shotgun">Sweeper Shotgun</option>
                            <option value="Assault SMG">Assault SMG</option>
                            <option value="Combat MG">Combat MG</option>
                            <option value="Combat MG Mk II">Combat MG Mk II</option>
                            <option value="Combat PDW">Combat PDW</option>
                            <option value="Gusenberg Sweeper">Gusenberg Sweeper</option>
                            <option value="Machine Pistol">Machine Pistol</option>
                            <option value="MG">MG</option>
                            <option value="Micro SMG">Micro SMG</option>
                            <option value="Mini SMG">Mini SMG</option>
                            <option value="SMG">SMG</option>
                            <option value="SMG Mk II">SMG Mk II</option>
                            <option value="Advanced Rifle">Advanced Rifle</option>
                            <option value="Assault Rifle">Assault Rifle</option>
                            <option value="Assault Rifle Mk II">Assault Rifle Mk II</option>
                            <option value="Bullpup Rifle">Bullpup Rifle</option>
                            <option value="Bullpup Rifle Mk II">Bullpup Rifle Mk II</option>
                            <option value="Carbine Rifle">Carbine Rifle</option>
                            <option value="Carbine Rifle Mk II">Carbine Rifle Mk II</option>
                            <option value="Compact Rifle">Compact Rifle</option>
                            <option value="Special Carbine">Special Carbine</option>
                            <option value="Special Carbine Mk II">Special Carbine Mk II</option>
                            <option value="Heavy Sniper">Heavy Sniper</option>
                            <option value="Heavy Sniper Mk II">Heavy Sniper Mk II</option>
                            <option value="Marksman Rifle">Marksman Rifle</option>
                            <option value="Marksman Rifle Mk II">Marksman Rifle Mk II</option>
                            <option value="Sniper Rifle">Sniper Rifle</option>
                         </select>
                      </div>
                      <div class="form-group">
                         <select class="form-control" name="rpstatus" required>
                            <option value="" disabled selected>Status...</option>
                            <option value="Valid">Valid</option>
                            <option value="Stolen">Stolen</option>
                            <option value="Blackmarket">Blackmarket</option>
                         </select>
                      </div>
                  </div>
                  <div class="modal-footer">
                    <button class="btn btn-primary btn-block" name="registerFirearmbtn">Register</button>
                  </div>
                  </form>
                </div>
              </div>
            </div>
            <?php echo $ftter; ?>
         </div>
      </div>
      <?php include('includes/js.php'); ?>
   </body>
</html>
