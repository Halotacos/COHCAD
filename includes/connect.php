<?php

// MySQL Settings
define("DB_HOST", "76.205.161.214:3306");
define("DB_USER", "admin");
define("DB_PASS", "admin");
define("DB_NAME", "hydrid_v1");


// Do Not Edit Below --- SERIOUSLY DON'T TOUCH THIS STUFF.
$pdoOptions = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    PDO::ATTR_EMULATE_PREPARES => false
);

$pdo = new PDO(
    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
    DB_USER,
    DB_PASS,
    $pdoOptions
);