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

require('includes/connect.php');
include('includes/config.php');

// Set Content-Type to JSON
header('Content-Type: application/json');

// Check if API Endpoint is Valid
if(!isset($_GET['endpoint'])) {
    echo json_encode(array(
        'response' => 400,
        'content' => 'Missing Endpoint'
    ));
    exit();
}

switch(strtolower($_GET['endpoint'])) {
    case "identities":
        echo json_encode(array(
            'response' => 200,
            'content' => dbquery('SELECT * FROM identities')
        ));
        break;
    case "logs":
        echo json_encode(array(
            'response' => 200,
            'content' => dbquery('SELECT * FROM logs')
        ));
        break;
    case "tickets":
        echo json_encode(array(
            'response' => 200,
            'content' => dbquery('SELECT * FROM tickets')
        ));
        break;
    case "vehicles":
        echo json_encode(array(
            'response' => 200,
            'content' => dbquery('SELECT * FROM vehicles')
        ));
        break;
    case "warrants":
        echo json_encode(array(
            'response' => 200,
            'content' => dbquery('SELECT * FROM warrants')
        ));
        break;
    case "weapons":
        echo json_encode(array(
            'response' => 200,
            'content' => dbquery('SELECT * FROM weapons')
        ));
        break;
    case "onduty":
        echo json_encode(array(
            'response' => 200,
            'content' => dbquery('SELECT * FROM on_duty')
        ));
        break;
    case "characters":
        echo json_encode(array(
            'response' => 200,
            'content' => dbquery('SELECT * FROM characters')
        ));
        break;
    case "bolos":
        echo json_encode(array(
            'response' => 200,
            'content' => dbquery('SELECT * FROM bolos')
        ));
        break;
    case "arrest_reports":
        echo json_encode(array(
            'response' => 200,
            'content' => dbquery('SELECT * FROM arrest_reports')
        ));
        break;
    case "calls":
        echo json_encode(array(
            'response' => 200,
            'content' => dbquery('SELECT * FROM 911calls')
        ));
        break;
    case "settings":
        $data = array(
            'response' => 200,
            'content' => dbquery('SELECT * FROM settings')
        );
        $data['content'][0]['serverip'] = $_SERVER['SERVER_NAME'];
        echo json_encode($data);
        break;
    default:
        echo json_encode(array(
            'response' => 400,
            'content' => 'Invalid Endpoint'
        ));
        break;
}