<?php

include 'dbconnect.incl.php';

function getSalt($con, $epost) {
    $query = "SELECT salt FROM brukere WHERE email=?";
    $statement = $con->prepare($query);
    $statement->bind_param("s", $epost);
    $statement->execute();
    $statement->bind_result($salt);
    $statement->fetch();
    $statement->close();
    return $salt;
}

function login() {
    $con = connect();
    
    $epost = ($_POST['epost']);
    $salt = getSalt($con, $epost);
    $pw = hash('sha512', $salt . $_POST['passord']);
    //echo $epost . '<br />' . $salt . '<br />' . $pw;
    
    $query = "SELECT brukerID FROM brukere WHERE email=? AND passord=?";
    $statement = $con->prepare($query);
    $statement->bind_param("ss", $epost, $pw);
    $statement->execute();
    $statement->store_result();
    $statement->bind_result($brukerID);
    $statement->fetch();
    
    if ($statement->num_rows == 1) {
        session_start();
        $_SESSION['brukerID'] = $brukerID;
        if (disconnect($con) && $statement->close()) {
            echo "<meta http-equiv='refresh' content='0; url=./index.php' />";
            //header('Location: ./todo.php');
        }
    } else {
        echo '<br /><strong>Feil brukernavn eller passord.</strong>';
    }
}

