<!DOCTYPE html>
<html>
    <head>
        <?php include 'login.incl.php' ?>
        <meta charset="UTF-8">
        <title>Logg inn MOOC inc.</title>
        <link rel="stylesheet" href="stilark.css" />
    </head>
    <body>
        <div id="wrapper">
            <form method="POST" action="login.php">
                <label for="epost">Epost: </label>
                <input type="email" name="epost"><br>

                <label for="passord">Passord</label> 
                <input type="password" name="passord"><br>

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
