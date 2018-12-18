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
if (isset($_POST['addWarrant'])) {
  $warrant_type_form = !empty($_POST['warrant_type']) ? trim($_POST['warrant_type']) : null;
  $warrant_type        = strip_tags($warrant_type_form);

  $sql          = "INSERT INTO warrants (signed_by, reason, wanted_person) VALUES (:signed_by, :reason, :wanted_person)";
  $stmt         = $pdo->prepare($sql);
  $signed_by = "SELF SIGN";
  $CHAR_NAME1 = $_SESSION['character_first_name'];
  $CHAR_NAME2 = $_SESSION['character_last_name'];
  $CHAR_NAME = $CHAR_NAME1 . ' ' . $CHAR_NAME2;
  $stmt->bindValue(':signed_by', $signed_by);
  $stmt->bindValue(':reason', $warrant_type);
  $stmt->bindValue(':wanted_person', $CHAR_NAME);
  $result = $stmt->execute();
  if ($result) {
      logAction('Added warrant to character', $user_username);
      header('Location: ' . $url['civ_newwarrant'] . '?warrant=added');
  }
}

//Alerts
if (isset($_GET['warrant']) && strip_tags($_GET['warrant']) === 'added') {
   $message = '<div class="alert alert-success" role="alert">WARRANT ADDED!</div>';
}
?>
<!DOCTYPE html>
<html>
<?php
$page_name = "Add Warrant";
include('includes/header.php')
?>
   <body>
      <div class="container">
         <div class="main">
            <a href="<?php print $url['civ_view'] ?>?id=<?php echo $_SESSION['character_id'] ?>"><img src="assets/imgs/doj.png" class="main-logo" draggable="false"/></a>
            <div class="main-header">
               Hello, <?php echo $_SESSION['character_first_name'] ?>
            </div>
            <?php print($message); ?>
            <form method="post" action="civ-addwarrant.php">
              <div class="form-group">
                 <select class="form-control" name="warrant_type" required>
                    <option value="" disabled selected>Select Warrant...</option>
                    <option value="Murder">Murder</option>
                    <option value="Murder of a LEO">Murder of a LEO</option>
                    <option value="Murder of LEO(s)">Murder of LEO(s)</option>
                    <option value="Murder of a First Responder">Murder of a First Responder</option>
                    <option value="Murder of First Responder(s)">Murder of First Responder(s)</option>
                    <option value="Murder of a Government Official">Murder of a Government Official</option>
                    <option value="Murder of Government Official(s)">Murder of Government Official(s)</option>
                    <option value="Kidnapping">Kidnapping</option>
                    <option value="Kidnapping of a LEO">Kidnapping of a LEO</option>
                    <option value="Kidnapping of LEO(s)">Kidnapping of LEO(s)</option>
                    <option value="Kidnapping of a First Responder">Kidnapping of a First Responder</option>
                    <option value="Kidnapping of First Responder(s)">Kidnapping of First Responder(s)</option>
                    <option value="Kidnapping of a Government Official">Kidnapping of a Government Official</option>
                    <option value="Kidnapping of Government Official(s)">Kidnapping of Government Official(s)</option>
                    <option value="Robbery">Robbery</option>
                    <option value="Robbery /w Deadly Weapon">Robbery /w Deadly Weapon</option>
                    <option value="Bank Robbery">Bank Robbery</option>
                    <option value="Bank Robbery">Bank Robbery /w Deadly Weapon</option>
                    <option value="Bank Robbery">Prison Break</option>
                    <option value="Bank Robbery">Prison Break /w Deadly Weapon</option>
                    <option value="Bank Robbery">Prison Escape</option>
                    <option value="Failure To Appear In Court">Failure To Appear In Court</option>
                    <option value="Grand Theft">Grand Theft</option>
                    <option value="Grand Theft Auto">Grand Theft Auto</option>
                 </select>
                 <button class="btn btn-info btn-block" id="addWarrant" name="addWarrant">Add Warrant</button>
              </div>
            </form>
            <?php echo $ftter; ?>
         </div>
      </div>
      <?php include('includes/js.php'); ?>
   </body>
</html>
