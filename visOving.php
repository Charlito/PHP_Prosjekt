<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" value="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="stilark.css" />
        <?php
        include 'service.incl.php';
        $ovingsID = $_GET['ovingsID'];
        $oving = getOving($ovingsID);
        echo "<title>" . $oving['navn'] . "</title>";
        ?>
    </head>
    <body>
        <div id="wrapper">
            <?php
            echo '<h1>' . $oving['navn'] . '</h1>';
            echo '<h2>Leveringsinformasjon</h2>';
            $utskrift = '<p>&Oslash;vingen er ';
            if ($oving['obligatorisk']) {
                $utskrift = $utskrift . 'obligatorisk og ';
            } else {
                $utskrift = $utskrift . 'ikke obligatorisk men ';
            }
            $utskrift = $utskrift . 'm&aring; leveres innen ' . $oving['innleveringsfrist'];
            echo $utskrift;
            echo "<h2>Oppgavebeskrivelse</h2>";
            echo '<p>' . $oving['oppgavetekst'] . '</p>'
            ?>
        </div>
    </body>
</html>
