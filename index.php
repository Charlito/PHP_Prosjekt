<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Redirecting...</title>
        <link rel="stylesheet" href="stilark.css" />
        <?php
        include 'service.incl.php';
        $rolle = getRolle();
        ?>
    </head>
    <body>
        <div id="wrapper">
            <?php
            echo ensureLogin();
            if ($rolle == 1) {
                echo "<meta http-equiv='refresh' content='0; url=./adminSide.php' />";
            } else {
                echo "<meta http-equiv='refresh' content='0; url=./todo.php' />";
            }
            ?>
        </div>
    </body>
</html>
