<!DOCTYPE html>
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
        echo ensureLogin();
        ?>
        <div id="wrapper">
            <h1>Registrering</h1>
            
            <form method="POST">
                <table>
                    <thead>
                    <th colspan="2">Registrer ny øving</th>
                    </thead>
                    <tr>
                        <td><label for="navn">Navn på øvingen</label></td>
                        <td><input id="navn" name="navn" type="text" /></td>
                    </tr>
                    <tr>
                        <td><label for="oppgavetekst">Oppgavetekst</label></td>
                        <td><textarea id="oppgavetekst" name="oppgavetekst"></textarea></td>
                    </tr>
                    <tr>
                        <td><label for="innleveringsfrist">Innleveringsfrist</label></td>
                        <td><input id="innleveringsfrist" type="date" name="innleveringsfrist" /></td>
                    </tr>
                    <tr>
                        <td><label for="obligatorisk">Obligatorisk</label></td>
                        <td><input id="obligatorisk" type="checkbox" name="obligatorisk" /></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" value="Legg til øving" name="leggTilOving"></td>
                    </tr>
                </table>
            </form>
            <?php
            if (isset($_POST['leggTilOving'])) {
                echo leggTilOving();
            }
            ?>
        </div>
    </body>
</html>
