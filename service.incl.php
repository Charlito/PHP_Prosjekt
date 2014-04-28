<?php

session_start();

include 'dbconnect.incl.php';

function sjekkOmAdmin() {
    $rolle = getRolle();
    if ($rolle != 1) {
        echo "<meta http-equiv='refresh' content='0; url=./index.php' />";
    }
}

function adminMeny() {
    echo '<nav>';
    echo '<ul class="nav" id="nav">';
    $headers = ['Registrer bruker', 'Legg til øving', 'Vis oversikt'];
    $linker = ['registrerBruker', 'registrerOving', 'visOversikt'];
    for ($i = 0; $i < count($linker); $i++) {
        echo "<li><a href='$linker[$i].php'>$headers[$i]</a></li>";
    }
    echo '</ul>';
    echo '</nav>';
}

function getRolle() {
    $con = connect();

    $brukerID = $_SESSION['brukerID'];

    $query = "SELECT rolle FROM brukere WHERE brukerID=?";
    $statement = $con->prepare($query);
    $statement->bind_param("i", $brukerID);
    $statement->execute();
    $statement->store_result();
    $statement->bind_result($rolle);
    $statement->fetch();

    $statement->close();

    return $rolle;
}


function salt() {
    return bin2hex(openssl_random_pseudo_bytes(8));
}

function leggTilBruker() {
    $con = connect();

    $epost = $_POST['epost'];
    $salt = salt();
    $passord = $_POST['passord'];
    $passordsjekk = $_POST['passordsjekk'];
    if ($passord != $passordsjekk || $passord == '' || trim($passord) == null) {
        return "<strong>Sjekk at passordene du skrev stemmer overens.</strong>";
    }
    $pw = hash("sha512", $salt . $passord);
    $navn = $_POST['navn'];
    if (trim($epost) == null || $epost == '' || trim($navn) == null || $navn == '') {
        return "<strong>Ingen av feltene kan v&aelig;re tomme,</strong>";
    }
    $rolle = intval($_POST['rolle']);

    $query = "INSERT INTO brukere (brukerID,email,navn,passord,salt,rolle) VALUES (DEFAULT,?,?,?,?,?)";
    $statement = $con->prepare($query);
    $statement->bind_param("ssssi", $epost, $navn, $pw, $salt, $rolle);
    $statement->execute();

    $brukerID = $statement->insert_id;

    $statement->close();
    disconnect($con);

    return "<p>La til bruker " . $brukerID . ", med navn: " . $navn . "</p>";
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
    if ($statement->execute()) {
        $statement->close();
        disconnect($con);
        return "<meta http-equiv='refresh' content='0; url=./todo.php' />";
    }
    $statement->close();
    disconnect($con);
    return "<strong>Kunne ikke levere &oslash;vingen, prøv igjen senere</strong>";
}

function leverTilbakemelding() {
    $con = connect();

    $brukerID = $_SESSION['brukerTilVurdering'];
    $ovingsID = $_SESSION['ovingsID'];
    $vurderingsbruker = $_SESSION['brukerID'];
    $tilbakemelding = $_POST['tilbakemelding'];
    //echo "Innlogget bruker: $vurderingsbruker, ovingsID: $ovingsID, bruker til vurdering: $brukerID, tilbakemelding: $tilbakemelding";

    $query = "INSERT INTO tilbakemeldinger VALUES(?,?,?,?,DEFAULT)";
    $statement = $con->prepare($query);
    $statement->bind_param("iiis", $brukerID, $ovingsID, $vurderingsbruker, $tilbakemelding);
    if ($statement->execute()) {
        $statement->close();
        disconnect($con);
        return "<meta http-equiv='refresh' content='0; url=./todo.php' />";
    }
    $statement->close();
    disconnect($con);
    return "<strong>Kunne ikke levere tilbakemeldingen, vennligst prøv senere.</strong>";
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

function getSpesifikkTilbakemelding($brukerTilVurdering, $ovingsID) {
    $con = connect();

    $brukerID = $_SESSION['brukerID'];

    $query = "SELECT tilbakemelding, nytteverdi FROM tilbakemeldinger "
            . "WHERE brukerID=? AND ovingsID=? AND vurderingsbruker=?";
    $statement = $con->prepare($query);
    $statement->bind_param("iii", $brukerTilVurdering, $ovingsID, $brukerID);
    if ($statement->execute()) {
        $statement->bind_result($tilbakemelding, $nytteverdi);
        $statement->fetch();
        $assoc = [
            'tilbakemelding' => $tilbakemelding,
            'nytteverdi' => $nytteverdi
        ];
        $statement->close();
        disconnect($con);
        return $assoc;
    }
    $statement->close();
    disconnect($con);
    echo "<strong>Kunne ikke hente tilbakemelding.</strong>";
}

function getTilbakemelding() {
    $con = connect();

    // OBS! Husk å endre tilbake til $_SESSION etter testing.
    $brukerID = $_SESSION['brukerID'];
    $ovingsID = $_SESSION['ovingsID'];

    $query = "SELECT tilbakemeldinger.brukerID, tilbakemeldinger.ovingsID, "
            . "tilbakemeldinger.tilbakemelding FROM tilbakemeldinger WHERE "
            . "tilbakemeldinger.brukerID=? AND tilbakemeldinger.ovingsID=?";

    $statement = $con->prepare($query);
    $statement->bind_param("ii", $brukerID, $ovingsID);
    $array = [NULL, NULL, NULL];
    if ($statement->execute()) {
        $statement->store_result();
        $statement->bind_result($brukerID, $ovingsID, $tilbakemelding);
        for ($i = 0; $i < $statement->num_rows; $i++) {
            $statement->fetch();
            $array[$i] = [
                'brukerID' => $brukerID,
                'ovingsID' => $ovingsID,
                'tilbakemelding' => $tilbakemelding];
        }
        $statement->close();
        disconnect($con);
        return $array;
    }
}

function getTilbakemeldinger($ovingsID, $brukerID) {
    $con = connect();
    $tilbakeMeldinger = [NULL, NULL, NULL];
    $query = "SELECT brukerID FROM tilbakemeldinger WHERE vurderingsbruker=? AND ovingsID=?";
    $statement = $con->prepare($query);
    $statement->bind_param("ii", $brukerID, $ovingsID);
    if ($statement->execute()) {
        $statement->store_result();
        $statement->bind_result($levertBruker);
        for ($i = 0; $i < $statement->num_rows; $i++) {
            $statement->fetch();
            $tilbakeMeldinger[$i] = $levertBruker;
        }
    }
    $statement->close();
    disconnect($con);
    return $tilbakeMeldinger;
}

function getAlleTilbakemeldinger() {
    $ovingArray = getOvinger();
    $ovingMTilbakemelding = [];

    for ($index = 0; $index < count($ovingArray); $index++) {
        $ovingMTilbakemelding[$index] = [$ovingArray[$index]['ovingsID']];
        $tilbakemeldinger = getTilbakemeldinger($ovingArray[$index]['ovingsID'], $_SESSION['brukerID']);
        for ($index2 = 1; $index2 < 4; $index2++) {
            array_push($ovingMTilbakemelding[$index], $tilbakemeldinger[$index2 - 1]);
        }
    }
    return $ovingMTilbakemelding;
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

    $brukerID = $_SESSION['brukerID'];

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

function getInnleveringerForVurdering($ovingsID) {
    $con = connect();

    $brukerID = $_SESSION['brukerID'];

    $query = "SELECT innleveringer.ovingsID, innleveringer.brukerID, "
            . "COUNT(tilbakemeldinger.brukerID) AS tilbakemeldinger "
            . "FROM innleveringer LEFT OUTER JOIN tilbakemeldinger "
            . "ON innleveringer.brukerID = tilbakemeldinger.brukerID "
            . "AND innleveringer.ovingsID = tilbakemeldinger.ovingsID "
            . "WHERE innleveringer.brukerID != ? "
            . "AND (vurderingsbruker != ? OR vurderingsbruker IS NULL) "
            . "AND innleveringer.ovingsID = ? GROUP BY tilbakemeldinger.ovingsID;";

    $statement = $con->prepare($query);
    $statement->bind_param("iii", $brukerID, $brukerID, $ovingsID);
    $array = [];

    if ($statement->execute()) {
        $statement->store_result();
        $statement->bind_result($ovingsID, $brukerID, $antallTilbakemeldinger);
        for ($i = 0; $i < $statement->num_rows; $i++) {
            $statement->fetch();
            $array[$i] = [
                'ovingsID' => $ovingsID,
                'brukerID' => $brukerID,
                'antallTilbakemeldinger' => $antallTilbakemeldinger
            ];
        }
    }

    $statement->close();
    disconnect($con);
    //print_r($array);
    return $array;
}

function getInnlevering($ovingsID, $brukerID) {
    $con = connect();

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
