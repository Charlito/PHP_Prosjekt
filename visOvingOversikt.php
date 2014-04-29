<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>

        <?php
        if (!isset($_GET['ovingsID'])) {
            echo "<meta http-equiv='refresh' content='0; url=./visOversikt.php' />";
        }
        adminMeny();
        ?>


        <table>
            <thead>
            <th>Student</th>
            <th>Øving</th>
            <th>Tilbakemeldinger</th>
        </thead>
        <?php
        $brukereOving = getBrukereOving($_GET['ovingsID']) //TODO
        ?>
    </table>
</body>
</html>
