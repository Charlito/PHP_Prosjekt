<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
include './service.incl.php';
sjekkOmAdmin();
?>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="stilark.css" />
        <title>Øving oversikt</title>
    </head>
    <body>

        <?php
        if (!isset($_GET['ovingsID'])) {
            echo "<meta http-equiv='refresh' content='0; url=./visOversikt.php' />";
        }
        adminMeny();
        ?>

        <div id="wrapper">
            <?php 
            if(isset($_GET['ovingsID'])){
                $oving = getOving($_GET['ovingsID']);
                echo "<h1>Oversikt over " . $oving['navn'] . "</h1>" ;
            }
                    
            
            ?>
            <table>
                <thead>
                <th>Student</th>
                <th>Øving</th>
                <th>Tilbakemeldinger gitt</th>
                <th>Tilbakemeldinger fått</th>
                <th>Godkjent</th>
                <th>Gi tilbakemelding</th>
                <th>Vis/rett øving</th>
                </thead>
                <?php
                $brukereOving = getBrukereOving($_GET['ovingsID']);

                for ($i = 0; $i < count($brukereOving); $i++) {
                    echo "<tr>"
                    . "<td>" . $brukereOving[$i]['navn'] . "</td>";
                    if (isset($brukereOving[$i]['levert'])) {
                        echo "<td>Levert</td>";
                    } else {
                        echo "<td>Ikke levert</td>";
                    }
                    if (isset($brukereOving[$i]['gittTilbakemeldinger'])) {
                        echo "<td>" . $brukereOving[$i]['gittTilbakemeldinger'] . "</td>";
                    } else {
                        echo "<td>0</td>";
                    }
                    if (isset($brukereOving[$i]['fåttTilbakemeldinger'])) {
                        echo "<td>" . $brukereOving[$i]['fåttTilbakemeldinger'] . "</td>";
                    } else {
                        echo "<td>0</td>";
                    }
                    $godkjent = "Ikke godkjent";
                    if(isset($brukereOving[$i]['godkjent']) && $brukereOving[$i]['godkjent']){
                        $godkjent = "Godkjent";
                    }
                    echo "<td>$godkjent</td>";
                    echo "<td><a href='tilbakemeldingOving.php?brukerTilVurdering=" . $brukereOving[$i]['brukerID'] . "&ovingsID=". $_GET['ovingsID'] . "'><button>Velg</button></a></td>";
                    echo "<td><a href='visOving.php?brukerTilVurdering=" . $brukereOving[$i]['brukerID'] . "&ovingsID=". $_GET['ovingsID'] . "'><button>Velg</button></a></td>";
                    echo "<td></td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </body>
</html>
