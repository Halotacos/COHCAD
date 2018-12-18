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

if (isset($_GET['setid']) && strip_tags($_GET['setid'])) {
  $i   = $_GET['setid'];
  $sql  = "SELECT * FROM identities WHERE identity_id = :i";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':i', $i);
  $stmt->execute();
  $identity = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($identity === false) {
     header('Location: ' . $url['index'] . '');
     exit();
  } else {
    //set the needed session variables
     $sidentity_id    = $identity['identity_id'];
     $_SESSION['identity_id'] = $sidentity_id;

     $sidentity_name    = $identity['identifier'];
     $_SESSION['identifier'] = $sidentity_name;

     $sis_fire    = $identity['is_fire'];
     $_SESSION['is_fire'] = $sis_fire;

     $_SESSION['leo_supervisor'] = "No";

     $_SESSION['active_dispatch'] = "No";

     $_SESSION['sub_division'] = "None";

     if ($identity['fire_supervisor'] === "Yes") {
       $_SESSION['fire_supervisor'] = "Yes";
     } else {
       $_SESSION['fire_supervisor'] = "No";
     }

     $_SESSION['notepad'] = "";

     $_SESSION['current_station'] = "";

     $sidentity_user    = $identity['user'];
     $_SESSION['on_duty'] = "No";
     header('Location: fire-index.php');
     exit();

  } if ($sidentity_user !== $user_id) {
    header('Location: ../../' . $url['index'] . '');
    exit();
  }
}

if ($_SESSION['is_fire'] === "No") {
  header('Location: ' . $url['index'] . '?np=fire');
  exit();
}

$stmts    = $pdo->prepare("SELECT * FROM settings");
$stmts->execute();
$leo_gsettings = $stmts->fetch(PDO::FETCH_ASSOC);
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
                <div id="get911callsFire"></div>
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
