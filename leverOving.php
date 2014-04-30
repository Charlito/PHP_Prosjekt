<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
        <title>Innlevering</title>
        <link rel="stylesheet" href="stilark.css" />
        <?php
        include 'service.incl.php';
        echo ensureLogin();
        ?>
    </head>
    <body>
        <div id="wrapper">
            <?php
            $ovingsID = $_GET['ovingsID'];
            $oving = getOving($ovingsID);
            echo "<h1>" . $oving['navn'] . "</h1>";
            ?>

            <form method="POST">
                <label for="innlevering">Innlevering: </label>
                <textarea id="innlevering" name="innlevering"></textarea>
                <br />
                <input type="submit" value="Lever &oslash;vingen">
            </form>
            
            <?php
            if (isset($_POST['innlevering'])) {
                echo leverOving($ovingsID);
            }
            ?>
            
        </div>
    </body>
</html>
