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
//this script is called to ensure the user always has the latest session variables for his character.
$sql  = "SELECT * FROM characters WHERE character_id = :charid";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':charid', $_SESSION['character_id']);
$stmt->execute();
$character = $stmt->fetch(PDO::FETCH_ASSOC);

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
