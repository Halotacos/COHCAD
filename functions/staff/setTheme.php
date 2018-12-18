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
require '../../includes/connect.php';
include '../../includes/config.php';
session_start();
$a = strip_tags($_GET['a']);

if ($a === "theme") {
  $q = strip_tags($_GET['q']);

  $sql     = "UPDATE `settings` SET `theme`='$q'";
  $stmt    = $pdo->prepare($sql);
  $updateTheme = $stmt->execute();
  exit();
} elseif ($a === "timezone") {
  $q = strip_tags($_GET['q']);

  $sql     = "UPDATE `settings` SET `timezone`='$q'";
  $stmt    = $pdo->prepare($sql);
  $updateTimeZone = $stmt->execute();
  exit();
} elseif ($a === "deleteSubDivision") {
  $q = strip_tags($_GET['id']);
  $stmt = $pdo->prepare("DELETE FROM sub_divisions WHERE id =:id");
  $stmt->bindParam(':id', $q);
  $stmt->execute();
  header('Location: ../../staff.php?module=updated');
  exit();
}
