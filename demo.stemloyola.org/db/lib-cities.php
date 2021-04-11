<?php

/*
 * (C) STEM Loyola 2021. Part of Project #04
 * 
 * This is a private script that defines functions that insert, delete, fetch
 * and update data in the database. Uses $connection defined in connect.php
 */

// Require database connection script
require (__DIR__."/connect.php");

// Database tables and columns names
// TODO: Create a table in your database as per "db-table-structure.png" structure.
//       Table name and column names must match the ones defined below
$TABLE_CITIES   = "tz_cities";
$COL_CITY       = "city";
$COL_REGION     = "region";
$COL_LATITUDE   = "latitude";
$COL_LONGITUDE  = "longitude";
$COL_POPULATION = "population";

/*
 * Insert a record into the cities table
 */
function addCity (&$city, &$error=null) {
    global $connection;
    global $TABLE_CITIES, $COL_CITY, $COL_REGION, $COL_LATITUDE, $COL_LONGITUDE, $COL_POPULATION;

    try {
        $query = "INSERT INTO $TABLE_CITIES".
                 "($COL_CITY, $COL_REGION, $COL_LATITUDE, $COL_LONGITUDE, $COL_POPULATION) ".
                 "VALUES".
                 "(:cty, :reg, :lat, :lon, :pop)";

        $statement = $connection->prepare($query);
        
        $statement->bindValue(":cty", $city["city"],       PDO::PARAM_STR);
        $statement->bindValue(":reg", $city["region"],     PDO::PARAM_STR);
        $statement->bindValue(":lat", $city["latitude"],   PDO::PARAM_STR);
        $statement->bindValue(":lon", $city["longitude"],  PDO::PARAM_STR);
        $statement->bindValue(":pop", $city["population"], PDO::PARAM_INT);
        
        $statement->execute();
        
        // Check if the query was executed successfully
        return ($statement->rowCount() > 0 ? true : false);
        
    } catch (PDOException $ex) {
        $error = $ex;
        return false;
    } catch (Exception $ex) {
        $error = $ex;
        return false;
    }
}


/*
 * Delete all data from the cities table
 */
function clearCitiesData (&$error=null) {
    global $connection;
    global $TABLE_CITIES;

    try {
        $query = "DELETE FROM $TABLE_CITIES";

        $statement = $connection->prepare($query);
        
        $statement->execute();
        
        // Check if the query was executed successfully
        return ($statement->rowCount() > 0 ? true : false);
        
    } catch (PDOException $ex) {
        $error = $ex;
        return false;
    } catch (Exception $ex) {
        $error = $ex;
        return false;
    }
}


/*
 * Loads all cities within a given region
 */
function getCitiesWithinRegion ($region, &$citiesList, &$error=null) {
    global $connection;
    global $TABLE_CITIES, $COL_CITY, $COL_REGION, $COL_LATITUDE, $COL_LONGITUDE, $COL_POPULATION;

    try {
        $query = "SELECT * FROM $TABLE_CITIES WHERE `$COL_REGION`=:reg";
        
        $statement = $connection->prepare($query);
        $statement->bindValue(":reg", $region, PDO::PARAM_STR);

        $statement->execute();
        
        if ($statement->rowCount() > 0){
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $city = array();

                $city["city"]       = $row[$COL_CITY];
                $city["region"]     = $row[$COL_REGION];
                $city["latitude"]   = $row[$COL_LATITUDE];
                $city["longitude"]  = $row[$COL_LONGITUDE];
                $city["population"] = $row[$COL_POPULATION];
                                
                array_push($citiesList, $city);
            }

        } else {
            $error = "No cities from '$region' were found";
            return false;
        }
    
        return true;
        
    } catch (PDOException $ex) {
        $error = $ex;
    } catch (Exception $ex) {
        $error = $ex;
        return false;
    }
}


// TODO: Define any additional functions as needed

?>