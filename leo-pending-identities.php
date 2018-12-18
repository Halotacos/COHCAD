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

if ($_SESSION['leo_supervisor'] === "No") {
  header('Location: ' . $url['leo_index'] . '');
  exit();
}

?>
<!DOCTYPE html>
<html>
<?php
$page_name = "LEO Supervisor";
include('includes/header.php')
?>
<body>
   <div class="container-leo">
      <div class="main-leo">
        <div class="leo-header"><div class="float-right" id="getTime"></div>
        <div class="float-left">
          <?php if (subdivisionModule_isInstalled): ?>
            <div style="margin-top:50px;">
              <h6>Sub Division</h6>
              <?php
              echo "
              <select style='width:200px;' name='changeSubDivision' class='select' onChange='changeSubDivision(this)'>
              <option selected='true' disabled='disabled'>" . $_SESSION['sub_division'] . "</option>";
              $getSDS = "SELECT * FROM sub_divisions";
              $result = $pdo->prepare($getSDS);
              $result->execute();
              while ($row = $result->fetch(PDO::FETCH_ASSOC))
                {
                echo "<option value='" . $row['name'] . "'>" . $row['name'] . "</option>";
                }
              echo "</select>";
               ?>
            </div>
          <?php endif; ?>
        </div>
         <div class="center"><a href="<?php echo $url['leo_index'] ?>"><img src="assets/imgs/police.png" class="main-logo" draggable="false"/></a></div>
         <div class="main-header-leo">
            <div class="float-left">Supervisor: <?php if ($_SESSION['leo_supervisor'] === "Yes") {
              echo 'Yes';
            } else {
              echo 'No';
            } ?></div>
            <div class="center">Welcome, <?php echo $_SESSION['identifier'] ?></div>
         </div>
       </div>
         <?php print($message); ?>
           <div class="row">
             <div class="col-sm-2">
               <a data-toggle="modal" href="#searchNameDB" class="btn btn-success btn-block">Name Lookup</a><br-leo>
               <a data-toggle="modal" href="#searchDMV" class="btn btn-success btn-block">Vehicle Lookup</a><br-leo>
               <a data-toggle="modal" href="#searchWeaponDB" class="btn btn-success btn-block">Weapon Lookup</a><br-leo>
               <a data-toggle="modal" href="#newTicket" class="btn btn-warning btn-block">New Ticket</a><br-leo>
               <a data-toggle="modal" href="#arrestReportModal" class="btn btn-warning btn-block">New Arrest Report</a><br-leo>
               <a data-toggle="modal" href="#notepad" class="btn btn-secondary btn-block">Notepad</a><br-leo>
               <a data-toggle="modal" href="#addBoloModal" class="btn btn-danger btn-block">Add Bolo</a><br-leo>
               <a data-toggle="modal" href="#activeUnitsmodal" class="btn btn-primary btn-block">Active Units</a><br-leo>
               <?php if (mapModule_isInstalled): ?>
                 <a data-toggle="modal" href="#liveMap" class="btn btn-info btn-block">Map</a><br-leo>
               <?php endif; ?>
             </div>
             <div class="col-sm-8">
               <div class="col-sm-12">
                <div id="getPendingIds"></div>
               </div>
             </div>
             <?php if ($_SESSION['leo_supervisor'] === "Yes"): ?>
             <div class="col-sm-2">
               <a href="<?php echo $url['leo_supervisor_view_pending_identities'] ?>" class="btn btn-success btn-block">All Identities</a><br-leo>
               <a href="<?php echo $url['leo_supervisor_view_pending_identities'] ?>" class="btn btn-success btn-block">Pending Identities</a><br-leo>
             </div>
             <?php endif; ?>
           </div>
         <?php echo $ftter; ?>
      </div>
   </div>

   <!-- modals -->

   <!-- end modals -->

   <!-- sounds -->
   <!-- <audio id="panicButton" src="assets/sounds/panic-button.mp3" preload="auto"></audio> -->
   <!-- end sounds -->

   <!-- js -->
   <?php include('includes/js.php'); ?>
   <script src="assets/js/pages/leo.js"></script>
   <!-- end js -->
</body>
</html>
