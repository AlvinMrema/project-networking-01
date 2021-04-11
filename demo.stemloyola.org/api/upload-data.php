<?php

/*
 * (C) STEM Loyola 2021. Part of Project #04
 * 
 * This is a public script that uses the private scripts (DB middleware) to
 * clear existing data and upload new cities data
 */

// Require our custom database library
require (__DIR__."/../../db/lib-cities.php");

try {
    // Extract the data from the request
    $data = json_decode( file_get_contents( 'php://input' ), true );

    // Add each city into the database
    $response = array();  // Data that'll be sent back

    clearCitiesData();  // To ensure only the data from the last upload request remains

    $wasSuccessful = true;
    foreach ($data['data'] as $city) {
        $error = null;
        
        if ( addCity($city, $error) == false) {
            $response["status"] = "error";
            $response["message"] = $error;

            $wasSuccessful = false;
            break;
        }
    }

    if ($wasSuccessful) {
        $response["status"] = "success";
        $response["message"] = "Data uploaded successfully";
    }

} catch (Exception $ex) {
    $response["status"] = "error";
    $response['message'] = "Uploading data: $ex";
}

// Send a response to the requester
header('Access-Control-Allow-Origin: *');  // Allow requests from all sources
echo (json_encode($response));  // Return data as JSON

?>