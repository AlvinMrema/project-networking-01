<?php

/*
 * (C) STEM Loyola 2021. Part of Project #04
 * 
 * This script fetches relevant data and renders the landing page
 */

try {
    // Randomly select the default region
    $regions = array(
                "Dar es Salaam", "Dodoma", "Arusha", "Mwanza", "Mbeya", 
                "Tanga", "Morogoro", "Zanzibar Urban/West", "Rukwa", "Kilimanjaro",
                "Kigoma", "Tabora", "Iringa", "Shinyanga", "Kagera", "Mara",
                "Ruvuma", "Mtwara", "Katavi", "Singida", "Pemba South", "Njombe",
                "Lindi", "Manyara", "Pemba North", "Coast", "Simiyu", "Geita",
                "Zanzibar North", "Zanzibar Central/South"
               );
    
    $region = $regions[array_rand($regions)];

    // Fetch all cities for the region
    $url = "https://demos.stemloyola.org/coder/amrema/api/get-regional-cities.php?region=$region"; // TODO: Replace ACCOUNT with your account username
    $url = str_replace(" ", "+", $url); // Replace any spaces found within the region's string of the url with '+' symbol

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
    <meta name="description" content="Web startup project challenge">
    <meta name="author" content="STEAM Team">

    <title>PROJ-04 | Home</title>

    <!-- Vendor scripts -->
    <link href="libs/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap styles -->

    <!-- Our custome styles -->
    <link href="assets/css/main.css" rel="stylesheet">
  </head>
  <body>
    <!-- Top navigation bar -->
    <nav class="navbar justify-content-center navbar-expand navbar-dark bg-dark fixed-top">
      <span class="navbar-brand mr-auto"><img src="assets/images/logo.png" width="30" height="30" class="d-inline-block align-top" alt="" loading="lazy"> Networking Lab 1</span>

      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="search.php">Search</a>
        </li>
      </ul>
    </nav>

    <div class="container">
        <!-- City header -->
        <main role="main" class="container">
        <div class="home-page text-center mt-5">
            <?php $region = ucwords($region); // Ensure proper capitalization ?>
            <h1><?= "Cities in the $region region" ?></h1>
            <?php if ($cities != null && count($cities) > 0) { ?>
                <?php if (count($cities) == 1) { ?>
                <p class="lead">Found <?= count($cities); ?> city!</p>
                <?php } else { ?>
                <p class="lead">Found <?= count($cities); ?> cities!</p>
                <?php } ?>
            <?php } else { ?>
            <p class="lead">No cities found!</p>
            <?php } ?>
        </div>
        </main>

        <!-- Cities table -->
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">City</th>
                    <th scope="col">Latitude</th>
                    <th scope="col">Longitude</th>
                    <th scope="col">Population</th>
                </tr>
            </thead>
            <tbody>
            <?php $row = 1; ?>
            <?php foreach ($cities as $city): ?>
                <tr>
                    <td scope="row"><?= $row++ ?></td>
                    <td><?= $city["city"] ?></td>
                    <td><?= $city["latitude"] ?></td>
                    <td><?= $city["longitude"] ?></td>
                    <td><?= $city["population"] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table> 

    </div>

    <!-- Footer section -->
    <footer class="fixed-bottom text-center">
      <div class="inner">
        <p>Copyright © 2021 STEM Loyola. All Rights Reserved</p>
      </div>
    </footer>

    <!-- Vendor scripts -->
    <script src="libs/js/jquery-3.6.0.slim.min.js"></script> <!-- required for Bootstrap -->
    <script src="libs/js/bootstrap.bundle.min.js"></script> <!-- Bootstrap library -->
  </body>
</html>
