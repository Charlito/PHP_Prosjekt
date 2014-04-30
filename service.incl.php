<?php

session_start();

include 'dbconnect.incl.php';

date_default_timezone_set('Europe/Oslo');

function sjekkOmAdmin() {
    $rolle = getRolle();
    if ($rolle != 1) {
        echo "<meta http-equiv='refresh' content='0; url=./index.php' />";
    }
}

function ensureLogin() {
    if (!isset($_SESSION['brukerID'])) {
        $_SESSION['error'] = "<div class='info'><p>Vennligst logg inn.</p></div>";
        return "<meta http-equiv='refresh' content='0; url=./login.php'";
    }

    if (isset($_SESSION['sist_aktiv']) && (time() - $_SESSION['sist_aktiv'] > 3600)) {
        session_destroy();
        session_unset();
        session_start();
        $_SESSION['error'] = "<div class='info'><p>Du var inaktiv for lenge, sesjonen er avsluttet.</p></div>";
        return "<meta http-equiv='refresh' content='0; url=./login.php'";
    }

    $_SESSION['sist_aktiv'] = time();
}

function adminMeny() {
    echo '<nav>';
    echo '<ul class="nav" id="nav">';
    $headers = ['Registrer bruker', 'Legg til øving', 'Vis oversikt'];
    $linker = ['registrerBruker', 'registrerOving', 'visOversikt'];
    for ($i = 0; $i < count($linker); $i++) {
        if ($i == count($linker) - 1) {
            $ovinger = getOvinger();
            echo "<li><a href='$linker[$i].php'>$headers[$i]</a><ul>";
            for ($j = 0; $j < count($ovinger); $j++) {
                echo "<li><a href='visOvingOversikt.php?ovingsID=" . $ovinger[$j]['ovingsID'] . "'>" . $ovinger[$j]['navn'] . "</a></li>";
            }
            echo "</ul></li>";
        } else {
            echo "<li><a href='$linker[$i].php'>$headers[$i]</a></li>";
        }
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

function getBruker() {
    $con = connect();

    $brukerID = $_SESSION['brukerID'];

    $query = "SELECT email, navn, rolle FROM brukere WHERE brukerID=?";
    $statement = $con->prepare($query);
    $statement->bind_param("i", $brukerID);
    $assoc = [];
    if ($statement->execute()) {
        $statement->bind_result($email, $navn, $rolle);
        $statement->fetch();
        $assoc = [
            'email' => $email,
            'navn' => $navn,
            'rolle' => $rolle
        ];
        $statement->close();
        disconnect($con);
        return $assoc;
    }
    $statement->close();
    disconnect($con);
    echo "<div class='error'><p>Kunne ikke hente bruker.</p></div>";
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
        return "<div class='info'><p>Sjekk at passordene du skrev stemmer overens.</p></div>";
    }
    $pw = hash("sha512", $salt . $passord);
    $navn = $_POST['navn'];
    if (trim($epost) == null || $epost == '' || trim($navn) == null || $navn == '') {
        return "<div class='warning'><p>Ingen av feltene kan v&aelig;re tomme.</p></div>";
    }
    $rolle = intval($_POST['rolle']);

    $query = "INSERT INTO brukere (brukerID,email,navn,passord,salt,rolle) VALUES (DEFAULT,?,?,?,?,?)";
    $statement = $con->prepare($query);
    $statement->bind_param("ssssi", $epost, $navn, $pw, $salt, $rolle);
    $statement->execute();

    $brukerID = $statement->insert_id;

    $statement->close();
    disconnect($con);

    return "<div class='success'><p>La til bruker " . $brukerID . ", med navn: " . $navn . "</p></div>";
}

function antallDagerTilFrist($innleveringsfrist) {
    $temp = new DateTime($innleveringsfrist);
    $today = new DateTime('-1day');
    $differanse = $today->diff($temp);
    if ($differanse->invert) {
        return -1;
    }
    return $differanse->days;
}

function leggTilOving() {
    $con = connect();

    $navn = $_POST['navn'];
    $oppgavetekst = $_POST['oppgavetekst'];
    if (trim($navn) == null || trim($navn) == '' || trim($oppgavetekst) == null || trim($oppgavetekst) == '' || $_POST['innleveringsfrist'] == null) {
        return "<div class='warning'><p>Ingen av feltene kan være tomme</p></div>";
    }

    $innleveringsfristInput = new DateTime($_POST['innleveringsfrist']);
    // Sjekker om datoen er gyldig
    if (new DateTime('-1day') > $innleveringsfristInput) {
        return "<div class='info'><p>Datoen er ugyldig.</p></div>";
    }
    // Formaterer datoen for lagring i databasen
    $innleveringsfrist = $innleveringsfristInput->format('Y-m-d');
    // Sørger for at en verdi blir satt for boolean-verdi obligatorisk for databasen
    $obligatorisk = 0;
    if (isset($_POST['obligatorisk'])) {
        $obligatorisk = 1;
    }

    $query = "INSERT INTO ovinger VALUES(DEFAULT,?,?,?,?)";
    $statement = $con->prepare($query);
    // OBS! Sjekk at databasen aksepterer en int som boolean-input.
    $statement->bind_param("sssi", $navn, $oppgavetekst, $innleveringsfrist, $obligatorisk);
    $statement->execute();

    if (disconnect($con) && $statement->close()) {
        return "<div class='success'><p>La til øving med navn: " . $navn . "</p></div>";
    }
}

function leverOving($ovingsID) {
    $con = connect();

    $brukerID = $_SESSION['brukerID'];
    $innlevering = $_POST['innlevering'];
    if (trim($innlevering) == null || trim($innlevering) == '') {
        return "<div class='error'><p>Innleveringen kan ikke være tom!</p></div>";
    }

    $query = "INSERT INTO innleveringer VALUES(?,?,?,DEFAULT,DEFAULT,DEFAULT)";
    $statement = $con->prepare($query);
    $statement->bind_param("iis", $brukerID, $ovingsID, $innlevering);
    if ($statement->execute()) {
        $statement->close();
        disconnect($con);
        return "<meta http-equiv='refresh' content='0; url=./todo.php' />";
    }
    $statement->close();
    disconnect($con);
    return "<div class='info'><p>Kunne ikke levere &oslash;vingen, prøv igjen senere</p></div>";
}

function leverTilbakemelding() {
    $con = connect();

    $brukerID = $_SESSION['brukerTilVurdering'];
    $ovingsID = $_SESSION['ovingsID'];
    $vurderingsbruker = $_SESSION['brukerID'];
    $tilbakemelding = $_POST['tilbakemelding'];
    $godkjent = $_POST['godkjent'];
    if (str_word_count($tilbakemelding) < 20) {
        return "<div class='warning'><p>Tilbakemeldingen må være minimum 20 ord. (Du skrev "
                . str_word_count($tilbakemelding) . ")</p></div>";
    }
    //echo "Innlogget bruker: $vurderingsbruker, ovingsID: $ovingsID, bruker til vurdering: $brukerID, tilbakemelding: $tilbakemelding";

    $query = "INSERT INTO tilbakemeldinger VALUES(?,?,?,?,?,DEFAULT)";
    $statement = $con->prepare($query);
    $statement->bind_param("iiisi", $brukerID, $ovingsID, $vurderingsbruker, $tilbakemelding, $godkjent);
    if ($statement->execute()) {
        $statement->close();
        disconnect($con);
        return "<meta http-equiv='refresh' content='0; url=./index.php' />";
    }
    $statement->close();
    disconnect($con);
    return "<div class='error'><p>Kunne ikke levere tilbakemeldingen, vennligst prøv senere.</p></div>";
}

function godkjennOving($brukerID, $ovingsID, $godkjent) {
    $con = connect();

    $query = "UPDATE innleveringer SET godkjent=? WHERE brukerID=? AND ovingsID=?";
    $statement = $con->prepare($query);
    $statement->bind_param("iii",$godkjent, $brukerID, $ovingsID);
    $statement->execute();

    if (disconnect($con) && $statement->close()) {
        return "<div class='success'><p>Øvingen er nå rettet.</p></div>";
    }
}

function slettOving() {
    $con = connect();

    $ovingsID = $_GET['ovingsID'];

    $query = "DELETE FROM ovinger WHERE ovingsID=?";

    $statement = $con->prepare($query);
    $statement->bind_param("i", $ovingsID);
    if ($statement->execute()) {
        $statement->close();
        disconnect($con);
        return "<div class='success'><p>Øvingen er ble slettet.</p></div>";
    }
    $statement->close();
    disconnect($con);
    return "<div class='error'><p>Øvingen kunne ikke slettes.</p></div>";
}

function getSpesifikkTilbakemelding($brukerTilVurdering, $ovingsID) {
    $con = connect();

    $brukerID = $_SESSION['brukerID'];

    $query = "SELECT tilbakemelding, godkjent, nytteverdi FROM tilbakemeldinger "
            . "WHERE brukerID=? AND ovingsID=? AND vurderingsbruker=?";
    $statement = $con->prepare($query);
    $statement->bind_param("iii", $brukerTilVurdering, $ovingsID, $brukerID);
    if ($statement->execute()) {
        $statement->bind_result($tilbakemelding, $godkjent, $nytteverdi);
        $statement->fetch();
        $assoc = [
            'tilbakemelding' => $tilbakemelding,
            'godkjent' => $godkjent,
            'nytteverdi' => $nytteverdi
        ];
        $statement->close();
        disconnect($con);
        return $assoc;
    }
    $statement->close();
    disconnect($con);
    echo "<div class='error'><p>Kunne ikke hente tilbakemelding.</p></div>";
}

function getTilbakemelding() {
    $con = connect();

    // OBS! Husk å endre tilbake til $_SESSION etter testing.
    $brukerID = $_SESSION['brukerID'];
    $ovingsID = $_SESSION['ovingsID'];

    $query = "SELECT brukerID, ovingsID, "
            . "tilbakemelding, godkjent FROM tilbakemeldinger WHERE "
            . "brukerID=? AND ovingsID=?";

    $statement = $con->prepare($query);
    $statement->bind_param("ii", $brukerID, $ovingsID);
    $array = [NULL, NULL, NULL];
    if ($statement->execute()) {
        $statement->store_result();
        $statement->bind_result($brukerID, $ovingsID, $tilbakemelding, $godkjent);
        for ($i = 0; $i < $statement->num_rows; $i++) {
            $statement->fetch();
            $array[$i] = [
                'brukerID' => $brukerID,
                'ovingsID' => $ovingsID,
                'tilbakemelding' => htmlspecialchars($tilbakemelding, ENT_SUBSTITUTE),
                'godkjent' => $godkjent];
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

function getOvingerInfo() {
    $ovinger = getOvinger();
    $ovingsID;
    $con = connect();
    $query1 = "select count(*) as manglendeTilbakemelding from "
            . "(select innleveringer.brukerID,count(tilbakemeldinger.brukerID) "
            . "from tilbakemeldinger "
            . "right outer join innleveringer on "
            . "tilbakemeldinger.brukerID = innleveringer.brukerID "
            . "where innleveringer.ovingsID=? and innleveringer.godkjent=0 "
            . "group by brukerID having count(*) < 3) as A";
    $query2 = "select count(*) as antInnlevering, sum(godkjent) as antGodkjent "
            . "from innleveringer where ovingsID=?";
    $statement1 = $con->prepare($query1);
    $statement1->bind_param("i", $ovingsID);
    $statement2 = $con->prepare($query2);
    $statement2->bind_param("i", $ovingsID);
    //$query = "SELECT * FROM ovinger ORDER BY innleveringsfrist";
    //$result = mysqli_query($con, $query);
    //$antall = mysqli_num_rows($result);
    $array = [];
    for ($i = 0; $i < count($ovinger); $i++) {
        $ovingsID = $ovinger[$i]['ovingsID'];
        $statement1->execute();
        $statement1->store_result();
        $statement1->bind_result($manglendeTilbakemelding);
        $statement1->fetch();
        $statement2->execute();
        $statement2->store_result();
        $statement2->bind_result($antInnleveringer, $antGodkjente);
        $statement2->fetch();
        $array[$i] = [
            'manglendeTilbakemelding' => $manglendeTilbakemelding,
            'antInnleveringer' => $antInnleveringer,
            'antGodkjente' => $antGodkjente
        ];
    }
    //erstatte null verdier med 0:
    for ($i = 0; $i < count($array); $i++) {
        if ($array[$i]['manglendeTilbakemelding'] == null) {
            $array[$i]['manglendeTilbakemelding'] = 0;
        }
        if ($array[$i]['antInnleveringer'] == null) {
            $array[$i]['antInnleveringer'] = 0;
        }
        if ($array[$i]['antGodkjente'] == null) {
            $array[$i]['antGodkjente'] = 0;
        }
    }

    $statement1->close();
    $statement2->close();
    disconnect($con);
    //print_r($array);
    return $array;
}

function getBrukereOving($ovingsID) {
    $con = connect();
    $query = "select * from("
            . "(select brukerID, navn from brukere where rolle=0) as A "
            . "left outer join "
            . "(select count(*) as levert, godkjent, brukerID from innleveringer where ovingsID=? group by brukerID) as B on A.brukerID = B.brukerID  "
            . "left outer join "
            . "(select count(*) as gittTilbakemeldinger, vurderingsbruker from tilbakemeldinger where ovingsID=? group by vurderingsbruker) as C on A.brukerID = C.vurderingsbruker "
            . "left outer join "
            . "(select count(*) as faattTilbakemeldinger, brukerID from tilbakemeldinger where ovingsID=? group by brukerID) as D on A.brukerID = D.brukerID)";

    $statement = $con->prepare($query);
    $statement->bind_param("iii", $ovingsID, $ovingsID, $ovingsID);
    $statement->execute();
    $statement->store_result();
    $array = [];
    $statement->bind_result($brukerID, $navn, $levert, $godkjent, $null, $gittTilbakemeldinger, $null, $fåttTilbakemeldinger, $null);
    for ($i = 0; $i < $statement->num_rows; $i++) {
        $statement->fetch();
        $array[$i] = [
            'brukerID' => $brukerID,
            'navn' => $navn,
            'levert' => $levert,
            'gittTilbakemeldinger' => $gittTilbakemeldinger,
            'fåttTilbakemeldinger' => $fåttTilbakemeldinger,
            'godkjent' => $godkjent
        ];
    }
    $statement->close();
    disconnect($con);
    return $array;
}

function getStudenter() {
    
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

    $query = "SELECT innlevering, innleveringsdato, rettet, godkjent FROM innleveringer WHERE brukerID=? AND ovingsID=? ORDER BY innleveringsdato";
    $statement = $con->prepare($query);
    $statement->bind_param("ii", $brukerID, $ovingsID);
    if ($statement->execute()) {
        $statement->bind_result($innlevering, $innleveringsdato, $rettet, $godkjent);
        $statement->fetch();
        $statement->close();
        disconnect($con);
        $assoc = [
            'innlevering' => htmlspecialchars($innlevering, ENT_SUBSTITUTE),
            'innleveringsdato' => $innleveringsdato,
            'rettet' => $rettet,
            'godkjent' => $godkjent];
        return $assoc;
    }
}
