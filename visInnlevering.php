<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="stilark.css" />
        <title>Innlevering</title>
        <?php
        include 'service.incl.php';
        echo ensureLogin();
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
            echo '<p>' . utf8_decode(htmlspecialchars($innlevering['innlevering'], ENT_SUBSTITUTE)) . '</p>';
            echo '';
            echo "<h2>Tilbakemeldinger</h2>";
            echo "<ol>";
            for ($i = 0; $i < count($tilbakemeldinger); $i++) {
                if ($tilbakemeldinger[$i]['tilbakemelding'] != null || $tilbakemeldinger[$i]['tilbakemelding'] != '') {
                    $utskrift = "<li><p>" . utf8_decode(htmlspecialchars($tilbakemeldinger[$i]['tilbakemelding'], ENT_SUBSTITUTE));
                    if ($tilbakemeldinger[$i]['godkjent']) {
                        $utskrift = $utskrift . "<br />Resultat: Godkjent</p></li>";
                    } else {
                        $utskrift = $utskrift . "<br />Resultat: Ikke godkjent</p></li>";
                    }
                    echo $utskrift;
                } else {
                    echo "<li><p>Ingen tilbakemelding</p></li>";
                }
            }
            echo "</ol>";
            ?>
        </div>
    </body>
</html>
