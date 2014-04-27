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
    $pw = hash("sha512", salt() . $passord);
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

    $navn = $_POST['navn'];
    $oppgavetekst = $_POST['oppgavetekst'];
    // Må være på formen YYYY-MM-DD
    $innleveringsfrist = $_POST['innleveringsfrist'];
    $obligatorisk = $_POST['obligatorisk'];

    $query = "INSERT INTO ovinger VALUES(DEFAULT,?,?,?,?)";
    $statement = $con->prepare($query);
    // OBS! Sjekk at databasen aksepterer en int som boolean-input.
    $statement->bind_param("sssi", $navn, $oppgavetekst, $innleveringsfrist, $obligatorisk);
    $statement->execute();

    if (disconnect($con) && $statement->close()) {
        return true;
    }
}

function leverOving($ovingsID) {
    $con = connect();

    $brukerID = $_SESSION['brukerID'];
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

    $query = "INSERT INTO tilbakemeldinger VALUES(?,?,?,?,DEFAULT))";
    $statement = $con->prepare($query);
    $statement->bind_param("iiisi", $brukerID, $ovingsID, $vurderingsbruker, $tilbakemelding);
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

function getOvinger() {
    $con = connect();

    $query = "SELECT * FROM ovinger ORDER BY innleveringsfrist";
    $result = mysqli_query($con, $query);
    $antall = mysqli_num_rows($result);
    $array = [];
    for ($i = 0; $i < $antall; $i++) {
        $assoc = mysqli_fetch_assoc($result);
        $array[$i] = $assoc;
    }
    $result->close();
    disconnect($con);
    //print_r($array);
    return $array;
}

function getOving($ovingsID) {
    $con = connect();

    $query = "SELECT navn, oppgavetekst, innleveringsfrist, obligatorisk FROM ovinger WHERE ovingsID=?";
    $statement = $con->prepare($query);
    $statement->bind_param("i", $ovingsID);
    if ($statement->execute()) {
        $statement->bind_result($navn, $oppgavetekst, $innleveringsfrist, $obligatorisk);
        $statement->fetch();
        $statement->close();
        disconnect($con);
        $assoc = [
            'navn' => $navn,
            'oppgavetekst' => $oppgavetekst,
            'innleveringsfrist' => $innleveringsfrist,
            'obligatorisk' => $obligatorisk];
        return $assoc;
    }
}

function getInnleveringer() {
    $con = connect();

    $brukerID = $_GET['brukerID'];

    $query = "SELECT ovinger.ovingsID, innleveringer.brukerID "
            . "FROM innleveringer RIGHT OUTER JOIN ovinger ON ovinger.ovingsID = innleveringer.ovingsID "
            . "AND brukerID=? ORDER BY ovinger.innleveringsfrist";

    $statement = $con->prepare($query);
    $statement->bind_param("i", $brukerID);
    $array = [];
    
    if ($statement->execute()) {
        $statement->store_result();
        $statement->bind_result($ovingsID, $brukerID_levert);
        for ($i = 0; $i < $statement->num_rows; $i++) {
            $statement->fetch();
            $array[$i] = [
                'ovingsID' => $ovingsID,
                'brukerID_levert' => $brukerID_levert
            ];
        }
    }
    
    $statement->close();
    disconnect($con);
    //print_r($array);
    return $array;
}

function getInnlevering($ovingsID) {
    $con = connect();

    // OBS! Endre tilbake til $_SESSION etter testing.
    $brukerID = $_GET['brukerID'];

    $query = "SELECT innlevering, innleveringsdato, godkjent FROM innleveringer WHERE brukerID=? AND ovingsID=? ORDER BY innleveringsdato";
    $statement = $con->prepare($query);
    $statement->bind_param("ii", $brukerID, $ovingsID);
    if ($statement->execute()) {
        $statement->bind_result($innlevering, $innleveringsdato, $godkjent);
        $statement->fetch();
        $statement->close();
        disconnect($con);
        $assoc = [
            'innlevering' => $innlevering,
            'innleveringsdato' => $innleveringsdato,
            'godkjent' => $godkjent];
        return $assoc;
    }
}