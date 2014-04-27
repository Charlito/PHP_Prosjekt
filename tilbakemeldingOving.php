<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Tilbakemelding</title>
        <link rel="stylesheet" href="stilark.css" />
    </head>
    <body>
        <?php
        $oving = getOving($_GET['ovingsID']);
        $innlevering = getInnlevering($ovingsID, $_GET['brukerID']);
        echo("<h1>Retting av medstudent nr ". $_GET['ovingsID'] .", " . $oving['navn']);
        //Skriv ut øvinga
        echo "<p>". $innlevering['oppgavetekst'] ."<p>";
        //TODO: erstatte y med session
        echo("<h2>Din tilbakemelding til student " . $_GET['brukerID'] ." (du selv nr y)</h2>");
        ?>
        <textarea ></textarea>
        <select>
            <option>Godkjent</option>
            <option>Ikke godkjent</option>
        </select>
        <input type="submit" value="Gi tilbakemelding">
        
    </body>
</html>
