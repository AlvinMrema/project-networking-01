<?php

/*
 * (C) STEM Loyola 2021. Part of Project #04
 * 
 * This is a public script that uses the private scripts (DB middleware) to
 * fetch all cities for a given region
 */

// Require our custom database library
require (__DIR__."/../../db/lib-cities.php");

if (isset($_GET["region"])) {
    // Extract the region from the GET request
    $region = htmlentities($_GET["region"]);

    // Fetch relevant cities from the database
    $response = array();  // Data that'll be sent back
    $cities = array();

    $error = null;
    $isSuccessful = getCitiesWithinRegion($region, $cities, $error);

    if ($isSuccessful) {
        $response["status"] = "success";
        $response["cities"] = $cities;
    } else {
        $response["status"] = "error";
        $response["message"] = $error;
    }
} else {
    $response["status"] = "error";
    $response['message'] = "Invalid URL ($_SERVER[REQUEST_SCHEME]//$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]). Must specify region";
}

// Send a response to the requester
header("Access-Control-Allow-Origin: *");  // Allow requests from all sources
echo (json_encode($response));  // Return data as JSON

?>