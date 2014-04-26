<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        if (isset($_SESSION['brukerID'])) {
            header('Location: /PHP_Prosjekt/todo.php');
        } else {
            header('Location: /PHP_Prosjekt/login.php');
        }
        ?>
    </body>
</html>
