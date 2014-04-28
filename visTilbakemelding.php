<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" value="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="stilark.css" />
        <title>Din tilbakemelding</title>
        <?php
        include 'services.incl.php';
        session_start();
        ?>
    </head>
    <body>
        <div id="wrapper">
            <?php
            $brukerID = $_SESSION['brukerID'];
            $ovingsID = $_GET['ovingsID'];
            $brukerTilVurdering = $_GET['brukerID'];
            echo "<p>Innlogget bruker: $brukerID, &oslash;ving: $ovingsID, tilbakemelding til bruker $brukerTilVurdering</p>";
            ?>
            <table>
                <thead>
                    <th></th>
                </thead>
            </table>
        </div>
    </body>
</html>
