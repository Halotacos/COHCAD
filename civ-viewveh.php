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

logAction('Viewing Character Vehicle', $user_username);
//Alerts
if (isset($_GET['license']) && strip_tags($_GET['license']) === 'invalid') {
   $message = '<div class="alert alert-danger" role="alert">It Seems Your Drivers License Is Not Valid.</div>';
} elseif (isset($_GET['vehicle']) && strip_tags($_GET['vehicle']) === 'deleted') {
   $message = '<div class="alert alert-danger" role="alert">Vehicle Deleted.</div>';
}
?>
<!DOCTYPE html>
<html>
<?php
$page_name = "Viewing Civilian";
include('includes/header.php')
?>
<script>
function showVeh(str) {
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
        xmlhttp.open("GET","functions/getveh.php?q="+str,true);
        xmlhttp.send();
    }
}
</script>

   <body>
      <div class="container">
         <div class="main">
            <a href="<?php print $url['civ_view'] ?>?id=<?php echo $_SESSION['character_id'] ?>"><img src="assets/imgs/dmv.png" class="main-logo" draggable="false"/></a>
            <div class="main-header">
               Hello, <?php echo $_SESSION['character_first_name'] ?>
            </div>
            <?php print($message); ?>
            <form>
              <select class="form-control" name="vehicles" onchange="showVeh(this.value)">
                <option selected="true" disabled="disabled">Select Vehicle</option>
                <?php
                $status = 'Enabled';
                $char_id = $_SESSION['character_id'];
                $getVeh = "SELECT * FROM vehicles WHERE vehicle_owner='$char_id' AND vehicle_status='$status'";
                $result = $pdo->prepare($getVeh);
                $result->execute();
                while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                  echo '<option value="'. $row['vehicle_id'] .'">'. $row['vehicle_model'] .' - '. $row['vehicle_plate'] .'</option>';
                }
                 ?>
              </select>
            </form>

            <div id="txtHint"></div>
            <?php echo $ftter; ?>
         </div>
      </div>
      <?php include('includes/js.php'); ?>
   </body>
</html>
