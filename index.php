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
            if (isset($_SESSION['brukerID'])) {
                header('Location: /PHP_Prosjekt/todo.php');
            } else {
                header('Location: /PHP_Prosjekt/login.php');
            }
            ?>
        </div>
    </body>
</html>
