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
require 'classes/lib/password.php';

if (isset($_POST['registerbtn'])) {
    //Pull the variables from the form
    $username = !empty($_POST['username']) ? trim($_POST['username']) : null;
    $pass       = !empty($_POST['password']) ? trim($_POST['password']) : null;
    $discord       = !empty($_POST['discord']) ? trim($_POST['discord']) : null;
    //Sanitize the variables, prevents xss, etc.
    $username        = strip_tags($username);
    $pass            = strip_tags($pass);
    $discord            = strip_tags($discord);

    userRegister($username, $pass, $discord);
}

//Error Messages
if (isset($_GET['password']) && strip_tags($_GET['password']) === 'short') {
   $message = '<div class="alert alert-danger" role="alert"><strong>ERROR:</strong> Please use a longer password.</div>';
} elseif (isset($_GET['password']) && strip_tags($_GET['password']) === 'long') {
  $message = '<div class="alert alert-danger" role="alert"><strong>ERROR:</strong> Please use a shorter password.</div>';
} elseif (isset($_GET['username']) && strip_tags($_GET['username']) === 'long') {
  $message = '<div class="alert alert-danger" role="alert"><strong>ERROR:</strong> Please use a shorter username.</div>';
} elseif (isset($_GET['email']) && strip_tags($_GET['email']) === 'taken') {
  $message = '<div class="alert alert-danger" role="alert"><strong>ERROR:</strong> That email is already in-use.</div>';
} elseif (isset($_GET['email']) && strip_tags($_GET['email']) === 'taken') {
  $message = '<div class="alert alert-danger" role="alert"><strong>ERROR:</strong> That username is already in-use.</div>';
}
?>
<!DOCTYPE html>
<html>
<?php
$page_name = "Register";
include('includes/header.php')
?>
   <body>
      <div class="container">
         <div class="main">
            <img src="assets/imgs/los_santos.png" class="main-logo" draggable="false"/>
            <div class="main-header">
               Please Register To Continue
            </div>
            <?php print($message); ?>
            <form method="post" action="register.php">
               <div class="row">
                  <div class="col">
                     <div class="form-group">
                        <input type="text" name="username" class="form-control" maxlength="36" placeholder="Username" title="This must be the name you use on discord." data-lpignore="true" required />
                     </div>
                  </div>
               </div>
               <?php if (discordModule_isInstalled): ?>
                 <div class="form-group">
                    <input type="text" name="discord" class="form-control" placeholder="Discord#Tag" data-lpignore="true" required />
                 </div>
               <?php endif; ?>
               <div class="form-group">
                  <input type="password" name="password" class="form-control" placeholder="Password" title="Please do not use a common password." data-lpignore="true" required />
               </div>
               <div class="form-group">
                  <input class="btn btn-block btn-primary" name="registerbtn" id="registerbtn" type="submit" value="Finish Signup">
               </div>
               <text>Already have an account? <a href="<?php print($url['login']) ?>">Login</a></text>
               <?php echo $ftter; ?>
            </form>
         </div>
      </div>
      <?php include('includes/js.php'); ?>
   </body>
</html>
