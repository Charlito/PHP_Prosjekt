<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" value="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="stilark.css" />
        <title>Innlevering</title>
        <?php
        include 'service.incl.php';
        $ovingsID = $_GET['ovingsID'];
        $_SESSION['ovingsID'] = $ovingsID;
        $oving = getOving($ovingsID);
        $innlevering = getInnlevering($ovingsID, $_SESSION['brukerID']);
        ?>
    </head>
    <body>
        <div id="wrapper">
            <?php
            $tilbakemeldinger = getTilbakemelding();
            echo '<h1>Din innlevering for ' . $oving['navn'] . '</h1>';
            
            $utskrift = '<p>&Oslash;vingen er ';
            if ($oving['godkjent']) {
                $utskrift = $utskrift . 'godkjent.';
            } else {
                $utskrift = $utskrift . 'ikke godkjent.';
            }
            echo $utskrift;
            
            echo "<h2>Oppgavebeskrivelse</h2>";
            echo '<p>' . $oving['oppgavetekst'] . '</p>';
            
            echo '<h2>Besvarelse</h2>';
            echo '<p>' . utf8_decode(htmlspecialchars($innlevering['innlevering'], ENT_SUBSTITUTE)) . '</p>';
            
            echo "<h2>Tilbakemeldinger</h2>";
            echo "<ol>";
            for ($i = 0; $i < count($tilbakemeldinger); $i++) {
                if ($tilbakemeldinger[$i]['tilbakemelding'] != null || $tilbakemeldinger[$i]['tilbakemelding'] != '') {
                    echo "<li><p>" . utf8_decode(htmlspecialchars($tilbakemeldinger[$i]['tilbakemelding'], ENT_SUBSTITUTE)) . "</p></li>";
                } else {
                    echo "<li><p>Ingen tilbakemelding</p></li>";
                }
            }
            echo "</ol>";
            ?>
        </div>
    </body>
</html>
