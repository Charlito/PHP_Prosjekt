<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
        <title>To-do</title>
        <link rel="stylesheet" href="stilark.css" />
        <?php
        include 'service.incl.php';
        echo ensureLogin();
        $bruker = getBruker();
        ?>
    </head>
    <body>
        <div id="wrapper">
            <?php 
            echo "<h1>Velkommen " . $bruker['navn'] . "</h1>";
            ?>
            <table>
                <thead>
                <th>Gj&oslash;rem&aring;l</th>
                <th>Gj&oslash;r &oslash;ving</th>
                <th>Rett medstudent 1</th>
                <th>Rett medstudent 2</th>
                <th>Rett medstudent 3</th>
                <th>Innleveringsfrist</th>
                </thead>
                <?php
                $ovinger = getOvinger();
                $innleveringer = getInnleveringer();
                $tilbakemeldinger = getAlleTilbakemeldinger();

                for ($i = 0; $i < count($ovinger); $i++) {
                    $utskrift = "<tr><td id='" . $ovinger[$i]['ovingsID'] . "'";
                    if ($ovinger[$i]['obligatorisk']) {
                        $utskrift = $utskrift . " class=obligatorisk";
                    }
                    $utskrift = $utskrift . "><a href='visOving.php?ovingsID=" . $ovinger[$i]['ovingsID'] . "'>" . $ovinger[$i]['navn'] . "</a></td>";


                    $utskrift = $utskrift . "<td><a href='";
                    if ($innleveringer[$i]['brukerID_levert'] != null || $innleveringer[$i]['brukerID_levert'] != '') {
                        $utskrift = $utskrift . "visInnlevering.php?ovingsID=" . $innleveringer[$i]['ovingsID'] . "'>Vis besvarelse";
                    } else {
                        $utskrift = $utskrift . "leverOving.php?ovingsID=" . $innleveringer[$i]['ovingsID'] . "'>Lever besvarelse";
                    }
                    $utskrift = $utskrift . "</a></td>";

                    for ($j = 1; $j < 4; $j++) {
                        if ($tilbakemeldinger[$i][$j] != null || $tilbakemeldinger[$i][$j] != '') {
                            $utskrift = $utskrift
                                    . "<td><a href='visTilbakemelding.php?ovingsID="
                                    . $tilbakemeldinger[$i][0] . "&brukerID=" . $tilbakemeldinger[$i][$j]
                                    . "'>Vis tilbakemelding</a></td>";
                        } else {
                            $utskrift = $utskrift
                                    . "<td><a href='tilbakemeldingOving.php?ovingsID="
                                    . $tilbakemeldinger[$i][0] . "'>Trekk tilfeldig</a></td>";
                        }
                    }
                    
                    $antallDagerTilFrist = antallDagerTilFrist($ovinger[$i]['innleveringsfrist']);
                    if ($antallDagerTilFrist > 1) {
                        $utskrift = $utskrift . "<td>$antallDagerTilFrist dager</td></tr>";
                    } elseif ($antallDagerTilFrist == 1) {
                        $utskrift = $utskrift . "<td>$antallDagerTilFrist dag</td></tr>";
                    } elseif ($antallDagerTilFrist == 0) {
                        $utskrift = $utskrift . "<td>Innen dagen</td></tr>";
                    } else {
                        $utskrift = $utskrift . "<td>Fristen har gått ut</td></tr>";
                    }
                    
                    echo $utskrift;
                }
                ?>
                <tfoot>
                    <tr><th colspan="6"><small>System levert av et lite team på to.</small></th></tr>
                </tfoot>
            </table>
        </div>
    </body>
</html>
