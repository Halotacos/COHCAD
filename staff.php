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
error_reporting(0);
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

if (isset($_POST['discordModule_install'])) {
  $stmt1 = $pdo->prepare( "ALTER TABLE `settings` ADD `discord_module` VARCHAR(36) NOT NULL DEFAULT 'Enabled' AFTER `donator`" );
  $stmt1->execute();
  sleep(5);
  $stmt2 = $pdo->prepare( "ALTER TABLE `users` ADD `discord` VARCHAR(60) NULL AFTER `join_ip`" );
  $stmt2->execute();
  logAction('(STAFF) Installed Discord Module', $user_username);
  header('Location: ' . $url['staff_index'] . '?module=installed');
  exit();
} elseif (isset($_POST['discordModule_uninstall'])) {
  $stmt1 = $pdo->prepare( "ALTER TABLE `settings` DROP `discord_module`" );
  $stmt1->execute();
  sleep(3);
  logAction('(STAFF) Uninstalled Discord Module', $user_username);
  header('Location: ' . $url['staff_index'] . '?module=uninstalled');
  exit();
}

if (isset($_POST['mapModule_install'])) {
  $stmt1 = $pdo->prepare( "ALTER TABLE `settings` ADD `map_module` VARCHAR(36) NOT NULL DEFAULT 'Enabled' AFTER `donator`" );
  $stmt1->execute();
  sleep(3);
  $stmt2 = $pdo->prepare( "ALTER TABLE `settings` ADD `map_module_link` VARCHAR(355) NOT NULL DEFAULT '#' AFTER `map_module`" );
  $stmt2->execute();
  sleep(3);
  logAction('(STAFF) Installed LiveMap Module', $user_username);
  header('Location: ' . $url['staff_index'] . '?module=installed');
  exit();
} elseif (isset($_POST['mapModule_uninstall'])) {
  $stmt1 = $pdo->prepare( "ALTER TABLE `settings` DROP `map_module`" );
  $stmt1->execute();
  sleep(3);
  $stmt1 = $pdo->prepare( "ALTER TABLE `settings` DROP `map_module_link`" );
  $stmt1->execute();
  sleep(3);
  logAction('(STAFF) Uninstalled LiveMap Module', $user_username);
  header('Location: ' . $url['staff_index'] . '?module=uninstalled');
  exit();
} elseif (isset($_POST['mapModule_updateSettings'])) {
    //Pull the variables from the form
    $mapModule_link_form = $_POST['mapModule_link'] ? trim($_POST['mapModule_link']) : null;
    //Sanitize the variables, prevents xss, etc.
    $update_mapModule_link      = strip_tags($mapModule_link_form);
    $sql2 = $pdo->prepare( "UPDATE `settings` SET map_module_link='$update_mapModule_link'" );
    $sql2->execute();
    logAction('(STAFF) Updated LiveMap Module Settings', $user_username);
    header('Location: staff.php?module=updated');
    exit();
}

if (isset($_POST['subdivisionModule_install'])) {
  $stmt = $pdo->prepare( "ALTER TABLE `settings` ADD `subdivision_module` VARCHAR(36) NOT NULL DEFAULT 'Enabled' AFTER `donator`" );
  $stmt->execute();
  sleep(2.3);
  $stmt1 = $pdo->prepare( "CREATE TABLE `sub_divisions` (
    `id` int(11) NOT NULL,
    `name` varchar(64) NOT NULL
  )" );
  $stmt1->execute();
  sleep(2.3);
  $stmt11 = $pdo->prepare( "ALTER TABLE `sub_divisions`
  ADD PRIMARY KEY (`id`)" );
  $stmt11->execute();
  sleep(2.3);
  $stmt12 = $pdo->prepare( "ALTER TABLE `sub_divisions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;" );
  $stmt12->execute();
  sleep(2.3);
  $sql2 = $pdo->prepare( "INSERT INTO `sub_divisions` (id, name) VALUES ('1', 'Example Sub Division')" );
  $sql2->execute();
  sleep(3);
  logAction('(STAFF) Installed Sub Division Module', $user_username);
  header('Location: ' . $url['staff_index'] . '?module=installed');
  exit();
} elseif (isset($_POST['subdivisionModule_uninstall'])) {
  $stmt1 = $pdo->prepare( "DROP TABLE `sub_divisions`" );
  $stmt1->execute();
  sleep(4);
  $stmt2 = $pdo->prepare( "ALTER TABLE `settings` DROP `subdivision_module`" );
  $stmt2->execute();
logAction('(STAFF) Uninstalled Sub Division Module', $user_username);
    header('Location: staff.php?module=uninstalled');
    exit();
} elseif (isset($_POST['subdivisionModule_createNewSD'])) {
  $new_sd_form = $_POST['new_sd'] ? trim($_POST['new_sd']) : null;
  //Sanitize the variables, prevents xss, etc.
  $update_new_sd       = htmlspecialchars($new_sd_form);
  $sql2 = $pdo->prepare( "INSERT INTO `sub_divisions` (name) VALUES ('$update_new_sd')" );
  $sql2->execute();
  sleep(3);
  logAction('(STAFF) Created New Sub Division', $user_username);
  header('Location: staff.php?module=updated');
  exit();
}

if ($_GET['deletechar']) {
  $char_id = strip_tags($_GET['deletechar']);
  $stmt = $pdo->prepare( "DELETE FROM characters WHERE character_id =:char_id" );
  $stmt->bindParam(':char_id', $char_id);
  $stmt->execute();
  logAction('(STAFF) Deleted Character ('. $char_id .')', $user_username);
  header('Location: staff.php?char=deleted');
  exit();
}

if (isset($_POST['deleteId'])) {
    //Pull the variables from the form
    $identity_id_form = !empty($_POST['identity_id_form']) ? trim($_POST['identity_id_form']) : null;
    $identifier_form = !empty($_POST['identifier_form']) ? trim($_POST['identifier_form']) : null;
    $leo_supervisor_form = !empty($_POST['leo_supervisor_form']) ? trim($_POST['leo_supervisor_form']) : null;
    //Sanitize the variables, prevents xss, etc.
    $identity_id_update        = strip_tags($identity_id_form);
    $identifier_update        = strip_tags($identifier_form);
    $leo_supervisor_update        = strip_tags($leo_supervisor_form);
    //if everything passes, than continue
    $stmt = $pdo->prepare( "DELETE FROM identities WHERE identity_id =:identity_id" );
    $stmt->bindParam(':identity_id', $identity_id_update);
    $result = $stmt->execute();
    logAction('(STAFF) Deleted Identity ('. $identifier_update .')', $user_username);
    if ($result) {
        //redirect
        header('Location: ' . $url['staff_index'] . '?id=deleted');
        exit();
    }
}
if (isset($_POST['editId'])) {
    //Pull the variables from the form
    $identity_id_form = $_POST['identity_id_form'] ? trim($_POST['identity_id_form']) : null;
    $identifier_form = $_POST['identifier_form'] ? trim($_POST['identifier_form']) : null;
    $leo_supervisor_form = $_POST['leo_supervisor_form'] ? trim($_POST['leo_supervisor_form']) : null;
    $is_dispatch_form = $_POST['is_dispatch_form'] ? trim($_POST['is_dispatch_form']) : null;
    $fire_supervisor_form = $_POST['fire_supervisor_form'] ? trim($_POST['fire_supervisor_form']) : null;
    $is_fire_form = $_POST['is_fire_form'] ? trim($_POST['is_fire_form']) : null;
    $status_form = $_POST['status_form'] ? trim($_POST['status_form']) : null;
    //Sanitize the variables, prevents xss, etc.
    $identity_id_update        = strip_tags($identity_id_form);
    $identifier_update        = strip_tags($identifier_form);
    $leo_supervisor_update        = strip_tags($leo_supervisor_form);
    $is_dispatch_update        = strip_tags($is_dispatch_form);
    $fire_supervisor_update        = strip_tags($fire_supervisor_form);
    $is_fire_update        = strip_tags($is_fire_form);
    $status_update        = strip_tags($status_form);
    //if everything passes, than continue
    $sql     = "UPDATE `identities` SET `identifier`=:identifier, `leo_supervisor`=:leo_supervisor, `status`=:status, `is_dispatch`=:is_dispatch, `is_fire`=:is_fire, `fire_supervisor`=:fire_supervisor WHERE identity_id=:identity_id";
    $stmt    = $pdo->prepare($sql);
    $stmt->bindParam(':identity_id', $identity_id_update);
    $stmt->bindParam(':leo_supervisor', $leo_supervisor_update);
    $stmt->bindParam(':identifier', $identifier_update);
    $stmt->bindParam(':is_dispatch', $is_dispatch_update);
    $stmt->bindParam(':is_fire', $is_fire_update);
    $stmt->bindParam(':fire_supervisor', $fire_supervisor_update);
    $stmt->bindParam(':status', $status_update);
    $updateId = $stmt->execute();
    logAction('(STAFF) Updated Identity ('. $identifier_update .')', $user_username);
    if ($updateId) {
      header('Location: ' . $url['staff_index'] . '?id=edited');
      exit();
    }
}

if (isset($_POST['UpdateCommunityNameBtn'])) {
    //Pull the variables from the form
    $updateCommunityName_form = !empty($_POST['updateCommunityName']) ? trim($_POST['updateCommunityName']) : null;

    //Sanitize the variables, prevents xss, etc.
    $updateCommunityName        = strip_tags($updateCommunityName_form);

    $sql     = "UPDATE `settings` SET `site_name`='$updateCommunityName'";
    $stmt    = $pdo->prepare($sql);
    $updateSiteName = $stmt->execute();
    if ($updateSiteName) {
      logAction('(STAFF) Updated Community Name', $user_username);
      $message='<div class="alert alert-success" id="dismiss">Community Name Updated</div>';
    }
}

if (isset($_GET['user']) && strip_tags($_GET['user']) === 'edited') {
   $message = '<div class="alert alert-success" role="alert" id="dismiss">User Edited!</div>';
} elseif (isset($_GET['id']) && strip_tags($_GET['id']) === 'deleted') {
	$message = '<div class="alert alert-danger" role="alert" id="dismiss">Identity Deleted!</div>';
} elseif (isset($_GET['id']) && strip_tags($_GET['id']) === 'edited') {
	$message = '<div class="alert alert-success" role="alert" id="dismiss">Identity Edited!</div>';
} elseif (isset($_GET['char']) && strip_tags($_GET['char']) === 'deleted') {
	$message = '<div class="alert alert-danger" role="alert" id="dismiss">Deleted Character!</div>';
} elseif (isset($_GET['np']) && strip_tags($_GET['np']) === 'rootuser') {
	$message = '<div class="alert alert-info" role="alert" id="dismiss">You Can Not Edit This User.</div>';
} elseif (isset($_GET['module']) && strip_tags($_GET['module']) === 'installed') {
	$message = '<div class="alert alert-info" role="alert" id="dismiss">Module Installed!</div>';
} elseif (isset($_GET['module']) && strip_tags($_GET['module']) === 'uninstalled') {
	$message = '<div class="alert alert-info" role="alert" id="dismiss">Module Uninstalled!</div>';
} elseif (isset($_GET['module']) && strip_tags($_GET['module']) === 'updated') {
	$message = '<div class="alert alert-info" role="alert" id="dismiss">Module Settings Updated!</div>';
} elseif (isset($_GET['user']) && strip_tags($_GET['user']) === 'deleted') {
	$message = '<div class="alert alert-info" role="alert" id="dismiss">User Deleted From System!</div>';
}
?>
<!DOCTYPE html>
<html>
<?php
$page_name = "Staff Home";
include('includes/header.php')
?>
<body>
   <div class="container-staff">
      <div class="main-staff">
         <a href="<?php echo $url['index'] ?>"><img src="assets/imgs/los_santos.png" class="main-logo" draggable="false"/></a>
         <div class="main-header-staff">
           <div class="center">
            Hello, <?php echo $user_username ?> <?php if (staff_access) {
              echo '<a href="staff.php"><i class="fas fa-cog"></i></a>';
            } ?>
          </div>
          <?php if ($data_hac !== "") {
            echo '<div class="alert alert-info" role="alert" id="dismiss">Alert From Hydrid Staff: '. $data_hac .'</div>';
          } ?>
          <?php if (isOutdated): ?>
            <div class="alert alert-danger">Hydrid is Outdated! The latest version is <strong><?php echo $data_vc; ?></strong>. You are currently on <strong><?php echo $version; ?></strong>. You can download the latest version from <a href="https://github.com/HydridSystems/Hydrid-CAD-MDT">GitHub</a></div>
          <?php endif; ?>
         </div>
         <?php print($message); ?>
         <?php if (staff_siteSettings): ?>
           <div class="row">
             <div class="col">
               <div class="form-group">
                 <label for="IdentityVerification">LEO Verification</label>
                 <select class="form-control" id="IdentityVerification" onchange="setIdentityVerification(this.value)">
                   <option selected="true" disabled="disabled"><?php if ($siteSettings['leo_validation'] === "no") {
                     echo 'No';
                   } else {
                     echo 'Yes';
                   } ?></option>
                   <option value="no">No</option>
                   <option value="yes">Yes</option>
                 </select>
               </div>
             </div>
             <div class="col">
               <div class="form-group">
                 <label for="SignUpVerification">Sign Up Verification</label>
                 <select class="form-control" id="SignUpVerification" onchange="setSignUpVerification(this.value)">
                   <option selected="true" disabled="disabled"><?php if ($siteSettings['join_validation'] === "no") {
                     echo 'No';
                   } else {
                     echo 'Yes';
                   } ?></option>
                   <option value="no">No</option>
                   <option value="yes">Yes</option>
                 </select>
               </div>
             </div>
             <div class="col">
               <div class="form-group">
                 <label for="theme">Theme</label>
                 <select class="form-control" id="theme" onchange="setTheme(this.value)">
                   <option selected="true" disabled="disabled"><?php if ($siteSettings['theme'] === "lux") {
                     echo 'default';
                   } else {
                     echo $siteSettings['theme'];
                   }?></option>
                   <option value="cerulean">cerulean</option>
                   <option value="cosmo">cosmo</option>
                   <option value="flat">flat</option>
                   <option value="journal">journal</option>
                   <option value="litera">litera</option>
                   <option value="lumen">lumen</option>
                   <option value="lux">default</option>
                   <option value="minty">minty</option>
                   <option value="pulse">pulse</option>
                   <option value="pulsev2">pulsev2</option>
                   <option value="sandstone">sandstone</option>
                   <option value="simplex">simplex</option>
                   <option value="yeti">yeti</option>
                 </select>
               </div>
             </div>
             <div class="col">
               <div class="form-group">
                 <label for="background_color">Background Color</label>
                 <select class="form-control" id="background_color" onchange="setBackground(this.value)">
                   <option selected="true" disabled="disabled"><?php echo $siteSettings['background']; ?></option>
                   <option value="default">Default</option>
                   <option value="darkred">Dark Red</option>
                   <option value="red">Red</option>
                   <option value="darkgreen">Dark Green</option>
                   <option value="green">Green</option>
                   <option value="darkblue">Dark Blue</option>
                   <option value="blue">Blue</option>
                   <option value="redblue">Red > Blue</option>
                   <option value="yellow">Yellow</option>
                   <option value="black">Black</option>
                 </select>
               </div>
             </div>
             <div class="col">
               <div class="form-group">
                 <label for="timezone">TimeZone</label>
                 <select class="form-control" id="timezone" onchange="setTimezone(this.value)">
                   <option selected="true" disabled="disabled"><?php echo $siteSettings['timezone']; ?></option>
                   <option value="Pacific/Midway">(GMT-11:00) Midway Island, Samoa</option>
                   <option value="America/Adak">(GMT-10:00) Hawaii-Aleutian</option>
                   <option value="Etc/GMT+10">(GMT-10:00) Hawaii</option>
                   <option value="Pacific/Marquesas">(GMT-09:30) Marquesas Islands</option>
                   <option value="Pacific/Gambier">(GMT-09:00) Gambier Islands</option>
                   <option value="America/Anchorage">(GMT-09:00) Alaska</option>
                   <option value="America/Ensenada">(GMT-08:00) Tijuana, Baja California</option>
                   <option value="Etc/GMT+8">(GMT-08:00) Pitcairn Islands</option>
                   <option value="America/Los_Angeles">(GMT-08:00) Pacific Time (US & Canada)</option>
                   <option value="America/Denver">(GMT-07:00) Mountain Time (US & Canada)</option>
                   <option value="America/Chihuahua">(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
                   <option value="America/Dawson_Creek">(GMT-07:00) Arizona</option>
                   <option value="America/Belize">(GMT-06:00) Saskatchewan, Central America</option>
                   <option value="America/Cancun">(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
                   <option value="Chile/EasterIsland">(GMT-06:00) Easter Island</option>
                   <option value="America/Chicago">(GMT-06:00) Central Time (US & Canada)</option>
                   <option value="America/New_York">(GMT-05:00) Eastern Time (US & Canada)</option>
                   <option value="America/Havana">(GMT-05:00) Cuba</option>
                   <option value="America/Bogota">(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
                   <option value="America/Caracas">(GMT-04:30) Caracas</option>
                   <option value="America/Santiago">(GMT-04:00) Santiago</option>
                   <option value="America/La_Paz">(GMT-04:00) La Paz</option>
                   <option value="Atlantic/Stanley">(GMT-04:00) Faukland Islands</option>
                   <option value="America/Campo_Grande">(GMT-04:00) Brazil</option>
                   <option value="America/Goose_Bay">(GMT-04:00) Atlantic Time (Goose Bay)</option>
                   <option value="America/Glace_Bay">(GMT-04:00) Atlantic Time (Canada)</option>
                   <option value="America/St_Johns">(GMT-03:30) Newfoundland</option>
                   <option value="America/Araguaina">(GMT-03:00) UTC-3</option>
                   <option value="America/Montevideo">(GMT-03:00) Montevideo</option>
                   <option value="America/Miquelon">(GMT-03:00) Miquelon, St. Pierre</option>
                   <option value="America/Godthab">(GMT-03:00) Greenland</option>
                   <option value="America/Argentina/Buenos_Aires">(GMT-03:00) Buenos Aires</option>
                   <option value="America/Sao_Paulo">(GMT-03:00) Brasilia</option>
                   <option value="America/Noronha">(GMT-02:00) Mid-Atlantic</option>
                   <option value="Atlantic/Cape_Verde">(GMT-01:00) Cape Verde Is.</option>
                   <option value="Atlantic/Azores">(GMT-01:00) Azores</option>
                   <option value="Europe/Belfast">(GMT) Greenwich Mean Time : Belfast</option>
                   <option value="Europe/Dublin">(GMT) Greenwich Mean Time : Dublin</option>
                   <option value="Europe/Lisbon">(GMT) Greenwich Mean Time : Lisbon</option>
                   <option value="Europe/London">(GMT) Greenwich Mean Time : London</option>
                   <option value="Africa/Abidjan">(GMT) Monrovia, Reykjavik</option>
                   <option value="Europe/Amsterdam">(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
                   <option value="Europe/Belgrade">(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
                   <option value="Europe/Brussels">(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
                   <option value="Africa/Algiers">(GMT+01:00) West Central Africa</option>
                   <option value="Africa/Windhoek">(GMT+01:00) Windhoek</option>
                   <option value="Asia/Beirut">(GMT+02:00) Beirut</option>
                   <option value="Africa/Cairo">(GMT+02:00) Cairo</option>
                   <option value="Asia/Gaza">(GMT+02:00) Gaza</option>
                   <option value="Africa/Blantyre">(GMT+02:00) Harare, Pretoria</option>
                   <option value="Asia/Jerusalem">(GMT+02:00) Jerusalem</option>
                   <option value="Europe/Minsk">(GMT+02:00) Minsk</option>
                   <option value="Asia/Damascus">(GMT+02:00) Syria</option>
                   <option value="Europe/Moscow">(GMT+03:00) Moscow, St. Petersburg, Volgograd</option>
                   <option value="Africa/Addis_Ababa">(GMT+03:00) Nairobi</option>
                   <option value="Asia/Tehran">(GMT+03:30) Tehran</option>
                   <option value="Asia/Dubai">(GMT+04:00) Abu Dhabi, Muscat</option>
                   <option value="Asia/Yerevan">(GMT+04:00) Yerevan</option>
                   <option value="Asia/Kabul">(GMT+04:30) Kabul</option>
                   <option value="Asia/Yekaterinburg">(GMT+05:00) Ekaterinburg</option>
                   <option value="Asia/Tashkent">(GMT+05:00) Tashkent</option>
                   <option value="Asia/Kolkata">(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
                   <option value="Asia/Katmandu">(GMT+05:45) Kathmandu</option>
                   <option value="Asia/Dhaka">(GMT+06:00) Astana, Dhaka</option>
                   <option value="Asia/Novosibirsk">(GMT+06:00) Novosibirsk</option>
                   <option value="Asia/Rangoon">(GMT+06:30) Yangon (Rangoon)</option>
                   <option value="Asia/Bangkok">(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
                   <option value="Asia/Krasnoyarsk">(GMT+07:00) Krasnoyarsk</option>
                   <option value="Asia/Hong_Kong">(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
                   <option value="Asia/Irkutsk">(GMT+08:00) Irkutsk, Ulaan Bataar</option>
                   <option value="Australia/Perth">(GMT+08:00) Perth</option>
                   <option value="Australia/Eucla">(GMT+08:45) Eucla</option>
                   <option value="Asia/Tokyo">(GMT+09:00) Osaka, Sapporo, Tokyo</option>
                   <option value="Asia/Seoul">(GMT+09:00) Seoul</option>
                   <option value="Asia/Yakutsk">(GMT+09:00) Yakutsk</option>
                   <option value="Australia/Adelaide">(GMT+09:30) Adelaide</option>
                   <option value="Australia/Darwin">(GMT+09:30) Darwin</option>
                   <option value="Australia/Brisbane">(GMT+10:00) Brisbane</option>
                   <option value="Australia/Hobart">(GMT+10:00) Hobart</option>
                   <option value="Asia/Vladivostok">(GMT+10:00) Vladivostok</option>
                   <option value="Australia/Lord_Howe">(GMT+10:30) Lord Howe Island</option>
                   <option value="Etc/GMT-11">(GMT+11:00) Solomon Is., New Caledonia</option>
                   <option value="Asia/Magadan">(GMT+11:00) Magadan</option>
                   <option value="Pacific/Norfolk">(GMT+11:30) Norfolk Island</option>
                   <option value="Asia/Anadyr">(GMT+12:00) Anadyr, Kamchatka</option>
                   <option value="Pacific/Auckland">(GMT+12:00) Auckland, Wellington</option>
                   <option value="Etc/GMT-12">(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
                   <option value="Pacific/Chatham">(GMT+12:45) Chatham Islands</option>
                   <option value="Pacific/Tongatapu">(GMT+13:00) Nuku'alofa</option>
                   <option value="Pacific/Kiritimati">(GMT+14:00) Kiritimati</option>
                 </select>
               </div>
             </div>
           </div>
           <div class="row">
             <div class="col">
               <div class="form-group">
                 <label for="modules">Modules</label>
                 <div class="input-group mb-3">
                   <button class="btn btn-danger btn-block" name="UpdateCommunityNameBtn" id="UpdateCommunityNameBtn" type="button" data-toggle="modal" data-target="#ModuleModal">Module Settings</button>
                 </div>
               </div>
             </div>
             <div class="col">
               <div class="form-group">
                 <form method="post" action="staff.php">
                 <label for="theme">Community Name</label>
                 <div class="input-group mb-3">
                   <input type="text" class="form-control" name="updateCommunityName" placeholder="<?php echo $siteSettings['name'] ?>" aria-label="Community Name" aria-describedby="basic-addon2" required>
                   <div class="input-group-append">
                     <button class="btn btn-success" name="UpdateCommunityNameBtn" id="UpdateCommunityNameBtn" type="submit" type="button">Update</button>
                   </div>
                 </div>
               </form>
               </div>
             </div>
             <div class="col">
               <label>Please report any bugs to our <a href="https://discord.gg/KhQMeuA">Discord</a></label>
             </div>
           </div>
         <?php endif; ?>
         <div class="row">
           <div class="col">
             <label for="users">Users</label><br>
             <input type="text" class="form-control" id="userSearch" onkeyup="searchUsers()" placeholder="Search by Username or Usergroup">
             <div style="background-color:white; color:black; overflow-y: scroll; height:300px;">
             <table id="users" style="background-color:white; color:black;">
               <tr>
                 <th><center>User ID</center></th>
                 <th><center>Username</center></th>
                 <th><center>Usergroup</center></th>
                 <th><center>Joined On</center></th>
                 <?php if (discordModule_isInstalled): ?>
                   <th><center>Discord</center></th>
                 <?php endif; ?>
                 <?php if (staff_editUsers): ?>
                   <th><center></center></th>
                 <?php endif; ?>
               </tr>
               <?php
               $getUsers = "SELECT * FROM users";
               $result = $pdo->prepare($getUsers);
               $result->execute();
               while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                 echo "<tr>";
                 echo "<td><center>" . $row['user_id'] . "</center></td>";
                 echo "<td><center>" . $row['username'] . "</center></td>";
                 echo "<td><center>" . $row['usergroup'] . "</center></td>";
                 echo "<td><center>" . $row['join_date'] . "</center></td>";
                 if (discordModule_isInstalled) {
                   echo "<td><center>" . $row['discord'] . "</center></td>";
                 }
                 if (staff_editUsers) {
                   echo '<td><a class="btn btn-info btn-sm" href="staff-edituser.php?user=' . $row['user_id'] . '" data-title="Edit user"><i class="fas fa-pencil-alt"></i></a></td>';
                 }
                 echo "</tr>";
               }
               ?>
             </table>
           </div>
           </div>
           <div class="col">
             <label for="characters">Characters</label>
             <input type="text" class="form-control" id="characterSearch" onkeyup="searchCharacters()" placeholder="Search by Character Name...">
             <div style="background-color:white; color:black; overflow-y: scroll; height:300px;">
             <table id="characters" style="background-color:white; color:black;">
               <tr>
                 <th><center>Character ID</center></th>
                 <th><center>Character Name</center></th>
                 <th><center>Character Owner</center></th>
                 <?php if (staff_editUsers): ?>
                   <th><center></center></th>
                 <?php endif; ?>
               </tr>
               <?php
               $getUsers = "SELECT * FROM characters";
               $result = $pdo->prepare($getUsers);
               $result->execute();
               while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                 echo "<tr>";
                 echo "<td><center>" . $row['character_id'] . "</center></td>";
                 echo "<td><center>" . $row['first_name'] . ' ' . $row['last_name'] . "</center></td>";
                 echo "<td><center>" . $row['owner_name'] . "</center></td>";
                 if (staff_editUsers) {
                   echo '<td><a class="btn btn-danger btn-sm" href="staff.php?deletechar='.$row['character_id'].'">Delete</a></td>';
                 }
                 echo "</tr>";
               }
               ?>
             </table>
           </div>
           </div>
         </div>
         <div class="row">
         <div class="col">
           <label for="characters">Identities</label>
           <input type="text" class="form-control" id="identitySearch" onkeyup="searchIdentities()" placeholder="Search for Identities...">
           <div style="background-color:white; color:black; overflow-y: scroll; height:300px;">
           <table id="identities" style="background-color:white; color:black;">
             <tr>
               <th><center>Identifier</center></th>
               <th><center>Status</center></th>
               <th><center>LEO Supv.</center></th>
               <th><center>Dispatch</center></th>
               <th><center>Fire/EMS</center></th>
               <th><center>Fire Supv.</center></th>
               <th><center>User</center></th>
               <?php if (staff_editUsers): ?>
                 <th><center></center></th>
               <?php endif; ?>
             </tr>
             <?php
             $getIds = "SELECT * FROM identities";
             $result = $pdo->prepare($getIds);
             $result->execute();
             while($row = $result->fetch(PDO::FETCH_ASSOC)) {
               echo "<tr>";
               echo "<td><center>" . $row['identifier'] . "</center></td>";
               echo "<td><center>" . $row['status'] . "</center></td>";
               echo "<td><center>" . $row['leo_supervisor'] . "</center></td>";
               echo "<td><center>" . $row['is_dispatch'] . "</center></td>";
               echo "<td><center>" . $row['is_fire'] . "</center></td>";
               echo "<td><center>" . $row['fire_supervisor'] . "</center></td>";
               echo "<td><center>" . $row['user_name'] . "</center></td>";
               echo '<td><a class="btn btn-info btn-sm" href="" data-toggle="modal" data-target="#editIdentity'.$row['identity_id'].'"><i class="fas fa-pencil-alt"></i></a></td>';
               echo "</tr>";

               echo '
               <div class="modal fade" id="editIdentity'.$row['identity_id'].'" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                     <div class="modal-content">
                        <div class="modal-header">
                           <h5 class="modal-title" id="exampleModalLabel">Editing '.$row['identifier'].'</h5>
                           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                           </button>
                        </div>
                        <div class="modal-body">
                          <form method="post" action="'.$url['staff_index'].'">
                          <input type="hidden" value="'.$row['identity_id'].'" name="identity_id_form">
                          <div class="form-group">
                             <label style="color:black;">Identifier</label>
                             <input type="text" name="identifier_form" class="form-control" placeholder="Identifier" value="'.$row['identifier'].'" data-lpignore="true" required />
                          </div>
                          <div class="form-group">
                          <label style="color:black;">Status</label>
                             <select class="form-control" name="status_form">
                                <option value="'.$row['status'].'" selected="true">'.$row['status'].'</option>
                                <option value="Active">Active</option>
                                <option value="Approval Needed">Approval Needed</option>
                             </select>
                          </div>
                          <div class="form-group">
                          <label style="color:black;">LEO Supervisor</label>
                             <select class="form-control" name="leo_supervisor_form">
                                <option value="'.$row['leo_supervisor'].'" selected="true">'.$row['leo_supervisor'].'</option>
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                             </select>
                          </div>
                          <div class="form-group">
                          <label style="color:black;">Fire Supervisor</label>
                             <select class="form-control" name="fire_supervisor_form">
                                <option value="'.$row['fire_supervisor'].'" selected="true">'.$row['fire_supervisor'].'</option>
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                             </select>
                          </div>
                          <div class="form-group">
                          <label style="color:black;">Dispatch Approved</label>
                             <select class="form-control" name="is_dispatch_form">
                                <option value="'.$row['is_dispatch'].'" selected>'.$row['is_dispatch'].'</option>
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                             </select>
                          </div>
                          <div class="form-group">
                          <label style="color:black;">Fire Approved</label>
                             <select class="form-control" name="is_fire_form">
                                <option value="'.$row['is_fire'].'" selected>'.$row['is_fire'].'</option>
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                             </select>
                          </div>
                        </div>
                        <div class="modal-footer">
                        <div class="form-group">
                           <input class="btn btn-danger" name="deleteId" id="deleteId" type="submit" value="Delete">
                        </div>
                        <div class="form-group">
                           <input class="btn btn-primary" name="editId" id="editId" type="submit" value="Edit">
                        </div>
                        </div>
                        </form>
                     </div>
                  </div>
               </div>
               ';
             }
             ?>
           </table>
         </div>
         </div>
         <div class="col">
           <label for="companies">Companies</label>
           <input type="text" class="form-control" id="companySearch" onkeyup="searchCompanies()" readonly="" placeholder="Search by Company Name...">
           <div style="background-color:white; color:black; overflow-y: scroll; height:300px;">
           <table id="companies" style="background-color:white; color:black;">
             <tr>
               <th><center>Company ID</center></th>
               <th><center>Company Name</center></th>
               <th><center>Company Owner</center></th>
             </tr>
             <tr>
             <td><center>NaN</center></td>
             <td><center>NaN</center></td>
             <td><center>NaN</center></td>
             </tr>
           </table>
         </div>
         </div>
       </div>
       <?php if (staff_editUsers): ?>
       <div class="row">
         <div class="col">
           <label for="leoLogs">Logs</label><br>
           <input type="text" class="form-control" id="leoLogs" onkeyup="searchleoLogs()" placeholder="Search by Action...">
           <div style="background-color:white; color:black; overflow-y: scroll; height:300px;">
           <table id="users" style="background-color:white; color:black;">
             <tr>
               <th><center>Log ID</center></th>
               <th><center>Action</center></th>
               <th><center>User</center></th>
               <th><center>Timestamp</center></th>
             </tr>
             <?php
             $getLeoLogs = "SELECT * FROM logs";
             $result = $pdo->prepare($getLeoLogs);
             $result->execute();
             while($row = $result->fetch(PDO::FETCH_ASSOC)) {
               echo "<tr>";
               echo "<td><center>" . $row['log_id'] . "</center></td>";
               echo "<td><center>" . $row['action'] . "</center></td>";
               echo "<td><center>" . $row['username'] . "</center></td>";
               echo "<td><center>" . $row['timestamp'] . "</center></td>";
               echo "</tr>";
             }
             ?>
           </table>
         </div>
         </div>
       </div>
       <?php endif; ?>
         <?php echo $ftter; ?>
      </div>
   </div>

   <div class="modal fade" id="ModuleModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Module Management</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
              <table style="width:100%">
                <tr>
                  <th>Name</th>
                  <th>Description</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
                <tr>
                  <td>Discord</td>
                  <td>Requires Discord#Tag to be entered when signing up.</td>
                  <td><?php if (discordModule_isInstalled): ?>
                    Installed
                    <?php else: ?>
                      Not Installed
                  <?php endif; ?></td>

                  <td>
                    <form method="post" action="staff.php">
                      <?php if (!discordModule_isInstalled): ?>
                        <div class="form-group">
                           <input class="btn btn-success btn-sm" name="discordModule_install" type="submit" value="Install">
                        </div>
                      <?php else: ?>
                          <div class="form-group">
                             <input class="btn btn-danger btn-sm" name="discordModule_uninstall" type="submit" value="Uninstall">
                          </div>
                      <?php endif; ?>
                    </form>
                  </td>
                </tr>

                <tr>
                  <td>LiveMap Module</td>
                  <td>Displays a MAP button in LEO Module. (MUST HAVE LiveMap SETUP).</td>
                  <td><?php if (mapModule_isInstalled): ?>
                    Installed
                    <?php else: ?>
                      Not Installed
                  <?php endif; ?></td>
                  <td>
                    <form method="post" action="staff.php">
                      <?php if (!mapModule_isInstalled): ?>
                        <div class="form-group">
                           <input class="btn btn-success btn-sm" name="mapModule_install" type="submit" value="Install">
                        </div>
                      <?php else: ?>
                          <div class="form-group">
                             <input class="btn btn-danger btn-sm" name="mapModule_uninstall" type="submit" value="Uninstall">
                             <a data-toggle="modal" href="#mapModule_settings" data-dismiss="modal" class="btn btn-primary btn-sm">Settings</a>
                          </div>
                      <?php endif; ?>
                    </form>
                  </td>
                </tr>

                <tr>
                  <td>Sub Division Module</td>
                  <td>Allows Sub Division Selection In LEO Module</td>
                  <td><?php if (subdivisionModule_isInstalled): ?>
                    Installed
                    <?php else: ?>
                      Not Installed
                  <?php endif; ?></td>
                  <td>
                    <form method="post" action="staff.php">
                      <?php if (!subdivisionModule_isInstalled): ?>
                        <div class="form-group">
                           <input class="btn btn-success btn-sm" name="subdivisionModule_install" type="submit" value="Install">
                        </div>
                      <?php else: ?>
                          <div class="form-group">
                             <input class="btn btn-danger btn-sm" name="subdivisionModule_uninstall" type="submit" value="Uninstall">
                             <a data-toggle="modal" href="#subdivisionModule_settings" data-dismiss="modal" class="btn btn-primary btn-sm">Settings</a>
                          </div>
                      <?php endif; ?>
                    </form>
                  </td>
                </tr>
              </table>
            </div>
         </div>
      </div>
   </div>

   <div class="modal fade" id="mapModule_settings" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Module Management</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
                    <form method="post" action="staff.php">
                      <div class="form-group">
                         <input type="text" name="mapModule_link" class="form-control" placeholder="https://map.example.com" data-lpignore="true" required />
                      </div>
            </div>
            <div class="modal-footer">
            <div class="form-group">
               <input class="btn btn-primary" name="mapModule_updateSettings" id="mapModule_updateSettings" type="submit" value="Update Settings">
            </div>
            </form>
         </div>
      </div>
   </div>
  </div>

   <div class="modal fade" id="custom10codesModule_settings" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Module Management</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
                    <form method="post" action="staff.php">
                      <div class="form-group">
                         <input type="text" name="10_6_btn" class="form-control" placeholder="10-6" data-lpignore="true" required />
                      </div>
                      <div class="form-group">
                         <input type="text" name="10_7_btn" class="form-control" placeholder="10-7" data-lpignore="true" required />
                      </div>
                      <div class="form-group">
                         <input type="text" name="10_8_btn" class="form-control" placeholder="10-8" data-lpignore="true" required />
                      </div>
                      <div class="form-group">
                         <input type="text" name="10_15_btn" class="form-control" placeholder="10-15" data-lpignore="true" required />
                      </div>
                      <div class="form-group">
                         <input type="text" name="10_23_btn" class="form-control" placeholder="10-23" data-lpignore="true" required />
                      </div>
                      <div class="form-group">
                         <input type="text" name="10_97_btn" class="form-control" placeholder="10-97" data-lpignore="true" required />
                      </div>
            </div>
            <div class="modal-footer">
            <div class="form-group">
               <input class="btn btn-primary" name="custom10codesModule_updateSettings" id="custom10codesModule_updateSettings" type="submit" value="Update Settings">
            </div>
            </form>
         </div>
      </div>
   </div>
 </div>
<?php if (subdivisionModule_isInstalled): ?>
 <div class="modal fade" id="subdivisionModule_settings" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
       <div class="modal-content">
          <div class="modal-header">
             <h5 class="modal-title" id="exampleModalLabel">Module Management</h5>
             <button type="button" class="close" data-dismiss="modal" aria-label="Close">
             <span aria-hidden="true">&times;</span>
             </button>
          </div>
          <div class="modal-body">
            <?php
            echo "
        		<table>
            <tr>
            <th><center>Name</center></th>
            <th><center></center></th>
            </tr>";
        		$getSubDiv = "SELECT * FROM sub_divisions";
        		$result = $pdo->prepare($getSubDiv);
        		$result->execute();
        		while ($row = $result->fetch(PDO::FETCH_ASSOC))
        			{
        			echo "<tr>";
        			echo "<td><center>" . $row['name'] . "</center></td>";
        			echo '<td><a class="btn btn-danger btn-sm" href="functions/staff/setTheme.php?a=deleteSubDivision&id=' . $row['id'] . '" data-title="Delete">Delete</a></td>';
        			echo "</tr>";
        			}

        		echo "</table>";
            echo '<a data-toggle="modal" href="#subdivisionModule_createNew" data-dismiss="modal" class="btn btn-primary btn-sm">Create New Sub Division</a>';
             ?>
          </div>
          <div class="modal-footer">
       </div>
    </div>
 </div>
</div>

<div class="modal fade" id="subdivisionModule_createNew" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-md modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Module Management</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
                 <form method="post" action="staff.php">
                   <div class="form-group">
                      <input type="text" name="new_sd" class="form-control" placeholder="Sub Division Name..." data-lpignore="true" required />
                   </div>
         </div>
         <div class="modal-footer">
         <div class="form-group">
            <input class="btn btn-primary" name="subdivisionModule_createNewSD" id="subdivisionModule_createNewSD" type="submit" value="Update Settings">
         </div>
         </form>
      </div>
   </div>
</div>
</div>
<?php endif; ?>
   <!-- javascript -->
   <?php include('includes/js.php'); ?>
   <script src="assets/js/pages/staff.js"></script>
   <!-- end javascript -->
</body>
</html>
