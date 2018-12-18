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

     $sis_dispatch    = $identity['is_dispatch'];
     $_SESSION['is_dispatch'] = $sis_dispatch;

     if ($identity['leo_supervisor'] === "Yes") {
       $_SESSION['leo_supervisor'] = "Yes";
     } else {
       $_SESSION['leo_supervisor'] = "No";
     }

     $_SESSION['active_dispatch'] = "Yes";

     $_SESSION['sub_division'] = "None";

     $sidentity_user    = $identity['user'];
     $_SESSION['on_duty'] = "No";
     header('Location: ' . $url['dispatch_index'] . '');
     exit();

  } if ($sidentity_user !== $user_id) {
    header('Location: ' . $url['index'] . '');
    exit();
  }
}

if ($_SESSION['is_dispatch'] === "No") {
  header('Location: ' . $url['index'] . '?np=dispatch');
  exit();
}

$stmts    = $pdo->prepare("SELECT * FROM settings");
$stmts->execute();
$leo_gsettings = $stmts->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['addBoloBtn'])) {
    //Pull the variables from the form
    $bolo_created_by_form = !empty($_POST['bolo_created_by']) ? trim($_POST['bolo_created_by']) : null;
    $vehicle_plate_form = !empty($_POST['vehicle_plate']) ? trim($_POST['vehicle_plate']) : null;
    $vehicle_model_form = !empty($_POST['vehicle_model']) ? trim($_POST['vehicle_model']) : null;
    $vehicle_color_form = !empty($_POST['vehicle_color']) ? trim($_POST['vehicle_color']) : null;
    $bolo_reason_form = !empty($_POST['bolo_reason']) ? trim($_POST['bolo_reason']) : null;
    //Sanitize the variables, prevents xss, etc.
    $bolo_created_by        = strip_tags($bolo_created_by_form);
    $vehicle_plate        = strip_tags($vehicle_plate_form);
    $vehicle_model        = strip_tags($vehicle_model_form);
    $vehicle_color        = strip_tags($vehicle_color_form);
    $bolo_reason        = strip_tags($bolo_reason_form);

    //than
    $sql          = "INSERT INTO bolos (vehicle_plate, vehicle_color, vehicle_model, bolo_reason, bolo_created_by, bolo_created_on) VALUES (:vehicle_plate, :vehicle_color, :vehicle_model, :bolo_reason, :bolo_created_by, :bolo_created_on)";
    $stmt         = $pdo->prepare($sql);
    $stmt->bindValue(':vehicle_plate', $vehicle_plate);
    $stmt->bindValue(':vehicle_color', $vehicle_color);
    $stmt->bindValue(':vehicle_model', $vehicle_model);
    $stmt->bindValue(':bolo_reason', $bolo_reason);
    $stmt->bindValue(':bolo_created_by', $bolo_created_by);
    $stmt->bindValue(':bolo_created_on', $date . ' ' . $time);
    $result = $stmt->execute();
    if ($result) {
      logAction('(DISPATCH) Added Bolo', $user_username);
        //redirect
        $message='<div class="alert alert-success" id="dismiss">Bolo Added</div>';
    }
}

if (isset($_POST['1041btn'])) {
  $sqlcd  = "SELECT COUNT(identifier) AS num FROM on_duty WHERE identifier = :identifier";
  $stmtcd = $pdo->prepare($sqlcd);
  $stmtcd->bindValue(':identifier', $_SESSION['identifier']);
  $stmtcd->execute();
  $rowcd = $stmtcd->fetch(PDO::FETCH_ASSOC);
  if ($rowcd['num'] > 0) {
    $i = $_SESSION['identifier'];
    $sql     = "DELETE FROM `on_duty` WHERE identifier = :i";
    $stmt    = $pdo->prepare($sql);
    $stmt->bindValue(':i', $i);
    $delete_old_ids = $stmt->execute();
  }

  $sql          = "INSERT INTO on_duty (identifier, type) VALUES (:identifier, :type)";
  $duty_type = "DISPATCH";
  $stmt         = $pdo->prepare($sql);
  $stmt->bindValue(':identifier', $_SESSION['identifier']);
  $stmt->bindValue(':type', $duty_type);
  $result = $stmt->execute();
  $_SESSION['on_duty'] = "Yes";
  logAction('(DISPATCH) Went On Duty', $user_username);

  header('Location: ' . $url['dispatch_index'] . '');
  exit();
}
if (isset($_POST['1042btn'])) {
  $i = $_SESSION['identifier'];
  $sql     = "DELETE FROM `on_duty` WHERE identifier = :i";
  $stmt    = $pdo->prepare($sql);
  $stmt->bindValue(':i', $i);
  $endShift = $stmt->execute();
  $_SESSION['on_duty'] = "No";
  logAction('(DISPATCH) Went Off Duty', $user_username);
  header('Location: ' . $url['dispatch_index'] . '');
  exit();
}

if (isset($_POST['createNewCall'])) {
    //Pull the variables from the form
    $call_description_form = !empty($_POST['call_description']) ? trim($_POST['call_description']) : null;
    $call_location_form      = !empty($_POST['call_location']) ? trim($_POST['call_location']) : null;
    $call_crossstreat_form       = !empty($_POST['call_crossstreat']) ? trim($_POST['call_crossstreat']) : null;
    $call_postal_form       = !empty($_POST['call_postal']) ? trim($_POST['call_postal']) : null;

    //Sanitize the variables, prevents xss, etc.
    $call_description        = strip_tags($call_description_form);
    $call_location           = strip_tags($call_location_form);
    $call_crossstreat            = strip_tags($call_crossstreat_form);
    $call_postal            = strip_tags($call_postal_form);

    //if everything passes, than continue
    $sql          = "INSERT INTO 911calls (caller_id, call_description, call_location, call_crossstreat, call_postal, call_timestamp) VALUES (:caller_id, :call_description, :call_location, :call_crossstreat, :call_postal, :call_timestamp)";
    $stmt         = $pdo->prepare($sql);
    $stmt->bindValue(':caller_id', $_SESSION['identifier']);
    $stmt->bindValue(':call_description', $call_description);
    $stmt->bindValue(':call_location', $call_location);
    $stmt->bindValue(':call_crossstreat', $call_crossstreat);
    $stmt->bindValue(':call_postal', $call_postal);
    $stmt->bindValue(':call_timestamp', $date . ' ' . $time);
    $result = $stmt->execute();
    if ($result) {
      logAction('(DISPATCH) Created New Call', $user_username);
        //redirect
        header('Location: ' . $url['dispatch_index'] . '?call=created');
    }
}

//Alerts
if (isset($_GET['call']) && strip_tags($_GET['call']) === 'created') {
  $message = '<div class="alert alert-success" role="alert" id="dismiss">New Call Created!</div>';
} elseif (isset($_GET['license']) && strip_tags($_GET['license']) === 'suspended') {
  $message = '<div class="alert alert-success" role="alert" id="dismiss">License Suspended!</div>';
}
?>
<!DOCTYPE html>
<html>
<?php
$page_name = "Dispatch Home";
include('includes/header.php')
?>
<body>
   <div class="container-leo">
      <div class="main-leo">
        <div class="leo-header"><div class="float-right" id="getTime"></div>
         <div class="center"><a href="functions/leo/api.php?a=endShift"><img src="assets/imgs/dispatch.png" class="main-logo" draggable="false"/></a></div>
         <div class="main-header-leo">
            <div class="float-left">Supervisor: <?php if ($_SESSION['leo_supervisor'] === "Yes") {
              echo 'Yes';
            } else {
              echo 'No';
            } ?></div>
            <div class="float-right"><div id="checkAOP"></div></div>
            <div class="center">Welcome, <?php echo $_SESSION['identifier'] ?></div>
         </div>
       </div>
         <div id="checkStatus"></div>
           <div class="row">
             <div class="col-sm-12">
                 <form method="post" action="dispatch-index.php">
               <button class="btn btn-info btn-md" name="1041btn" style="width:200px; margin-bottom: 4px;" type="submit" type="button" <?php if ($_SESSION['on_duty'] === "Yes") {echo 'disabled';} ?>>10-41</button>
               <button class="btn btn-info btn-md" name="1042btn" style="width:200px; margin-bottom: 4px;" type="submit" type="button" <?php if ($_SESSION['on_duty'] === "No") {echo 'disabled';} ?>>10-42</button>
               </form>
               <a data-toggle="modal" href="#searchNameDB" style="width:200px; margin-bottom: 4px;" class="btn btn-success">Name Lookup</a>
               <a data-toggle="modal" href="#searchDMV" style="width:200px; margin-bottom: 4px;" class="btn btn-success">Vehicle Lookup</a>
               <a data-toggle="modal" href="#searchWeaponDB" style="width:200px; margin-bottom: 4px;" class="btn btn-success">Weapon Lookup</a>
               <a data-toggle="modal" href="#addBoloModal" style="width:200px; margin-bottom: 4px;" class="btn btn-danger">Add Bolo</a>
               <a data-toggle="modal" href="#modal911" style="width:200px; margin-bottom: 4px;" class="btn btn-info">New Call</a>
               <a data-toggle="modal" href="#aop" style="width:200px; margin-bottom: 4px;" class="btn btn-danger">Change AOP</a>
               <?php if (mapModule_isInstalled): ?>
                 <a data-toggle="modal" href="#liveMap" style="width:200px; margin-bottom: 4px;" class="btn btn-info">Map</a><br-leo>
               <?php endif; ?>
               <?php print($message); ?>
               <div class="row">
               <div class="col">
                   <div id="getActiveUnitsDispatch">
                       <h5 style='margin-top:20px; color:white;'>Active Units</h5>
                       <div style='overflow-y: scroll; height:200px;'>
                           <table style='border: 1px solid black;' id="dispUnitsTable">

                           </table>
                       </div>
                   </div>
               </div>
               <div class="col">
                <div id="getBolos"></div>
               </div>
             </div>
               <div class="row">
                 <div class="col">
                  <div id="get911calls"></div>
                 </div>
               </div>
             </div>
           </div>
         <?php echo $ftter; ?>
      </div>
   </div>

   <!-- modals -->
   <!-- 911 modal -->
   <div class="modal fade" id="modal911" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">New Call</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <form method="post" action="dispatch-index.php">
                 <div class="form-group">
                    <input type="text" name="call_description" class="form-control" maxlength="20" placeholder="Call Desc" data-lpignore="true" required />
                 </div>
                  <div class="row">
                     <div class="col">
                        <div class="form-group">
                           <input type="text" name="call_location" class="form-control" placeholder="Street" data-lpignore="true" required />
                        </div>
                     </div>
                     <div class="col">
                        <div class="form-group">
                           <input type="text" name="call_crossstreat" class="form-control" placeholder="Nearest Cross Street" data-lpignore="true" required />
                        </div>
                     </div>
                     <div class="col">
                        <div class="form-group">
                           <input type="text" name="call_postal" class="form-control" pattern="\d*" placeholder="Postal" data-lpignore="true" required />
                        </div>
                     </div>
                  </div>
            </div>
            <div class="modal-footer">
            <div class="form-group">
               <input class="btn btn-primary" name="createNewCall" id="createNewCall" type="submit" value="Create New Call">
            </div>
            </form>
            </div>
         </div>
      </div>
   </div>
   <!-- end modal -->
   <!-- search weapon modal -->
   <div class="modal fade" id="searchWeaponDB" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Weapon Database</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
              <form>
                <select class="js-example-basic-single" name="weaponSearch" onchange="showWpn(this.value)">
                  <option selected="true" disabled="disabled">Search Serial, or Owner Name</option>
                  <?php
                  $status = 'Enabled';
                  $getWpn = "SELECT * FROM weapons WHERE wpn_status='$status'";
                  $result = $pdo->prepare($getWpn);
                  $result->execute();
                  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="'. $row['wpn_id'] .'">'. $row['wpn_type'] .' - '. $row['wpn_serial'] .' - '. $row['wpn_ownername'] .'</option>';
                  }
                   ?>
                </select>
              </form>

              <div id="showWpn"></div>
            </div>
         </div>
      </div>
   </div>
   <!-- // -->
   <!-- search vehicle modal -->
   <div class="modal fade" id="searchDMV" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Vehicle Database</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
              <form>
                <select class="js-example-basic-single" name="plateSearch" onchange="showVeh(this.value)">
                  <option selected="true" disabled="disabled">Search VIN, Plate, Or Model</option>
                  <?php
                  $status = 'Enabled';
                  $getVeh = "SELECT * FROM vehicles WHERE vehicle_status='$status'";
                  $result = $pdo->prepare($getVeh);
                  $result->execute();
                  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="'. $row['vehicle_id'] .'">'. $row['vehicle_vin'] .' - '. $row['vehicle_plate'] .' - '. $row['vehicle_model'] .'</option>';
                  }
                   ?>
                </select>
              </form><br>
              <div id="showVehInfo"></div>
            </div>
         </div>
      </div>
   </div>
   <!-- // -->
   <!-- search name modal -->
   <div class="modal fade" id="searchNameDB" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Name Database</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
              <form>
                <select class="js-example-basic-single" name="weaponSearch" onchange="showName(this.value)">
                  <option selected="true" disabled="disabled">Search Name, Or DOB</option>
                  <?php
                  $status = 'Enabled';
                  $getChars = "SELECT * FROM characters WHERE status='$status'";
                  $result = $pdo->prepare($getChars);
                  $result->execute();
                  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="'. $row['character_id'] .'">'. $row['first_name'] .' '. $row['last_name'] .' // '. $row['date_of_birth'] .'</option>';
                  }
                   ?>
                </select>
              </form><br>
              <div id="showPersonInfo"></div>
            </div>
         </div>
      </div>
   </div>
   <!-- // -->
   <!-- add bolo modal -->
   <div class="modal fade" id="addBoloModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Add Bolo</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
              <form method="post" action="dispatch-index.php">
                <div class="form-group">
                   <input type="text" name="bolo_created_by" class="form-control" maxlength="126" readonly="true" value="<?php echo $_SESSION['identifier'] ?>" data-lpignore="true" />
                </div>
                <div class="form-group">
                  <select class="js-example-basic-single" name="vehicle_plate">
                    <option selected="true" disabled="disabled">Search For Plate</option>
                    <?php
                    $status = 'Enabled';
                    $getChars = "SELECT * FROM vehicles WHERE vehicle_status='$status'";
                    $result = $pdo->prepare($getChars);
                    $result->execute();
                    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                      echo '<option value="'. $row['vehicle_plate'] .'">'. $row['vehicle_plate'] .'</option>';
                    }
                     ?>
                  </select>
                </div>
                <div class="form-group">
                  <input type="text" name="vehicle_model" class="form-control" maxlength="126" placeholder="Vehicle Model" data-lpignore="true" required />
                </div>
                <div class="form-group">
                  <input type="text" name="vehicle_color" class="form-control" maxlength="126" placeholder="Vehicle Color" data-lpignore="true" required />
                </div>
                <div class="form-group">
                  <input type="text" name="bolo_reason" class="form-control" maxlength="255" placeholder="Bolo Reason" data-lpignore="true" required />
                </div>
           </div>
           <div class="modal-footer">
           <div class="form-group">
              <input class="btn btn-primary" name="addBoloBtn" id="addBoloBtn" type="submit" value="Add Bolo">
           </div>
           </form>
            </div>
         </div>
      </div>
   </div>
   <!-- // -->
   <!-- aop modal -->
   <div class="modal fade" id="aop" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">AOP Management</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
              <select class="form-control" id="aopSet" onchange="setAOP(this.value)">
                <option selected="true" disabled="disabled"><?php echo $leo_gsettings['aop']; ?></option>
                <option value="State Wide">State Wide</option>
                <option value="Blaine County">Blaine County</option>
                <option value="Sandy Shores">Sandy Shores</option>
                <option value="Grapeseed">Grapeseed</option>
                <option value="Paleto Bay">Paleto Bay</option>
                <option value="Sandy Shores/Grapeseed">Sandy Shores/Grapeseed</option>
                <option value="Sandy Shores/Grapeseed">Grapeseed/Paleto Bay</option>
                <option value="Los Santos">Los Santos</option>
                <option value="Lower Los Santos">Lower Los Santos</option>
                <option value="Mid Los Santos">Mid Los Santos</option>
                <option value="Upper Los Santos">Upper Los Santos</option>
              </select>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
         </div>
      </div>
   </div>
   <!-- end modal -->

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
   <!-- end modals -->
   <!-- js -->
   <?php include('includes/js.php'); ?>
   <script src="assets/js/pages/dispatch.js"></script>
   <!-- end js -->
</body>
</html>
