<?php

/*
 * (C) STEM Loyola 2021. Part of Project #04
 * 
 * This is a private script that parses the configuration file and establishes
 * a connection to the database
 */

// Resource: PDO Tutorial: https://phpdelusions.net/pdo

// Load database configurations
$configs = parse_ini_file (__DIR__."/config.ini", false);

// Assemble connection details
$settings = array (
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
                PDO::ATTR_PERSISTENT => false, 
            );
$dataSource = "mysql:host=".$configs["HOSTNAME"].";dbname=".$configs["DATABASE"].";charset=utf8";

// Establish database connection
$connection = null;
try {
    $connection = new PDO ($dataSource, $configs["USERNAME"], $configs["PASSWORD"], $settings);
    //echo("Connected...");
} catch (PDOException $ex) {
    $connection = null;
    //echo("[ERROR] $ex");
}

?>