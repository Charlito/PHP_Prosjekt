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
        <meta charset="UTF-8">
        <link rel="stylesheet" href="stilark.css" />
        <title>Vis oversikt</title>
    </head>
    <body>
        <?php
        adminMeny();
        ?>
        <div id="wrapper">
            <h1>Vis oversikt</h1>
            <table>
                <thead>
                <th>Øvinger</th>
                <th>Antall innleveringer</th>
                <th>Mangler tilbakemelding</th>
                <th>Antall godkjente</th>
                <th>Slett øving</th>
                </thead>
                <?php
                $ovinger = getOvinger();
                $ovingInfo = getOvingerInfo();
                for ($i = 0; $i < count($ovinger); $i++) {
                    echo "<tr>"
                    . "<td><a href='visOvingOversikt.php?ovingsID=" . $ovinger[$i]['ovingsID'] ."'>" . $ovinger[$i]['navn'] . "</a></td>"
                    . "<td>" . $ovingInfo[$i]['antInnleveringer'] . "</td>"
                    . "<td>" . $ovingInfo[$i]['manglendeTilbakemelding'] . "</td>"
                    . "<td>" . $ovingInfo[$i]['antGodkjente'] . "</td>"
                    . "<td><input type='submit' name='" . $ovinger[$i]['ovingsID'] . "'></td>"
                    . "</tr>";
                }
                // put your code here
                ?>
            </table>
        </div>
    </body>
</html>
