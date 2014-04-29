<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="stilark.css" />
        <title>Innlevering</title>
        <?php
        include 'service.incl.php';
        ?>
    </head>
    <body>
        <?php 
        adminMeny();
        ?>
        <div id="wrapper">
            <h1>Registrering</h1>
            
            <?php
            if (isset($_GET['result'])) {
                echo "<p>" . $_GET['result'] . "</p>";
            }
            ?>
            
            <table>
                <form method="POST">
                    <thead>
                    <th colspan="2">Registrer ny bruker</th>
                    </thead>
                    <tr>
                        <td><label for="epost">Epost: </label></td>
                        <td><input type="email" name="epost" id="epost"></td>
                    </tr>
                    <tr>
                        <td><label for="navn">Navn: </label></td> 
                        <td><input type="text" name="navn" id="navn"></td>
                    </tr>
                    <tr>
                        <td><label for="passord">Passord: </label></td>
                        <td><input type="password" name="passord" id="passord"></td>
                    </tr>
                    <tr>
                        <td><label for="passordsjekk">Gjenta passord: </label></td>
                        <td><input type="password" name="passordsjekk" id="passordsjekk"></td>
                    </tr>
                    <tr>
                        <td><label for="rolle">Rolle: </label></td>
                        <td>
                            <select id="rolle" name="rolle">
                                <option value=0>Student</option>
                                <option value=1>L&aelig;rer</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="submit" value="Registrer bruker" name="registrer">
                        </td>
                    </tr>
                </form>
            </table>
            
            <?php
            if (isset($_POST['registrer'])) {
                echo leggTilBruker();
            }
            ?>
            
        </div>
    </body>
</html>