<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="stilark.css" />
        <title>Din tilbakemelding</title>
        <?php
        include 'service.incl.php';
        echo ensureLogin();
        ?>
    </head>
    <body>
        <div id="wrapper">
            
            <?php
            $brukerID = $_SESSION['brukerID'];
            $ovingsID = $_GET['ovingsID'];
            $brukerTilVurdering = $_GET['brukerID'];
            //echo "<p>Innlogget bruker: $brukerID, &oslash;ving: $ovingsID, tilbakemelding til bruker $brukerTilVurdering</p>";
            $oving = getOving($ovingsID);
            $innlevering = getInnlevering($ovingsID, $brukerTilVurdering);
            $tilbakemelding = getSpesifikkTilbakemelding($brukerTilVurdering, $ovingsID);
            echo "<h1>Din tilbakemelding til " . $oving['navn'] . "</h1>";
            echo "<h2>Oppgavetekst</h2>";
            echo "<p>" . $oving['oppgavetekst'] . "</p>";
            echo "<h2>Besvarelse</h2>";
            echo "<p>" . htmlspecialchars($innlevering['innlevering'], ENT_SUBSTITUTE) . "</p>";
            echo "<h2>Din tilbakemelding</h2>";
            echo "<p>" . $tilbakemelding['tilbakemelding'] . "<br />Resultat: "; 
            if ($tilbakemelding['godkjent']) {
                echo "Godkjent</p>";
            } else {
                echo "Ikke godkjent</p>";
            }
            ?>

        </div>
    </body>
</html>
