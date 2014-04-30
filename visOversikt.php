<!DOCTYPE html>
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

        
        if (isset($_POST))
            slettOving(key($_POST));
        ?>
        <div id="wrapper">
            <h1>Vis oversikt</h1>
            <form method="POST">
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
                        . "<td><a href='visOvingOversikt.php?ovingsID=" . $ovinger[$i]['ovingsID'] . "'>" . $ovinger[$i]['navn'] . "</a></td>"
                        . "<td>" . $ovingInfo[$i]['antInnleveringer'] . "</td>"
                        . "<td>" . $ovingInfo[$i]['manglendeTilbakemelding'] . "</td>"
                        . "<td>" . $ovingInfo[$i]['antGodkjente'] . "</td>"
                        . "<td><input type='submit' name='" . $ovinger[$i]['ovingsID'] . "' value='Slett'></td>"
                        . "</tr>";
                    }
                    // put your code here
                    ?>
                </table>
            </form>
        </div>
    </body>
</html>
