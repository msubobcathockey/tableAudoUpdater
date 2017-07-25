<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        include('TableGrabber.php');
        //Reads the data in the file to a string in a 2d array
        $table1 = TableGrabber::loadTable('stats17.csv');
        $table1g = TableGrabber::loadTable('stats17g.csv');
        $table2 = TableGrabber::loadTable('roster17.csv');
        $table3 = TableGrabber::loadTable('winloss17.csv');
        $table4 = TableGrabber::loadTable('stats16.csv');
        $table4g = TableGrabber::loadTable('stats16g.csv');
        $table5 = TableGrabber::loadTable('roster16.csv');
        $table6 = TableGrabber::loadTable('winloss16.csv');
        
        //Prints the 2d array with included html to form and display the table
        TableGrabber::renderTable($table1);
        TableGrabber::renderTable($table1g);
        TableGrabber::renderTable($table2);
        TableGrabber::renderTable($table3);
        TableGrabber::renderTable($table4);
        TableGrabber::renderTable($table4g);
        TableGrabber::renderTable($table5);
        TableGrabber::renderTable($table6);
        ?>
    </body>
</html>
