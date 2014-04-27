<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Innlevering</title>
        <link rel="stylesheet" href="stilark.css" />
        <?php include 'service.incl.php'; ?>
    </head>
    <body>
        <?php
        $ovingsID = $_GET['ovingsID'];
        echo "<h1>$ovingsID</h1>";
        ?>
        <form method="POST">
            <label for="innlevering">Innlevering: </label>
            <textarea id="innlevering" name="innlevering"></textarea>
            <input type="submit" value="Lever øvingen">
        </form>
        
        <?php
        if (isset($_POST['innlevering'])) {
            leverOving($ovingsID);
        }
        ?>
    </body>
</html>
