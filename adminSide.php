<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
include './service.incl.php';

//sjekke om brukeren er lærer, hvis ikke -> redirect til index
sjekkOmAdmin();
?>


<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="stilark.css" />
        <title></title>
    </head>
    <body>
        <?php 
        adminMeny();
        ?>
        <article>
            <p>Velkommen til lærersiden. Her kan du velge ulike handlinger i fra menyen. Du kan registrere nye brukere, legge til øvinger og vise oversikt over hvilke studenter som har levert øvingene og se/gi tilbakemeldinger til disse.</p>
        </article>
        <?php
        ?>
    </body>
</html>
