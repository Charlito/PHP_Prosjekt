<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta http-equiv="Content-type" value="text/html; charset=UTF-8" />
        <title>To-do</title>
        <link rel="stylesheet" href="stilark.css" />
        <?php
        include 'service.incl.php';
        ?>
    </head>
    <body>
        <div id="wrapper">
            <table>
                <thead>
                <th>Gj&oslash;rem&aring;l</th>
                    <th>Gj&oslash;r &oslash;ving</th>
                    <th>Rett medstudent 1</th>
                    <th>Rett medstudent 2</th>
                    <th>Rett medstudent 3</th>
                </thead>
                <?php
                //TODO: foreach øving:
                //  if(isDone(øving))visBesvarelse; else leverBesvarelse;
                //  for(i=1:3) if(harGittTilbakemelding) visTilbakemelding; else trekkTilfeldig;
                getOvinger();
                ?>
            </table>
        </div>
    </body>
</html>
