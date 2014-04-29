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
        <title>Registrer øving</title>
    </head>
    <body>
        <?php
        adminMeny();

        if (isset($_POST['leggTilOving'])) {
            echo leggTilOving();
        }
        ?>
        <div id="wrapper">
            <h1>Registrering</h1>
            <form method="POST">
                <table>
                    <thead>
                    <th colspan="2">Registrer ny øving</th>
                    </thead>
                    <tr>
                        <td><label for="navn">Øvings navn:</label></td>
                        <td><input name="navn" type="text"></td>
                    </tr>
                    <tr>
                        <td><label for="oppgavetekst">Oppgavetekst:</label></td>
                        <td><textarea name="oppgavetekst"></textarea></td>
                    </tr>
                    <tr>
                        <td><label for="innleveringsfrist">Innleveringsfrist: (YYYY-MM-DD)</label></td>
                        <td><input type="text" name="innleveringsfrist"></td>
                    </tr>
                    <tr>
                        <td><label for="obligatorisk">Obligatorisk</label></td>
                        <td><input type="checkbox" name="obligatorisk"></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" value="Legg til øving" name="leggTilOving"></td>
                    </tr>
                </table>
            </form>
        </div>
    </body>
</html>
