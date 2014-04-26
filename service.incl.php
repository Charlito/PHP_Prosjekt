<?php

include 'dbconnect.incl.php';

function salt() {
    return bin2hex(openssl_random_pseudo_bytes(8));
}

function leggTilBruker() {
    $con = connect();

    $epost = $_POST['epost'];
    $salt = salt();
    echo $salt;
    $passord = $_POST['passord'];
    $passordsjekk = $_POST['passordsjekk'];
    if ($passord != $passordsjekk) {
        return false;
    }
    $pw = hash("sha512", salt() . $_POST['passord']);
    $navn = $_POST['navn'];
    $rolle = $_POST['rolle'];

    $query = "INSERT INTO brukere VALUES(DEFAULT,?,?,?,?,DEFAULT)";
    $statement = $con->prepare($query);
    $statement->bind_param("ssssi", $epost, $navn, $pw, $rolle);
    $statement->execute();

    if (disconnect($con) && $statement->close()) {
        return true;
    }
}

function leggTilOving() {
    $con = connect();

    $oppgavetekst = $_POST['oppgavetekst'];
    // Må være på formen YYYY-MM-DD
    $innleveringsfrist = $_POST['innleveringsfrist'];
    $obligatorisk = $_POST['obligatorisk'];

    $query = "INSERT INTO ovinger VALUES(DEFAULT,?,?,?)";
    $statement = $con->prepare($query);
    $statement->bind_param("ssb", $oppgavetekst, $innleveringsfrist, $obligatorisk);
    $statement->execute();

    if (disconnect($con) && $statement->close()) {
        return true;
    }
}

function leverOving() {
    $con = connect();

    $brukerID = $_POST['brukerID'];
    $ovingsID = $_POST['ovingsID'];
    $innlevering = $_POST['innlevering'];

    $query = "INSERT INTO innleveringer VALUES(?,?,?,DEFAULT,DEFAULT)";
    $statement = $con->prepare($query);
    $statement->bind_param("iis", $brukerID, $ovingsID, $innlevering);
    $statement->execute();

    if (disconnect($con) && $statement->close()) {
        return true;
    }
}

function leverTilbakemelding() {
    $con = connect();

    $brukerID = $_POST['brukerID'];
    $ovingsID = $_POST['ovingsID'];
    $vurderingsbruker = $_POST['vurderingsbruker'];
    $tilbakemelding = $_POST['tilbakemelding'];
    $nytteverdi = $_POST['nytteverdi'];

    $query = "INSERT INTO tilbakemeldinger VALUES(?,?,?,?,))";
    $statement = $con->prepare($query);
    $statement->bind_param("iiisi", $brukerID, $ovingsID, $vurderingsbruker, $tilbakemelding, $nytteverdi);
    $statement->execute();

    if (disconnect($con) && $statement->close()) {
        return true;
    }
}

function godkjennOving() {
    $con = connect();

    $brukerID = $_POST['brukerID'];
    $ovingsID = $_POST['ovingsID'];

    $query = "UPDATE innleveringer SET godkjent=true WHERE brukerID=? AND ovingsID=?";
    $statement = $con->prepare($query);
    $statement->bind_param("ii", $brukerID, $ovingsID);
    $statement->execute();

    if (disconnect($con) && $statement->close()) {
        return true;
    }
}
