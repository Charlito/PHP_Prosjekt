<?php
include 'login.incl.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Logg inn MOOC inc.</title>
        <link rel="stylesheet" href="stilark.css" />
    </head>
    <body>
        <div id="wrapper">
            <h1>Logg inn</h1>
            <?php
            if (isset($_GET['error'])) {
                echo "<strong>" . $_GET['error'] . "</strong>";
            }
            ?>
            <form method="POST" action="login.php">
                <label for="epost">Epost: </label>
                <input type="email" name="epost" id="epost"><br>

                <label for="passord">Passord</label> 
                <input type="password" name="passord" id="passord"><br>

                <input type="submit" value="Logg inn" name="login">
            </form>
            <?php
            if (isset($_POST['login'])) {
                login();
            }
            ?>
        </div>
    </body>
</html>
