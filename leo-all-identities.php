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

if (isset($_POST['deleteId'])) {
    //Pull the variables from the form
    $identity_id = !empty($_POST['identity_id_form']) ? trim($_POST['identity_id_form']) : null;
    $identifier = !empty($_POST['identifier_form']) ? trim($_POST['identifier_form']) : null;
    $leo_supervisor = !empty($_POST['leo_supervisor_form']) ? trim($_POST['leo_supervisor_form']) : null;
    //Sanitize the variables, prevents xss, etc.
    $identity_id_update        = strip_tags($identity_id);
    $identifier_update        = strip_tags($identifier);
    $leo_supervisor_update        = strip_tags($leo_supervisor);
    
    deleteIdentityLEO($identity_id_update, $identifier_update, $leo_supervisor_update);
}
if (isset($_POST['editId'])) {
    //Pull the variables from the form
    $identity_id = !empty($_POST['identity_id_form']) ? trim($_POST['identity_id_form']) : null;
    $identifier = !empty($_POST['identifier_form']) ? trim($_POST['identifier_form']) : null;
    $leo_supervisor = !empty($_POST['leo_supervisor_form']) ? trim($_POST['leo_supervisor_form']) : null;
    $is_dispatch = !empty($_POST['is_dispatch_form']) ? trim($_POST['is_dispatch_form']) : null;
    //Sanitize the variables, prevents xss, etc.
    $identity_id_update        = strip_tags($identity_id);
    $identifier_update        = strip_tags($identifier);
    $leo_supervisor_update        = strip_tags($leo_supervisor);
    $is_dispatch_update        = strip_tags($is_dispatch);
    
    editIdentityLEO($identity_id_update, $identifier_update, $leo_supervisor_update, $is_dispatch_update);
}

if (isset($_GET['id']) && strip_tags($_GET['id']) === 'edited') {
   $message = '<div class="alert alert-success" role="alert" id="dismiss">Identity Updated!</div>';
} elseif (isset($_GET['id']) && strip_tags($_GET['id']) === 'deleted') {
  $message = '<div class="alert alert-danger" role="alert" id="dismiss">Identity Deleted!</div>';
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
               <?php print($message); ?>
               <div class="col-sm-12">
                <?php
                $stmt    = $pdo->prepare("SELECT * FROM identities");
                $stmt->execute();
                $idRows = $stmt->fetch(PDO::FETCH_ASSOC);
                if (empty($idRows['identity_id'])) {
                  echo "<h5 style='margin-top:20px; color:white;'>NO IDENTIFIERS</h5>";
                } else {
                  echo "<h5 style='margin-top:20px; color:white;'>IDENTIFIERS</h5><table style='border: 1px solid black;'>
                  <tr>
                  <th><center>Identifier</center></th>
                  <th><center>Supervisor</center></th>
                  <th><center>Owner</center></th>
                  <th><center>Edit</center></th>
                  </tr>";
                  $stmt2    = $pdo->prepare("SELECT * FROM identities");
                  $stmt2->execute();
                  while($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td><center>" . $row['identifier'] . "</center></td>";
                    echo "<td><center>" . $row['leo_supervisor'] . "</center></td>";
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
                               <form method="post" action="leo-all-identities.php">
                               <input type="hidden" value="'.$row['identity_id'].'" name="identity_id_form">
                               <div class="form-group">
                                  <label style="color:black;">Identifier</label>
                                  <input type="text" name="identifier_form" class="form-control" placeholder="Identifier" value="'.$row['identifier'].'" data-lpignore="true" required />
                               </div>
                               <div class="form-group">
                               <label style="color:black;">LEO Supervisor</label>
                                  <select class="form-control" name="leo_supervisor_form">
                                     <option value="'.$row['leo_supervisor'].'" selected>'.$row['leo_supervisor'].'</option>
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
                  echo "</table>";
                }
                ?>
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
   <script type="text/javascript">
    $(document).ready(function() {
      $('.js-example-basic-single').select2({
        theme: "bootstrap4",
        minimumInputLength: 3,
      });
    });
    $(document).ready(function() {
    $('.js-example-basic-multiple').select2({
      theme: "bootstrap4"
    });
});
      $(document).ready(function () {
        $("#dismiss").delay(3000).fadeOut("slow");
      });
   </script>
   <!-- end js -->
</body>
</html>
