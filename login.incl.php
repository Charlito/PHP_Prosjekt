<?php

include 'dbconnect.incl.php';

function login() {
    $con = connect();
    
    $epost = ($_POST['epost']);
    $pw = hash('sha256', $_POST['passord']);
    echo $epost . '<br />' . $pw;
    
    $query = "SELECT brukerID FROM brukere WHERE email=? AND passord=?";
    $statement = $con->prepare($query);
    $statement->bind_param("ss", $epost, $pw);
    $statement->execute();
    $statement->store_result();
    $statement->bind_result($brukerID);
    
    if ($statement->num_rows == 1) {
        session_start();
        $_SESSION['brukerID'] = $brukerID;
        if (disconnect($con)) {
            header('Location: ./index.php');
        }
    } else {
        echo '<br /><strong>Feil brukernavn eller passord.</strong>';
    }
}

