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
//Alerts
if (isset($_GET['license']) && strip_tags($_GET['license']) === 'invalid') {
   $message = '<div class="alert alert-danger" role="alert">It Seems Your Drivers License Is Not Valid.</div>';
}
?>
<!DOCTYPE html>
<html>
<?php
$page_name = "My Warrants";
include('includes/header.php')
?>
   <body>
     <style>
     table {
         width: 100%;
         border-collapse: collapse;
     }

     table, td, th {

         padding: 5px;
     }

     th {text-align: left;}
     </style>
      <div class="container">
         <div class="main">
            <a href="<?php print $url['civ_view'] ?>?id=<?php echo $_SESSION['character_id'] ?>"><img src="assets/imgs/doj.png" class="main-logo" draggable="false"/></a>
            <div class="main-header">
               Hello, <?php echo $_SESSION['character_first_name'] ?>
            </div>
            <?php print($message); ?>
            <?php
            echo "<table>
            <tr>
            <th><center>Issued On</center></th>
            <th><center>Signed By</center></th>
            <th><center>Reason</center></th>
            </tr>";
            $char_id = $_SESSION['character_id'];
            $my_name = $_SESSION['character_first_name'] . ' ' . $_SESSION['character_last_name'];
            $getVeh = "SELECT * FROM warrants WHERE wanted_person='$my_name'";
            $result = $pdo->prepare($getVeh);
            $result->execute();
            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
              echo "<tr>";
              echo "<td><center>" . $row['issued_on'] . "</center></td>";
              echo "<td><center>" . $row['signed_by'] . "</center></td>";
              echo "<td><center>" . $row['reason'] . "</center></td>";
              echo "</tr>";
            }
            echo "</table>";
             ?>
            <?php echo $ftter; ?>
         </div>
      </div>
      <?php include('includes/js.php'); ?>
   </body>
</html>
