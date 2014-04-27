<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" value="text/html; charset=UTF-8" />
        <title>To-do</title>
        <link rel="stylesheet" href="stilark.css" />
        <?php
        include 'service.incl.php';
        ?>
    </head>
    <body>
        <div id="wrapper">
            <?php
            session_start();
            echo $_SESSION['brukerID'];
            ?>
            <table>
                <thead>
                <th>Gj&oslash;rem&aring;l</th>
                <th>Gj&oslash;r &oslash;ving</th>
                <th>Rett medstudent 1</th>
                <th>Rett medstudent 2</th>
                <th>Rett medstudent 3</th>
                </thead>
                <?php
                //TODO: foreach øving:
                //  if(isDone(øving))visBesvarelse; else leverBesvarelse;
                //  for(i=1:3) if(harGittTilbakemelding) visTilbakemelding; else trekkTilfeldig;
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
                                    . "<td><a href=tilbakemeldingOving.php?ovingsID="
                                    . $tilbakemeldinger[$i][0] . "'>Trekk tilfeldig</a></td>";
                        }
                    }
                    echo $utskrift;
                }
                ?>
            </table>
        </div>
    </body>
</html>
