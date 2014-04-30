<?php

function connect() {
    $host = "sql2.freesqldatabase.com";
    $user = "sql238446";
    $pw = "mS4*eC9*";
    $db_name = "sql238446";
    $con = mysqli_connect($host, $user, $pw) or die("Cannot connect to host: " . mysqli_error($con));
    mysqli_select_db($con, $db_name) or die("Cannot connect to Database: " . mysqli_error($con));
    mysqli_set_charset($con, 'utf-8');
    return $con;
}

function disconnect($con) {
    return mysqli_close($con) or die("Could not close connection to database: " . mysqli_error($con));
}