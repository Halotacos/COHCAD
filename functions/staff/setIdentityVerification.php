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
$q = strip_tags($_GET['q']);

$sql     = "UPDATE `settings` SET `identity_approval_needed`='$q'";
$stmt    = $pdo->prepare($sql);
$updateIDV = $stmt->execute();
