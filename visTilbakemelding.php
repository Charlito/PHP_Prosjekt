<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" value="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="stilark.css" />
        <title>Din tilbakemelding</title>
        <?php
        include 'service.incl.php';
        ?>
    </head>
    <body>
        <div id="wrapper">
            
            <?php
            $brukerID = $_SESSION['brukerID'];
            $ovingsID = $_GET['ovingsID'];
            $brukerTilVurdering = $_GET['brukerID'];
            echo "<p>Innlogget bruker: $brukerID, &oslash;ving: $ovingsID, tilbakemelding til bruker $brukerTilVurdering</p>";
            $oving = getOving($ovingsID);
            $innlevering = getInnlevering($ovingsID, $brukerTilVurdering);
            $tilbakemelding = getSpesifikkTilbakemelding($brukerTilVurdering, $ovingsID);
            echo print_r($tilbakemelding);
            echo "<h1>Din tilbakemelding til " . $oving['navn'] . "</h1>";
            ?>

        </div>
    </body>
</html>
