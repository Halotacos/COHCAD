<?php
/**
Hydrid CAD/MDT.
Copyright (C) 2018 s11k and Hydrid.
 Credit is not allowed to be removed from this program, doing so will
 result in copyright takedown.
 WE DO NOT SUPPORT CHANGING CODE IN ANYWAY, AS IT WILL MESS WITH FUTURE
 UPDATES. NO SUPPORT IS PROVIDED FOR CODE THAT IS EDITED.
**/
require 'includes/connect.php';
include 'includes/config.php';
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in'])) {
    header('Location: ' . $url['login'] . '');
    exit();
}
include 'includes/isLoggedIn.php';

if (isset($_GET['id'])) {
    $character = dbquery('SELECT * FROM characters WHERE character_id="' . escapestring($_GET['id']) . '"')[0];
    if ($character === false) {
       header('Location: ' . $url['civ_index'] . '');
       exit();
    } else {
       $character_id    = $character['character_id'];
       $_SESSION['character_id'] = $character_id;

       $character_first_name    = $character['first_name'];
       $_SESSION['character_first_name'] = $character_first_name;

       $character_last_name    = $character['last_name'];
       $_SESSION['character_last_name'] = $character_last_name;

       $character_dob    = $character['date_of_birth'];
       $_SESSION['character_dob'] = $character_dob;

       $character_address    = $character['address'];
       $_SESSION['character_address'] = $character_address;

       $character_height    = $character['height'];
       $_SESSION['character_height'] = $character_height;

       $character_eye_color    = $character['eye_color'];
       $_SESSION['character_eye_color'] = $character_eye_color;

       $character_hair_color    = $character['hair_color'];
       $_SESSION['character_hair_color'] = $character_hair_color;

       $character_sex    = $character['sex'];
       $_SESSION['character_sex'] = $character_sex;

       $character_weight    = $character['weight'];
       $_SESSION['character_weight'] = $character_weight;

       $character_blood_type    = $character['blood_type'];
       $_SESSION['character_blood_type'] = $character_blood_type;

       $character_organ_donor    = $character['organ_donor'];
       $_SESSION['character_organ_donor'] = $character_organ_donor;

       $character_owner_id    = $character['owner_id'];
       $_SESSION['character_owner_id'] = $character_owner_id;

       $character_status      = $character['status'];
       $_SESSION['character_status'] = $character_status;

       $character_license_driver      = $character['license_driver'];
       $_SESSION['character_license_driver'] = $character_license_driver;

       $character_license_firearm      = $character['license_firearm'];
       $_SESSION['character_license_firearm'] = $character_license_firearm;

    } if ($character_owner_id !== $user_id) {
      logAction('Attempted To Access Someone Elses Character', $user_username);
      header('Location: ' . $url['civ_index'] . '');
      exit();
    }
 }

 if (isset($_POST['deleteCharbtn'])) {
   $sql     = "UPDATE `characters` SET `status` = :status WHERE character_id = :charid";
   $stmt    = $pdo->prepare($sql);
   $status  = 'Disabled';
   $stmt->bindValue(':charid', $_SESSION['character_id']);
   $stmt->bindValue(':status', $status);
   $deleteChar = $stmt->execute();
   //Continue
   if ($deleteChar) {
     logAction('Deleted Character', $user_username);
      header('Location: ' . $url['civ_index'] . '?character=deleted');
   }
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
    $stmt->bindValue(':caller_id', $_SESSION['character_id']);
    $stmt->bindValue(':call_description', $call_description);
    $stmt->bindValue(':call_location', $call_location);
    $stmt->bindValue(':call_crossstreat', $call_crossstreat);
    $stmt->bindValue(':call_postal', $call_postal);
    $stmt->bindValue(':call_timestamp', $date . ' ' . $time);
    $result = $stmt->execute();
    if ($result) {
        //redirect
        header('Location: ' . $url['civ_view'] . '?call=created');
    }
}

//Alerts
if (isset($_GET['character']) && strip_tags($_GET['character']) === 'created') {
   $message = '<div class="alert alert-success" role="alert">Character Created!</div>';
} elseif (isset($_GET['vehicle']) && strip_tags($_GET['vehicle']) === 'registered') {
  $message = '<div class="alert alert-success" role="alert">Vehicle Registered!</div>';
} elseif (isset($_GET['call']) && strip_tags($_GET['call']) === 'created') {
  $message = '<div class="alert alert-success" role="alert">New 911 Call Created!</div>';
}
?>
<!DOCTYPE html>
<html>
<?php
$page_name = "Viewing Civilian";
include('includes/header.php')
?>
   <body>
      <div class="container">
         <div class="main">
            <a href="<?php print $url['index'] ?>"><img src="assets/imgs/los_santos.png" class="main-logo" draggable="false"/></a>
            <div class="main-header">
               Hello, <?php echo $_SESSION['character_first_name'] ?>
            </div>
            <?php print($message); ?>
              <div class="row">
                <div class="col">
                  <a data-toggle="modal" href="#dmv1" class="btn btn-primary btn-block btn-sb">DMV</a>
                </div>
                <div class="col">
                  <a href="<?php print $url['civ_firearms'] ?>" class="btn btn-primary btn-block btn-sb">Firearms</a>
                </div>
              </div>

              <div class="row">
                <div class="col">
                  <a data-toggle="modal" href="#modal911" class="btn btn-primary btn-block btn-sb">9-1-1</a>
                </div>
              </div>

              <div class="row">
                <div class="col">
                  <a data-toggle="modal" href="#warrants" class="btn btn-primary btn-block btn-sb">Warrants</a>
                </div>
                <div class="col">
                  <a href="#" onClick="alert('Coming Soon');" class="btn btn-primary btn-block btn-sb">Court</a>
                </div>
              </div>
              <a data-toggle="modal" href="#deletechar" class="btn btn-primary btn-block btn-sb">Delete Character</a>

            <div class="modal fade" id="dmv1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">San Andreas Department Of Motor Vehicle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">

                      <a href="<?php print $url['civ_driverlicense'] ?>" class="btn btn-primary btn-block btn-sb">Drivers License</a>

                      <a href="<?php print $url['civ_registernewvehicle'] ?>" class="btn btn-primary btn-block btn-sb">Register New Vehicle</a>

                      <a href="<?php print $url['civ_viewveh'] ?>" class="btn btn-primary btn-block btn-sb">My Vehicles</a>

                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- warrant modal -->
            <div class="modal fade" id="warrants" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Warrant Management</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">

                      <a href="<?php print $url['civ_newwarrant'] ?>" class="btn btn-primary btn-block btn-sb">Add New Warrant</a>

                      <a href="<?php print $url['civ_viewwarrants'] ?>" class="btn btn-primary btn-block btn-sb">My Warrants</a>

                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- end modal -->
            <!-- 911 modal -->
            <div class="modal fade" id="modal911" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
               <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">New 911 Call</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>
                     <div class="modal-body">
                        <form method="post" action="civ-view.php">
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
                        <input class="btn btn-primary" name="createNewCall" id="createNewCall" type="submit" value="Create New 911 Call">
                     </div>
                     </form>
                     </div>
                  </div>
               </div>
            </div>
            <!-- end modal -->
            <!-- deletecharconfirm -->
            <div class="modal fade" id="deletechar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure you want to delete this character?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <strong><font color="red">After you click delete, this can not be reversed!</font></strong>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form method="post" action="civ-view.php">
                      <input class="btn btn-danger" name="deleteCharbtn" type="submit" value="Delete Character">
                    </form>
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
