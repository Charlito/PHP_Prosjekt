<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Redirecting...</title>
        <link rel="stylesheet" href="stilark.css" />
        <?php
        include 'service.incl.php';
        ?>
    </head>
    <body>
        <div id="wrapper">
            <?php
            echo ensureLogin();
            echo "<meta http-equiv='refresh' content='0; url=./todo.php' />";
            ?>
        </div>
    </body>
</html>
