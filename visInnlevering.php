<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta http-equiv="Content-type" value="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="stilark.css" />
        <title>Innlevering</title>
        <?php
        include 'service.incl.php';
        $ovingsID = $_GET['ovingsID'];
        $oving = getOving($ovingsID);
        $innlevering = getInnlevering($ovingsID);
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
            echo '<p>' . $innlevering['innlevering'] . '</p>';
            
            echo "<h2>Tilbakemeldinger</h2>";
            echo "<ol>";
            for ($i = 0; $i < count($tilbakemeldinger); $i++) {
                if ($tilbakemeldinger[$i]['tilbakemelding'] != null || $tilbakemeldinger[$i]['tilbakemelding'] != '') {
                    echo "<li><p>" . $tilbakemeldinger[$i]['tilbakemelding'] . "</p></li>";
                } else {
                    echo "<li><p>Ingen tilbakemelding</p></li>";
                }
            }
            echo "</ol>";
            ?>
        </div>
    </body>
</html>
