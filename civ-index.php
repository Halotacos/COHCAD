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

if (isset($_POST['createcharbtn'])) {
    //Pull the variables from the form
    $first_name_form = !empty($_POST['first_name']) ? trim($_POST['first_name']) : null;
    $last_name_form      = !empty($_POST['last_name']) ? trim($_POST['last_name']) : null;
    $sex_form       = !empty($_POST['sex']) ? trim($_POST['sex']) : null;
    $date_of_birth_form       = !empty($_POST['date_of_birth']) ? trim($_POST['date_of_birth']) : null;
    $address_form       = !empty($_POST['address']) ? trim($_POST['address']) : null;
    $eye_color_form       = !empty($_POST['eye_color']) ? trim($_POST['eye_color']) : null;
    $hair_color_form       = !empty($_POST['hair_color']) ? trim($_POST['hair_color']) : null;
    $height_form       = !empty($_POST['height']) ? trim($_POST['height']) : null;
    $weight_form       = !empty($_POST['weight']) ? trim($_POST['weight']) : null;
    $blood_type_form       = !empty($_POST['blood_type']) ? trim($_POST['blood_type']) : null;
    $organ_donor_form       = !empty($_POST['organ_donor']) ? trim($_POST['organ_donor']) : null;

    //Sanitize the variables, prevents xss, etc.
    $first_name        = strip_tags($first_name_form);
    $last_name           = strip_tags($last_name_form);
    $sex            = strip_tags($sex_form);
    $date_of_birth            = strip_tags($date_of_birth_form);
    $address            = strip_tags($address_form);
    $eye_color            = strip_tags($eye_color_form);
    $hair_color            = strip_tags($hair_color_form);
    $height            = strip_tags($height_form);
    $weight            = strip_tags($weight_form);
    $blood_type            = strip_tags($blood_type_form);
    $organ_donor            = strip_tags($organ_donor_form);

    //Continue the execution, check if email is taken.
    $sql  = "SELECT COUNT(first_name) AS num FROM characters WHERE first_name = :first_name";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':first_name', $first_name);
    $stmt->execute();
    $row1 = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql2  = "SELECT COUNT(last_name) AS num FROM characters WHERE last_name = :last_name";
    $stmt2 = $pdo->prepare($sql2);
    $stmt2->bindValue(':last_name', $last_name);
    $stmt2->execute();
    $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    if ($row1['num'] > 0 AND $row2['num'] > 0) {
        logAction('Tried To Register A Taken Character', $user_username);
        header('Location: ' . $url['civ_index'] . '?character=taken');
        exit();
    }

    //if everything passes, than continue
    $sql          = "INSERT INTO characters (first_name, last_name, date_of_birth, address, height, eye_color, hair_color, sex, weight, blood_type, organ_donor, owner_id, owner_name) VALUES (:first_name, :last_name, :date_of_birth, :address, :height, :eye_color, :hair_color, :sex, :weight, :blood_type, :organ_donor, :owner_id, :owner_name)";
    $stmt         = $pdo->prepare($sql);
    $stmt->bindValue(':first_name', $first_name);
    $stmt->bindValue(':last_name', $last_name);
    $stmt->bindValue(':date_of_birth', $date_of_birth);
    $stmt->bindValue(':address', $address);
    $stmt->bindValue(':height', $height);
    $stmt->bindValue(':eye_color', $eye_color);
    $stmt->bindValue(':hair_color', $hair_color);
    $stmt->bindValue(':sex', $sex);
    $stmt->bindValue(':weight', $weight);
    $stmt->bindValue(':blood_type', $blood_type);
    $stmt->bindValue(':organ_donor', $organ_donor);
    $stmt->bindValue(':owner_id', $user_id);
    $stmt->bindValue(':owner_name', $user_username);
    $result = $stmt->execute();
    if ($result) {
        //redirect
        logAction('Registered New Character', $user_username);
        header('Location: ' . $url['civ_index'] . '?character=created');
    }
}

//Alerts
if (isset($_GET['character']) && strip_tags($_GET['character']) === 'created') {
   $message = '<div class="alert alert-success" role="alert">Character Created!</div>';
} elseif (isset($_GET['character']) && strip_tags($_GET['character']) === 'deleted') {
   $message = '<div class="alert alert-info" role="alert">Character Deleted From System!</div>';
} elseif (isset($_GET['character']) && strip_tags($_GET['character']) === 'taken') {
   $message = '<div class="alert alert-info" role="alert">That name has already been taken!</div>';
}
?>
<!DOCTYPE html>
<html>
   <?php
   $page_name = "Civilian Home";
   include('includes/header.php')
   ?>
   <body>
      <div class="container">
         <div class="main">
            <a href="<?php print $url['index'] ?>"><img src="assets/imgs/los_santos.png" class="main-logo" draggable="false"/></a>
            <div class="main-header">
               Hello, <?php echo $user_username ?>
            </div>
            <?php print($message); ?>
              <button class="btn btn-primary btn-block btn-sb" data-toggle="modal" data-target="#newCharacterModel">Create New Character</button>
              <button class="btn btn-primary btn-block btn-sb" data-toggle="modal" data-target="#viewCharactersModel">View Characters</button>
            <!-- Create new char model -->
            <div class="modal fade" id="newCharacterModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
               <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Create New Character</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>
                     <div class="modal-body">
                        <form method="post" action="civ-index.php">
                           <div class="row">
                              <div class="col">
                                 <div class="form-group">
                                    <input type="text" name="first_name" class="form-control" maxlength="126" placeholder="First Name" data-lpignore="true" required />
                                 </div>
                              </div>
                              <div class="col">
                                 <div class="form-group">
                                    <input type="text" name="last_name" class="form-control" maxlength="126" placeholder="Last Name" data-lpignore="true" required />
                                 </div>
                              </div>
                           </div>
                           <div class="form-group">
                              <select class="form-control" name="sex" required>
                                 <option value="" disabled selected>Sex</option>
                                 <option value="Male">Male</option>
                                 <option value="Female">Female</option>
                                 <option value="Transgender">Transgender</option>
                                 <option value="Other">Other</option>
                              </select>
                           </div>
                           <div class="form-group">
                              <input type="text" data-provide="datepicker" name="date_of_birth" class="form-control" placeholder="Date of Birth" data-lpignore="true" required />
                           </div>
                           <div class="form-group">
                              <input type="text" name="address" class="form-control" placeholder="Address" data-lpignore="true" required />
                           </div>
                           <div class="row">
                              <div class="col">
                                 <div class="form-group">
                                    <select class="form-control" name="eye_color" required>
                                       <option value="" disabled selected>Eye Color</option>
                                       <option value="Amber">Amber</option>
                                       <option value="Blue">Blue</option>
                                       <option value="Brown">Brown</option>
                                       <option value="Gray">Gray</option>
                                       <option value="Green">Green</option>
                                       <option value="Hazel">Hazel</option>
                                       <option value="Red">Red</option>
                                    </select>
                                 </div>
                              </div>
                              <div class="col">
                                 <div class="form-group">
                                    <select class="form-control" name="hair_color" required>
                                       <option value="" disabled selected>Hair Color</option>
                                       <option value="Black">Black</option>
                                       <option value="Brown">Brown</option>
                                       <option value="Blond">Blond</option>
                                       <option value="Auburn">Auburn</option>
                                       <option value="Red">Red</option>
                                       <option value="Gray">Gray</option>
                                       <option value="White">White</option>
                                       <option value="Bald">Bald</option>
                                    </select>
                                 </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col">
                                 <div class="form-group">
                                    <input type="text" name="height" class="form-control" maxlength="126" placeholder="Height" data-lpignore="true" required />
                                 </div>
                              </div>
                              <div class="col">
                                 <div class="form-group">
                                    <input type="text" name="weight" class="form-control" maxlength="126" placeholder="Weight" data-lpignore="true" required />
                                 </div>
                              </div>
                           </div>
                           <div class="form-group">
                              <select class="form-control" name="blood_type" required>
                                 <option value="" disabled selected>Blood Type</option>
                                 <option value="A+">A+</option>
                                 <option value="A-">A-</option>
                                 <option value="B+">B+</option>
                                 <option value="B-">B-</option>
                                 <option value="O+">O+</option>
                                 <option value="O-">O-</option>
                                 <option value="AB+">AB+</option>
                                 <option value="AB-">AB-</option>
                              </select>
                           </div>
                           <div class="form-group">
                              <select class="form-control" name="organ_donor" required>
                                 <option value="" disabled selected>Organ Donor</option>
                                 <option value="Yes">Yes</option>
                                 <option value="No">No</option>
                              </select>
                           </div>
                     </div>
                     <div class="modal-footer">
                     <div class="form-group">
                        <input class="btn btn-primary" name="createcharbtn" id="createcharbtn" type="submit" value="Create New Character">
                     </div>
                     </form>
                     </div>
                  </div>
               </div>
            </div>
            <!-- List Characters Model -->
            <div class="modal fade" id="viewCharactersModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
               <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Listing Characters</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>
                     <div class="modal-body">
                       <select class="form-control" name="character_list" onchange="location = this.value;">
                         <option selected="true" disabled="disabled">Select Character</option>
                         <?php
                         $status = 'Enabled';
                         $getCharacters = "SELECT * FROM characters WHERE owner_id='$user_id' AND status='$status'";
                         $result = $pdo->prepare($getCharacters);
                         $result->execute();
                         while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                           echo '<option value="'. $url['civ_view'] .'?id='. $row['character_id'] .'">'. $row['first_name'] .' '. $row['last_name'] .'</option>';
                         }
                          ?>
                       </select>
                     </div>
                  </div>
               </div>
            </div>
            <?php echo $ftter; ?>
         </div>
      </div>
      <?php include('includes/js.php'); ?>
   </body>
</html>
