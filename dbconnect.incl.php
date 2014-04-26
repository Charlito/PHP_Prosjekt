<?php

function connect() {
    $host = "localhost";
    $user = "root";
    $pw = "root";
    $db_name = "MOOC";
    $con = mysqli_connect($host, $user, $pw) or die("Cannot connect to host: " . mysqli_error($con));
    mysqli_select_db($con, $db_name) or die("Cannot connect to Database: " . mysqli_error($con));
    return $con;
}

function disconnect($con) {
    return mysqli_close($con) or die("Could not close connection to database: " . mysqli_error($con));
}