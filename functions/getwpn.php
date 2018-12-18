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

if ($_GET['deletewpn']) {
  $wpnid = strip_tags($_GET['deletewpn']);
  $stmt = $pdo->prepare( "DELETE FROM weapons WHERE wpn_id =:wpn_id" );
  $stmt->bindParam(':wpn_id', $wpnid);
  $stmt->execute();
  header('Location: ../civ-firearms.php?firearm=deleted');
  exit();
}

$q = strip_tags($_GET['q']);
echo "<table>
<tr>
<th><center>Weapon Type</center></th>
<th><center>Weapon Serial</center></th>
<th><center>Delete</center></th>
</tr>";
$getWpn = "SELECT * FROM weapons WHERE wpn_id='$q'";
$result = $pdo->prepare($getWpn);
$result->execute();
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
  echo "<tr>";
  echo "<td><center>" . $row['wpn_type'] . "</center></td>";
  echo "<td><center>" . $row['wpn_serial'] . "</center></td>";
  echo '<td><a class="btn btn-danger btn-sm" href="functions/getwpn.php?deletewpn=' . $row['wpn_id'] . '" data-title="Delete"><i class="fas fa-minus-circle"></i></a></td>';
  echo "</tr>";
}
echo "</table>";
