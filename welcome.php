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
session_start();
include('includes/config.php');
?>
<!DOCTYPE html>
<html>
<?php
$page_name = "Welcome";
include('includes/header.php')
?>
   <body>
      <div class="container">
         <div class="main">
            <img src="assets/imgs/california.png" class="main-logo" draggable="false"/><br />
            <text>
              <strong>Thank you for signing up with Hydrid! </strong><br />
              <?php if ($siteSettings['join_validation'] === "yes"): ?>
                This community has validation enabled, meaning a staff member will have to validate your account. Please do not pester staff about validation. After your account is validated, you will be able to login and start using Hydrid!
              <?php else: ?>
                It appears that this community does not have validation enabled for new accounts. You are free to login, and start using Hydrid.
              <?php endif; ?>
            </text>
            <a href="<?php print($url['login']) ?>"><button class="btn btn-block btn-primary" style="margin-top: 10px;">Continue to login</button></a>
            <?php echo $ftter; ?>
         </div>
      </div>
      <?php include('includes/js.php'); ?>
   </body>
</html>
