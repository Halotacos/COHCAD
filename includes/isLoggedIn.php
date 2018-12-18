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

// Get User Data
$user_id = $_SESSION['user_id'];
$userRow = dbquery('SELECT * FROM users WHERE user_id=' . $user_id)[0];

//Define variables
$user_username = $userRow['username'];
$user_email = $userRow['email'];
$user_usergroup = $userRow['usergroup'];
$user_departments = $userRow['departments'];
$user_ip = $userRow['join_ip'];
$user_joindate = $userRow['join_date'];

//Module Defines
if (discordModule_isInstalled) {
  $user_discord   = $userRow['discord'];
}

// Default Values
$perms['banned'] = false;
$perms['panel_access'] = false;
$perms['staff_approveUsers'] = false;
$perms['staff_access'] = false;
$perms['staff_viewUsers'] = false;
$perms['staff_editUsers'] = false;
$perms['staff_siteSettings'] = false;

// Define User Permissions
switch($user_usergroup) {
  case "Banned":
    $perms['banned'] = true;
    break;
  case "User":
    $perms['panel_access'] = true;
    break;
  case "Moderator":
    $perms['panel_access'] = true;
    $perms['staff_approveUsers'] = true;
    $perms['staff_access'] = true;
    $perms['staff_viewUsers'] = true;
    break;
  case "Admin":
    $perms['panel_access'] = true;
    $perms['staff_approveUsers'] = true;
    $perms['staff_access'] = true;
    $perms['staff_viewUsers'] = true;
    $perms['staff_editUsers'] = true;
    break;
  case "Management":
    $perms['panel_access'] = true;
    $perms['staff_approveUsers'] = true;
    $perms['staff_access'] = true;
    $perms['staff_viewUsers'] = true;
    $perms['staff_editUsers'] = true;
    $perms['staff_siteSettings'] = true;
    break;
  case "Developer":
    $perms['panel_access'] = true;
    $perms['staff_approveUsers'] = true;
    $perms['staff_access'] = true;
    $perms['staff_viewUsers'] = true;
    $perms['staff_editUsers'] = true;
    $perms['staff_siteSettings'] = true;
    break;
}

// Define Values
define("banned", $perms['banned']);
define("panel_access", $perms['panel_access']);
define("staff_approveUsers", $perms['staff_approveUsers']);
define("staff_access", $perms['staff_access']);
define("staff_viewUsers", $perms['staff_viewUsers']);
define("staff_editUsers", $perms['staff_editUsers']);
define("staff_siteSettings", $perms['staff_siteSettings']);

// Check If Banned
if (banned) {
  session_destroy();
  header('Location: ' . $url['login'] . '?account=banned');
  exit();
}

// Check Update Status
if ($update_in_progress === "Yes") {
  if ($user_usergroup !== "Developer") {
    session_destroy();
    header('Location: ' . $url['login'] . '?update=ip');
    exit();
  }
}
