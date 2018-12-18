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

// Debug Toggle
$GLOBAL['debug'] = false;


// Version Number -- Do Not Change
$version = "v1.0.7";


// Disable Error Reporting
if(!$GLOBAL['debug']) {
  error_reporting(0);
}


// Oudated Variables/Constants?
$update_in_progress = "No";
define("isDonator", false);


// Load Plugin Loader
//require_once("classes/lib/plugins.class.php");
//plugins::start('plugins/');

// Get Global Functions
require_once("functions.php");

// Get Site Config
$settingsRow = dbquery('SELECT * FROM settings')[0];

if (empty($settingsRow)) {
  die("Database Error (2) - Contact Support");
}

//Define variables
$siteSettings['background'] = $settingsRow['background_color'];
$siteSettings['name'] = $settingsRow['site_name'];
$siteSettings['theme'] = $settingsRow['theme'];
$siteSettings['join_validation'] = $settingsRow['validation_enabled'];
$siteSettings['leo_validation'] =  $settingsRow['identity_approval_needed'];
$siteSettings['timezone'] = $settingsRow['timezone'];

//Module checks

//DISCORD MODULE
if (isset($settingsRow['discord_module'])) {
  if ($settingsRow['discord_module'] === "Enabled") {
    define("discordModule_isInstalled", true);
  }
} else {
  define("discordModule_isInstalled", false);
}

//MAP MODULE
if (isset($settingsRow['map_module'])) {
  if ($settingsRow['map_module'] === "Enabled") {
    $mapModule_link = $settingsRow['map_module_link'];
    define("mapModule_isInstalled", true);
  }
} else {
  define("mapModule_isInstalled", false);
}

//SUB DIVISION MODULE
if (isset($settingsRow['subdivision_module'])) {
  if ($settingsRow['subdivision_module'] === "Enabled") {
    define("subdivisionModule_isInstalled", true);
  } else {
    define("subdivisionModule_isInstalled", false);
  }
} else {
  define("subdivisionModule_isInstalled", false);
}

//End Module Checks

//Important Settings
$background_color = ""; //light_blue, blue, red, gold
$bootstrap_theme = $siteSettings['theme'];
$community_name = $siteSettings['name'];
$community_url = $siteSettings['theme'];
//Validation Settings
$validation_enabled = $siteSettings['join_validation'];
$identity_approval_needed = $siteSettings['leo_validation'];

// Define URLS
$url['index'] = "index.php";
$url['register'] = "register.php";
$url['welcome'] = "welcome.php";
$url['login'] = "login.php";
$url['civ_index'] = "civ-index.php";
$url['civ_view'] = "civ-view.php";
$url['civ_driverlicense'] = "civ-driverlicense.php";
$url['civ_registernewvehicle'] = "civ-registernewveh.php";
$url['civ_viewveh'] = "civ-viewveh.php";
$url['civ_firearms'] = "civ-firearms.php";
$url['civ_newwarrant'] = "civ-addwarrant.php";
$url['civ_viewwarrants'] = "civ-mywarrants.php";
$url['leo_index'] = "leo-index.php";
$url['staff_edit_user'] = "staff-edituser.php";
$url['staff_index'] = "staff.php";
$url['fire_index'] = "fire-index.php";
$url['leo_supervisor_view_pending_identities'] = "leo-pending-identities.php";
$url['leo_supervisor_view_pending_identities'] = "leo-all-identities.php";
$url['dispatch_index'] = "dispatch-index.php";
$url['staff_setup'] = "setup.php";
$url['settings'] = "user-settings.php";
$url['logout'] = "logout.php";

$message     = '';

$ip = $_SERVER['REMOTE_ADDR'];
date_default_timezone_set($siteSettings['timezone']);
$date   = date('Y-m-d');
$us_date = date_format(date_create_from_format('Y-m-d', $date), 'm/d/Y');
$time = date('h:i:s A', time());

//REMOVING ANYTHING BELOW THIS LINE WILL VOID SUPPORT.
//________________________________________________________________________________________________________________________________________________________________________________________________________________________

// Version Check/Control
$data_vc = file_get_contents("https://pastebin.com/raw/d63r81DF");

if ($data_vc > $version) {
  define('isOutdated', true);
} else {
  define('isOutdated', false);
}

//pdo check
if (!class_exists('PDO')) {
  die("Sorry, Hydrid can not be used without PDO being enabled. If you're running on a local machine, It should already be enabled. If you are running off a hosting provider, Please contact them for further assistance.");
}

//php version check
if (floatval(phpversion()) < 5.6) {
  die("Your PHP Version is not supported. Please update to continue using Hydrid.");
}

// hydrid announce check
// Version Check/Control
$data_hac = "";

//YOU ARE NOT ALLOWED TO REMOVE THIS. REMOVING THIS, REMOVING BACKLINKS, WILL RESULT IN A DMCA TAKEDOWN AS IT IS A BREACH OF OUR LICENSE (AGPL v3)
if ($data_vc > $version) {
  $ftter = '<br /><small><strong><a href="https://discord.gg/NeRrWZC" target="_BLANK">Powered by Hydrid</a></strong></small><br />
  <small>Version: '.$version.'<br />Latest Version: '.$data_vc.'<br /><small><strong><font color="red">Hydrid is Outdated. Hydrid does not provide support for Outdated versions.</font></strong></small>';
} else {
  $ftter = '<br /><small><strong><a href="https://discord.gg/NeRrWZC" target="_BLANK">Powered by Hydrid</a></strong></small><br />
  <small>Version: '.$version.'<br />Latest Version: '.$data_vc;
}
