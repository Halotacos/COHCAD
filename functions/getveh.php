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
error_reporting(0);
require '../includes/connect.php';
include '../includes/config.php';
session_start();

if ($_GET['deleteveh']) {
  $vehicle_id = strip_tags($_GET['deleteveh']);
  $stmt = $pdo->prepare( "DELETE FROM vehicles WHERE vehicle_id =:vehicle_id" );
  $stmt->bindParam(':vehicle_id', $vehicle_id);
  $stmt->execute();
  header('Location: ../civ-viewveh.php?vehicle=deleted');
  exit();
}

$q = strip_tags($_GET['q']);
echo "<table>
<tr>
<th><center>Plate</center></th>
<th><center>Color</center></th>
<th><center>Model</center></th>
<th><center>Insurance</center></th>
<th><center>Registration</center></th>
<th><center>VIN #</center></th>
<th><center>Delete</center></th>
</tr>";
$char_id = $_SESSION['character_id'];
$getVeh = "SELECT * FROM vehicles WHERE vehicle_id='$q'";
$result = $pdo->prepare($getVeh);
$result->execute();
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
  echo "<tr>";
  echo "<td><center>" . $row['vehicle_plate'] . "</center></td>";
  echo "<td><center>" . $row['vehicle_color'] . "</center></td>";
  echo "<td><center>" . $row['vehicle_model'] . "</center></td>";
  echo "<td><center>" . $row['vehicle_is'] . "</center></td>";
  echo "<td><center>" . $row['vehicle_rs'] . "</center></td>";
  echo "<td><center>" . $row['vehicle_vin'] . "</center></td>";
  echo '<td><a class="btn btn-danger btn-sm" href="functions/getveh.php?deleteveh=' . $row['vehicle_id'] . '" data-title="Delete"><i class="fas fa-minus-circle"></i></a></td>';
  echo "</tr>";
}
echo "</table>";
