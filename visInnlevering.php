<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="stilark.css" />
        <title>Innlevering</title>
        <?php
        include 'service.incl.php';
        echo ensureLogin();

        if (isset($_POST['svar'])) {
            echo godkjennOving($_SESSION['brukerTilVurdering'], $_SESSION['ovingsID'], $_POST['godkjent']);
        }

        $ovingsID = $_GET['ovingsID'];
        $_SESSION['ovingsID'] = $ovingsID;
        $oving = getOving($ovingsID);
        $tilbakemeldinger = getTilbakemelding($brukerID);

        if (isset($_POST['lagre'])) {
            echo lagreNytteverdi($ovingsID, $tilbakemeldinger);
        }
        $brukerID = $_SESSION['brukerID'];
        if (getRolle() == 1) {
            $brukerID = $_GET['brukerTilVurdering'];
            $_SESSION['brukerTilVurdering'] = $brukerID;
        }
        $innlevering = getInnlevering($ovingsID, $brukerID);
        ?>
    </head>
    <body>
        <div id="wrapper">
            <?php
            echo '<h1>Din innlevering for ' . $oving['navn'] . '</h1>';
            echo "<h2>Status</h2>";
            $temp = new DateTime($innlevering['innleveringsdato']);
            $levert = $temp->format('d/m/Y');
            $utskrift = "<p>Besvarelse levert den $levert.</p><p>&Oslash;vingen er";
            if (!$innlevering['rettet']) {
                $utskrift = $utskrift . " ikke rettet.</p>";
            } else {
                if ($oving['godkjent']) {
                    $utskrift = $utskrift . ' godkjent.</p>';
                } else {
                    $utskrift = $utskrift . ' ikke godkjent.</p>';
                }
            }
            echo $utskrift;

            echo "<h2>Oppgavebeskrivelse</h2>";
            echo '<p>' . $oving['oppgavetekst'] . '</p>';

            echo '<h2>Besvarelse</h2>';
            echo '<p>' . htmlspecialchars($innlevering['innlevering'], ENT_SUBSTITUTE) . '</p>';
            echo '';

            echo "<h2>Tilbakemeldinger</h2>";
            echo "<form method='POST'> ";
            echo "<table>";
            echo "<thead>"
            . "<th>Tilbakemelding</th>"
            . "<th>Resultat</th>"
            . "<th>Nytteverdi</th>"
            . "</thead>";
            for ($i = 0; $i < count($tilbakemeldinger); $i++) {
                if ($tilbakemeldinger[$i]['tilbakemelding'] != null || $tilbakemeldinger[$i]['tilbakemelding'] != '') {
                    $utskrift = "<tr><td>" . htmlspecialchars($tilbakemeldinger[$i]['tilbakemelding'], ENT_SUBSTITUTE);
                    if ($tilbakemeldinger[$i]['godkjent']) {
                        $utskrift = $utskrift . "</td><td>Resultat: Godkjent</td>";
                    } else {
                        $utskrift = $utskrift . "</td><td>Resultat: Ikke godkjent</td>";
                    }
                    echo $utskrift;

                    if (getRolle() == 0) {

                        echo "<td><select name='nytteverdi$i'>"
                        . "<option value=0>Velg nytteverdi</option>"
                        . "<option value=1>Lite nyttig</option>"
                        . "<option value=2>Nyttig</option>"
                        . "<option value=3>Meget nyttig</option>"
                        . "</select></td>"
                        . "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Ingen tilbakemelding</td></tr>";
                }
            }
            echo "<tfoot><tr><td colspan='3'><input type='submit' value='Lagre' name='lagre' /></td></tr></tfoot>";
            echo "</table>";
            echo "</form>";
            if (getRolle() == 1) {
                echo "<form method='POST'>"
                . "<select name='godkjent'>"
                . "<option value='1'>Godkjent</option>"
                . "<option value='0'>Ikke godkjent</option>"
                . "</select>"
                . "<input type='submit' value='Rett øving' name='svar'>"
                . "</form>";
            }
            ?>
        </div>
    </body>
</html>
