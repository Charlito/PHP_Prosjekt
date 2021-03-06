<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
        <title>Tilbakemelding</title>
        <link rel="stylesheet" href="stilark.css" />
        <?php
        include 'service.incl.php';
        echo ensureLogin();
        ?>
    </head>
    <body>
        <?php
        $rolle = getRolle();
        if ($rolle == 1) {
            adminMeny();
        }
        ?>
        <div id="wrapper">
            <?php
            $brukerID = $_SESSION['brukerID'];
            $ovingsID = $_GET['ovingsID'];
            $oving = getOving($ovingsID);
            $innleveringer = getInnleveringerForVurdering($ovingsID);
            //print_r($innleveringer);
            if ($rolle == 0) {
                $brukerTilVurdering = $innleveringer[0]['brukerID'];
                $_SESSION['brukerTilVurdering'] = $brukerTilVurdering;
            } else {
                if (isset($_GET['brukerTilVurdering'])) {
                    $brukerTilVurdering = $_GET['brukerTilVurdering'];
                    $_SESSION['brukerTilVurdering'] = $brukerTilVurdering;
                }
            }
            
            
            $_SESSION['ovingsID'] = $ovingsID;

            $innlevering = getInnlevering($innleveringer[0]['ovingsID'], $brukerTilVurdering);



            if ($brukerTilVurdering != null || $brukerTilVurdering != '') {
                echo "<h1>Retting av student nr $brukerTilVurdering, " . $oving['navn'] . "</h1>";
            } else {
                echo "<h1>Retting av " . $oving['navn'] . "</h1>";
            }
            //Skriv ut øvinga
            echo "<h2>Oppgavetekst</h2>";
            echo "<p>" . $oving['oppgavetekst'] . "<p>";
            echo "<h2>Studentbesvarelse</h2>";
            if ($innlevering['innlevering'] != null || $innlevering['innlevering'] != '') {
                echo "<p>" . $innlevering['innlevering'] . "</p>";
                echo "<h2>Din tilbakemelding til student " . $brukerTilVurdering . " (du selv nr " . $brukerID . ")</h2>";
            } else {
                echo "<p>Det er ingen passende studentbesvarelser &aring; vurdere akkurat n&aring;.</p>";
                echo "<h2>Din tilbakemelding: </h2>";
            }
            ?>
            <form method="POST" action="tilbakemeldingOving.php?ovingsID=
                <?php 
                echo $ovingsID;
                if ($rolle == 1) {
                    echo "&brukerTilVurdering=$brukerTilVurdering";
                }
                ?>">
                <textarea id="tilbakemelding" name="tilbakemelding"></textarea>

                <select name="godkjent">
                    <option value="1">Godkjent</option>
                    <option value="0">Ikke godkjent</option>
                </select>
                <input type="submit" value="Gi tilbakemelding" name="submit">
            </form>
            <?php
            if (isset($_POST['submit'])) {
                echo leverTilbakemelding();
            }
            ?>
        </div>
    </body>
</html>