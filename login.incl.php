<?php

include 'dbconnect.incl.php';

if(isset($_POST['login'])){
    $con = connect();
    
    $epost = real_escape_string($_POST['epost']);
    $pw = crypt(real_escape_string($_POST['passord']));
    echo $pw;
    
    $query = "SELECT brukerID FROM brukere WHERE epost = ? AND passord = ?";
    
    $statement = $con->prepare($query);
    $statement->bind_param("ss", $epost, $pw);
    $statement->execute();
    $statement->store_result();
    $statement->bind_result($brukerID);
    
    if ($statement->num_rows == 1) {
        session_start();
        $_SESSION['brukerID'] = $brukerID;
        if (disconnect($con)) {
            header('Location: /PHP_Prosjekt/index.php');
        }
    } else {
        echo 'Feil brukernavn eller passord.';
    }
}