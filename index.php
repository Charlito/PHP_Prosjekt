<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Redirecting...</title>
        <link rel="stylesheet" href="stilark.css" />
    </head>
    <body>
        <div id="wrapper">
            <?php
            session_start();
            
            if (isset($_SESSION['brukerID'])) {
                echo "<meta http-equiv='refresh' content='0; url=./todo.php' />";
                //header('Location: ./todo.php');
            } else {
                echo "<meta http-equiv='refresh' content='0; url=./login.php?error=Feil brukernavn eller passord.' />";
                //header('Location: ./login.php');
            }
            ?>
        </div>
    </body>
</html>
