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
    while ($array = mysqli_fetch_assoc($result)) {
        $utskrift = "<tr><td colspan='5' id='" . $array['ovingsID'] . "'";
        if ($array['obligatorisk']) {
            $utskrift = $utskrift . " class=obligatorisk";
        }
        $utskrift = $utskrift . "><a href='visOving.php?id=" . $array['ovingsID'] . "'>" . $array['navn'] . "</a></td></tr>";
        echo $utskrift;
    }
    disconnect($con);
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
    
    $brukerID = $_POST['brukerID'];
    $ovingsID = $_POST['ovingsID'];
    
    $query = "SELECT * FROM innleveringer WHERE brukerID=? AND ovingsID=? ORDER BY innleveringsdato";
    $statement = $con->prepare($query);
    $statement->bind_param("ii", $brukerID, $ovingsID);
    if ($statement->execute()) {
        while ($row = $statement->fetch()) {
            print_r($row);
        }
    }
    $statement->close();
    disconnect($con);
}

