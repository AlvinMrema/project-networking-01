<?php

/*
 * (C) STEM Loyola 2021. Part of Project #04
 * 
 * This script fetches relevant data and renders the landing page
 */

try {
    $region = "Mbeya";   // Set the default region
    if (isset($_GET["region"])) $region = htmlentities($_GET["region"]);  // Extract the requested region if available

    // Fetch all cities for the region
    $url = "https://demos.stemloyola.org/coder/fsowani/api/get-regional-cities.php?region=$region"; // TODO: Call your get-regional-cities.php
    $data = file_get_contents($url); 
    $data = json_decode($data, true);

    $cities = null;
    if ($data["status"] == "success") $cities = $data["cities"];

} catch (Exception $ex) {
    echo($ex);
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>STEM Loyola | Project 04</title> 
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
    </style>  
</head>
<body>
    <h2><?= "Cities in $region" ?></h2>
    <table>
        <tr>
            <th>City</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Population</th>
        </tr>
        <?php foreach ($cities as $city): ?>
        <tr>
            <td><?= $city["city"]?></td>
            <td><?= $city["latitude"]?></td>
            <td><?= $city["longitude"]?></td>
            <td><?= $city["population"]?></td>
        </tr>
        <?php endforeach; ?>
    </table> 
</body>
</html>

