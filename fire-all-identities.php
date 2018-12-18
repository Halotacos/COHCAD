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

if ($_SESSION['fire_supervisor'] === "No") {
  header('Location: ' . $url['index'] . '?np=fire');
  exit();
}

$stmts    = $pdo->prepare("SELECT * FROM settings");
$stmts->execute();
$leo_gsettings = $stmts->fetch(PDO::FETCH_ASSOC);

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
    if ($result) {
        //redirect
        header('Location: ' . $url['leo_supervisor_view_pending_identities'] . '?id=deleted');
        exit();
    }
}
if (isset($_POST['editId'])) {
    //Pull the variables from the form
    $identity_id_form = !empty($_POST['identity_id_form']) ? trim($_POST['identity_id_form']) : null;
    $identifier_form = !empty($_POST['identifier_form']) ? trim($_POST['identifier_form']) : null;
    $fire_supervisor_form = !empty($_POST['fire_supervisor_form']) ? trim($_POST['fire_supervisor_form']) : null;
    $is_fire_form = !empty($_POST['is_fire_form']) ? trim($_POST['is_fire_form']) : null;
    //Sanitize the variables, prevents xss, etc.
    $identity_id_update        = strip_tags($identity_id_form);
    $identifier_update        = strip_tags($identifier_form);
    $fire_supervisor_update        = strip_tags($fire_supervisor_form);
    $is_fire_update        = strip_tags($is_fire_form);
    //if everything passes, than continue
    $sql     = "UPDATE `identities` SET `identifier`=:identifier, `fire_supervisor`=:fire_supervisor, `is_fire`=:is_fire WHERE identity_id=:identity_id";
    $stmt    = $pdo->prepare($sql);
    $stmt->bindParam(':identity_id', $identity_id_update);
    $stmt->bindParam(':fire_supervisor', $fire_supervisor_update);
    $stmt->bindParam(':identifier', $identifier_update);
    $stmt->bindParam(':is_fire', $is_fire_update);
    $updateId = $stmt->execute();
    if ($updateId) {
      header('Location: fire-all-identities.php?id=edited');
      exit();
    }
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
$page_name = "Fire Home";
include('includes/header.php')
?>
<body>
   <div class="container-leo">
      <div class="main-leo">
        <div class="leo-header"><div class="float-right" id="getTime"></div>
          <div class="float-left">
          </div>
         <div class="center"><a href="functions/leo/api.php?a=endShift"><img src="assets/imgs/fire.png" class="main-logo" draggable="false"/></a></div>
         <div class="main-header-leo">
            <div class="float-left">Supervisor: <?php if ($_SESSION['fire_supervisor'] === "Yes") {
              echo 'Yes';
            } else {
              echo 'No';
            } ?></div>
            <div class="float-right"><div id="checkAOP"></div></div>
            <div class="center">Welcome, <?php echo $_SESSION['identifier'] ?></div>
         </div>
       </div>
         <div id="checkStatus"></div>
         <h6 style="color:white;">DEPARTMENT/STATION</h6>
         <?php if ($_SESSION['on_duty'] === "No"): ?>
           <select style='width:250px;' name='setFireStation' class='select' onChange='setFireStation(this)'>
           <option selected='true' disabled='disabled'><?php if ($_SESSION['on_duty'] === "No") {
             echo '--- SELECT DEPARTMENT ---';
           } else {
             echo $_SESSION['current_station'];
           } ?></option>
           <option value='R.H.F.D'>Rockford Hills Fire Department</option>
           <option value='D.F.D'>Davis Fire Department</option>
           <option value='E.L.H.F.D'>El Burro Heights Fire Department</option>
           <option value='P.B.F.D'>Paleto Bay Fire Department</option>
           <option value='L.S.I.A.F.D'>L.S.I.A Fire Department</option>
           <option value='S.S.F.D'>Sandy Shores Fire Department</option>
           <option value='F.Z.F.D'>Fort Zancudo Fire Department</option>
         </select>
         <?php else: ?>
           <select style='width:200px;' name='updateFireStation' class='select' onChange='updateFireStation(this)'>
           <option selected='true' disabled='disabled'><?php
             echo $_SESSION['current_station'];
            ?></option>
           <option value='R.H.F.D'>Rockford Hills Fire Department</option>
           <option value='D.F.D'>Davis Fire Department</option>
           <option value='E.L.H.F.D'>El Burro Heights Fire Department</option>
           <option value='P.B.F.D'>Paleto Bay Fire Department</option>
           <option value='L.S.I.A.F.D'>L.S.I.A Fire Department</option>
           <option value='S.S.F.D'>Sandy Shores Fire Department</option>
           <option value='F.Z.F.D'>Fort Zancudo Fire Department</option>
         </select>
         <?php endif; ?>
           <div class="row">
             <div class="col-sm-2">
               <a data-toggle="modal" href="#notepad" class="btn btn-secondary btn-block">Notepad</a><br-leo>
               <?php if (mapModule_isInstalled): ?>
                 <a data-toggle="modal" href="#liveMap" class="btn btn-info btn-block">Map</a><br-leo>
               <?php endif; ?>
             </div>
             <div class="col-sm-8">
               <form method="post" action="leo-index.php">

               </form>
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
                   <th><center>Fire Supervisor</center></th>
                   <th><center>Is Fire/EMS</center></th>
                   <th><center>Owner</center></th>
                   <th><center>Edit</center></th>
                   </tr>";
                   $stmt2    = $pdo->prepare("SELECT * FROM identities");
                   $stmt2->execute();
                   while($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                     echo "<tr>";
                     echo "<td><center>" . $row['identifier'] . "</center></td>";
                     echo "<td><center>" . $row['fire_supervisor'] . "</center></td>";
                     echo "<td><center>" . $row['is_fire'] . "</center></td>";
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
                                <form method="post" action="fire-all-identities.php">
                                <input type="hidden" value="'.$row['identity_id'].'" name="identity_id_form">
                                <div class="form-group">
                                   <label style="color:black;">Identifier</label>
                                   <input type="text" name="identifier_form" class="form-control" placeholder="Identifier" value="'.$row['identifier'].'" data-lpignore="true" required />
                                </div>
                                <div class="form-group">
                                <label style="color:black;">Fire Supervisor</label>
                                   <select class="form-control" name="fire_supervisor_form">
                                      <option value="'.$row['fire_supervisor'].'" selected>'.$row['fire_supervisor'].'</option>
                                      <option value="No">No</option>
                                      <option value="Yes">Yes</option>
                                   </select>
                                </div>
                                <div class="form-group">
                                <label style="color:black;">Approved For Fire/EMS</label>
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
                   echo "</table>";
                 }
                 ?>
               </div>
             </div>
             <?php if ($_SESSION['fire_supervisor'] === "Yes"): ?>
               <div class="col-sm-2">
                 <a href="fire-all-identities.php" class="btn btn-success btn-block">All Identities</a><br-leo>
               </div>
             <?php endif; ?>
           </div>
         <?php echo $ftter; ?>
      </div>
   </div>

   <!-- modals -->
   <!-- notepad modal -->
   <div class="modal fade" id="notepad" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Notepad (Resets After Shift)</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
              <form method="post" action="leo-index.php">
                <div class="form-group">
                  <?php if ($siteSettings['theme'] === "lumen"): ?>
                    <textarea name="textarea" oninput="updateNotepad(this.value)" rows="12" cols="89"><?php echo $_SESSION['notepad']; ?></textarea>
                  <?php elseif ($siteSettings['theme'] === "pulse" OR $siteSettings['theme'] === "pulsev2"): ?>
                    <textarea name="textarea" oninput="updateNotepad(this.value)" rows="12" cols="89"><?php echo $_SESSION['notepad']; ?></textarea>
                  <?php elseif ($siteSettings['theme'] === "simplex"): ?>
                    <textarea name="textarea" oninput="updateNotepad(this.value)" rows="12" cols="89"><?php echo $_SESSION['notepad']; ?></textarea>
                  <?php else: ?>
                    <textarea name="textarea" oninput="updateNotepad(this.value)" rows="12" cols="74"><?php echo $_SESSION['notepad']; ?></textarea>
                  <?php endif; ?>
                </div>
           </div>
           </form>
         </div>
      </div>
   </div>
   <!-- // -->
   <!-- live map modal -->
   <div class="modal fade" id="liveMap" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Live Map</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
              <iframe frameborder="0" width="100%" height="720" src="<?php echo $mapModule_link; ?>"></iframe>
            </div>
      </div>
   </div>
  </div>
   <!-- // -->
   <!-- end modals -->
   <!-- js -->
   <?php include('includes/js.php'); ?>
   <script src="assets/js/pages/fire.js"></script>
   <!-- end js -->
</body>
</html>
