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
if (isset($_POST['registervehbtn'])) {
    //Pull the variables from the form
    $plate_form = !empty($_POST['plate']) ? trim($_POST['plate']) : null;
    $color_form      = !empty($_POST['color']) ? trim($_POST['color']) : null;
    $model_form       = !empty($_POST['model']) ? trim($_POST['model']) : null;
    $insurance_status_form       = !empty($_POST['insurance_status']) ? trim($_POST['insurance_status']) : null;
    $registration_status_form       = !empty($_POST['registration_status']) ? trim($_POST['registration_status']) : null;
    //Sanitize the variables, prevents xss, etc.
    $plate        = strip_tags($plate_form);
    $color           = strip_tags($color_form);
    $model            = strip_tags($model_form);
    $insurance_status            = strip_tags($insurance_status_form);
    $registration_status            = strip_tags($registration_status_form);

     //check if the person has a valid license
     if ($_SESSION['character_license_driver'] === "Invalid" || $_SESSION['character_license_driver'] === "Expired" || $_SESSION['character_license_driver'] === "Fake" || $_SESSION['character_license_driver'] === "Suspended") {
       header('Location: ' . $url['civ_registernewvehicle'] . '?license=invalid');
       exit();
     }

    //Add any checks (length, etc here....)
    if (strlen($plate) < 2) {
        header('Location: ' . $url['civ_registernewvehicle'] . '?plate=short');
        exit();
    } elseif (strlen($plate) > 8) {
      header('Location: ' . $url['civ_registernewvehicle'] . '?plate=long');
      exit();
    }
    //Continue the execution, check if email is taken.
    $sql  = "SELECT COUNT(vehicle_plate) AS num FROM vehicles WHERE vehicle_plate = :plate";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':plate', $plate);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['num'] > 0) {
      logAction('Tried To Register A Vehicle With A Taken Plate', $user_username);
        header('Location: ' . $url['civ_registernewvehicle'] . '?plate=taken');
        exit();
    }
    //if everything passes, than continue
    $sql          = "INSERT INTO vehicles (vehicle_plate, vehicle_color, vehicle_model, vehicle_is, vehicle_rs, vehicle_vin, vehicle_owner, vehicle_ownername) VALUES (:vehicle_plate, :vehicle_color, :vehicle_model, :vehicle_is, :vehicle_rs, :vehicle_vin, :vehicle_owner, :vehicle_ownername)";
    $stmt         = $pdo->prepare($sql);
    function generateRandomString($length = 17) {
    return substr(str_shuffle(str_repeat($x='0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }
    $vin = generateRandomString();
    $stmt->bindValue(':vehicle_plate', $plate);
    $stmt->bindValue(':vehicle_color', $color);
    $stmt->bindValue(':vehicle_model', $model);
    $stmt->bindValue(':vehicle_is', $insurance_status);
    $stmt->bindValue(':vehicle_rs', $registration_status);
    $stmt->bindValue(':vehicle_vin', $vin);
    $stmt->bindValue(':vehicle_owner', $_SESSION['character_id']);
    $stmt->bindValue(':vehicle_ownername', $_SESSION['character_first_name'] . ' ' . $_SESSION['character_last_name']);
    $result = $stmt->execute();
    if ($result) {
        //redirect
        logAction('Registered New Vehicle', $user_username);
        header('Location: ' . $url['civ_view'] . '?id='. $_SESSION['character_id'] .'&vehicle=registered');
    }
}

//Alerts
if (isset($_GET['license']) && strip_tags($_GET['license']) === 'invalid') {
   $message = '<div class="alert alert-danger" role="alert">It Seems Your Drivers License Is Not Valid.</div>';
} elseif (isset($_GET['plate']) && strip_tags($_GET['plate']) === 'taken') {
	$message = '<div class="alert alert-danger" role="alert">That Plate Is Taken.</div>';
}
?>
<!DOCTYPE html>
<html>
<?php
$page_name = "Register New Vehicle";
include('includes/header.php')
?>
   <body>
      <div class="container">
         <div class="main">
            <a href="<?php print $url['civ_view'] ?>?id=<?php echo $_SESSION['character_id'] ?>"><img src="assets/imgs/dmv.png" class="main-logo" draggable="false"/></a>
            <div class="main-header">
               Hello, <?php echo $_SESSION['character_first_name'] ?>
            </div>
            <?php print($message); ?>
            <form method="post" action="civ-registernewveh.php">
              <div class="form-group">
                 <input type="text" name="plate" class="form-control" maxlength="8" onkeydown="upperCaseF(this)" placeholder="License Plate" data-lpignore="true" required />
              </div>
               <div class="row">
                 <div class="col">
                   <div class="form-group">
                     <select class="form-control" name="color" required>
                        <option value="" disabled selected>Vehicle Color</option>
                        <option value="Black">Black</option>
                        <option value="White">White</option>
                        <option value="Red">Red</option>
                        <option value="Blue">Blue</option>
                        <option value="Green">Green</option>
                        <option value="Yellow">Yellow</option>
                        <option value="Orange">Orange</option>
                        <option value="Brown">Brown</option>
                        <option value="Gray">Gray</option>
                        <option value="Silver">Silver</option>
                        <option value="Gold">Gold</option>
                        <option value="Cyan">Cyan</option>
                     </select>
                   </div>
                 </div>
                 <div class="col">
                   <div class="form-group">
                      <input type="text" name="model" class="form-control" maxlength="64" placeholder="Vehicle Model" data-lpignore="true" required />
                   </div>
                 </div>
               </div>
               <div class="row">
                 <div class="col">
                   <div class="form-group">
                     <select class="form-control" name="insurance_status" required>
                        <option value="" disabled selected>Insurance Status</option>
                        <option value="None">None</option>
                        <option value="Valid">Valid</option>
                        <option value="Invalid">Invalid</option>
                        <option value="Expired">Expired</option>
                        <option value="Fake">Fake</option>
                     </select>
                   </div>
                 </div>
                 <div class="col">
                   <div class="form-group">
                     <select class="form-control" name="registration_status" required>
                        <option value="" disabled selected>Registration Status</option>
                        <option value="None">None</option>
                        <option value="Valid">Valid</option>
                        <option value="Invalid">Invalid</option>
                        <option value="Expired">Expired</option>
                        <option value="Fake">Fake</option>
                     </select>
                   </div>
                 </div>
               </div>
               <div class="form-group">
                  <input class="btn btn-block btn-primary" name="registervehbtn" id="registervehbtn" type="submit" value="Register Vehicle">
               </div>
            </form>
            <script>
            function upperCaseF(a){
              setTimeout(function(){
                a.value = a.value.toUpperCase();
              }, 1);
            }
          </script>
            <?php echo $ftter; ?>
         </div>
      </div>
      <?php include('includes/js.php'); ?>
   </body>
</html>
