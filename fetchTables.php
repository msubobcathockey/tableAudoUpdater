<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        include('TableGrabber.php');
        
        //determines inputes for the parseTable function
        $table1 = TableGrabber::parseTable("http://d15k3om16n459i.cloudfront.net/prostats/teamplayerstats.html?teamid=514106&seasonid=17008", '<table width="98%" class="tablelines" cellpadding="2" border="0" cellspacing="1">', '<td colspan=19 class="maincolor">&nbsp;</td>', 17);
        $table1g = TableGrabber::parseTable("http://d15k3om16n459i.cloudfront.net/prostats/teamplayerstats.html?teamid=514106&seasonid=17008", '<a name="goalies"></a>', '<td colspan="13">&nbsp;</td>', 13);
        $table2 = TableGrabber::parseTable("http://d15k3om16n459i.cloudfront.net/prostats/teamroster.html?teamid=514106&seasonid=17008", '<table width="98%" class="tablelines" cellpadding="2" border="0" cellspacing="1">', "</table>", 6);
        $table3 = TableGrabber::parseTable("http://achahockey.org/stats/overview/team/514106?leagueid=1800&conferenceid=1151&divisionid=77500&site_id=2439&page=overview&web_page_id=103177&web_page_title=Stats&full_calendar=", "18 </h3>", "</table>", 10);
        $table4 = TableGrabber::parseTable("http://d15k3om16n459i.cloudfront.net/prostats/teamplayerstats.html?teamid=514106&seasonid=16169", '<table width="98%" class="tablelines" cellpadding="2" border="0" cellspacing="1">', '<td colspan=19 class="maincolor">&nbsp;</td>', 17);
        $table4g = TableGrabber::parseTable("http://d15k3om16n459i.cloudfront.net/prostats/teamplayerstats.html?teamid=514106&seasonid=16169", '<a name="goalies"></a>', '<td colspan="13">&nbsp;</td>', 13);
        $table5 = TableGrabber::parseTable("http://d15k3om16n459i.cloudfront.net/prostats/teamroster.html?teamid=514106&seasonid=16169", '<table width="98%" class="tablelines" cellpadding="2" border="0" cellspacing="1">', "</table>", 6);
        $table6 = TableGrabber::parseTable("http://achahockey.org/stats/overview/team/514106?leagueid=1800&conferenceid=1151&divisionid=77500&site_id=2439&page=overview&web_page_id=103177&web_page_title=Stats&full_calendar=", "17 </h3>", "</table>", 10);
        //special code here to deal with blank spaces in the schedule
        $table7 = TableGrabber::parseTable("achahockey.org/stats/schedule/team/514106?leagueid=1800&conferenceid=1151&divisionid=77500&seasonid=16169&teamid=514106&site_id=2439&page=schedule&web_page_id=103177&web_page_title=Stats&full_calendar=", '<table class="table table-striped table-bordered table-hover table-condensed table-stats">', '/table>', 9, ['/<td class="text-right">\\s+\\d+\\s+<\/td>/:<td class="text-right"></td>',
                    '/<th class="span2"><\/th>/:<th class="span2"> Boxscore </th>',
                    '/<th class="text-right" title="Game Number">#<\/th>/:<th class="text-right" title="Game Number"></th>']);
        $table8 = TableGrabber::parseTable("http://achahockey.org/stats/schedule/team/514106?leagueid=1800&conferenceid=1151&seasonid=17008&teamid=514106&site_id=2439&page=schedule&web_page_id=103177&web_page_title=Stats&full_calendar=", '<table class="table table-striped table-bordered table-hover table-condensed table-stats">', '/table>', 9, ['/<td class="text-right">\\s+\\d+\\s+<\/td>/:<td class="text-right"></td>',
                    '/<th class="span2"><\/th>/:<th class="span2"> Boxscore </th>',
                    '/<th class="text-right" title="Game Number">#<\/th>/:<th class="text-right" title="Game Number"></th>']);
        
        //Saves the data, spaced by the delimiter to a .csv file
        TableGrabber::saveTable('stats17.csv', $table1);
        TableGrabber::saveTable('stats17g.csv', $table1g);
        TableGrabber::saveTable('roster17.csv', $table2);
        TableGrabber::saveTable('winloss17.csv', $table3);
        TableGrabber::saveTable('stats16.csv', $table4);
        TableGrabber::saveTable('stats16g.csv', $table4g);
        TableGrabber::saveTable('roster16.csv', $table5);
        TableGrabber::saveTable('winloss16.csv', $table6);
        TableGrabber::saveTable('schedule16.csv', $table7);
        TableGrabber::saveTable('schedule17.csv', $table8);
        ?>
    </body>
</html>
