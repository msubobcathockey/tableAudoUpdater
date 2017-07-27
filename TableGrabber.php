<?php
/**
* Parses html from a given url and grabs the data from specified tables on the page and
* provides utilities for saving and loading the tables.
* @author In order of contribution Daniel Church <daniellchurch@fvcc.edu>, Connor Mesec <connormesec@gmail.com>
* @copyright (c) 2017, Daniel Church, Connor Mesec
* @license https://opensource.org/licenses/lgpl-license.php LGPL
*/
class TableGrabber {
	public static $path = 'datatables';
   /**
    * Grabs the full html of a page.
    * @param string $url The url of the page you want to grab.
    * @return string The html source of the requested url.
    */
   public static function grabFullPage($url) {
       $curl = curl_init($url);
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
       curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        
       $html = curl_exec($curl);

       if (curl_errno($curl)) {
           echo ('Scraper error: ') . curl_error($curl);
           exit;
       }

       curl_close($curl);

       return $html;
   }

   /**
    * Replace any ( / [ ] ^ $ . | ? * + ( ) { } )'s in the html that would break the regex expression.
    * @param string $tag The tag to prepare that may contain characters that need to be escaped.
    * @return string The correctly regex ready tag .
    */
   private static function prepTag($tag) {
       // } { ) ( + * ? | . $ ^ ] [ /
       return trim(
               preg_replace('/\}/', '\\}',
               preg_replace('/\{/', '\\{',
               preg_replace('/\)/', '\\)',
               preg_replace('/\+/', '\\+',
               preg_replace('/\*/', '\\*',
               preg_replace('/\?/', '\\?',
               preg_replace('/\|/', '\\|',
               preg_replace('/\./', '\\.',
               preg_replace('/\$/', '\\$',
               preg_replace('/\^/', '\\^',
               preg_replace('/\]/', '\\]',
               preg_replace('/\[/', '\\[',
               preg_replace('/\//', '\\/', $tag
                       ))))))))))))));
   }

   /**
    * Grabs the html of a given url between two optional tags
    * @param string $url The url to grab the html from.
    * @param string $startTag The start of the area to grab, as an html tag (use id's, etc to specify which).
    * @param string $endTag The end of t he area to grab, as an html tag.
    * @param array linesToReplace Option values that can be passed in to be replaced with other values.  Use the format oldTag:newTag.
    * @return array The html of the given url.
    */
   public static function grabPage($url, $startTag = '', $endTag = '', $linesToReplace = array()) {
       $html = TableGrabber::grabFullPage($url);

       // Put it all on one line
       $page = trim(preg_replace('/\n/', '', $html));
       
       foreach($linesToReplace as $line) {
           $split = explode(":", $line);
           $page = preg_replace($split[0], TableGrabber::prepTag($split[1]), $page);
       }

       $list = array();
       if (!preg_match('/' . TableGrabber::prepTag($startTag) . '(.*?)' . TableGrabber::prepTag($endTag) . '/', $page, $list))
           echo('Error');
       else
           return $list[0];
   }

   /**
    * Reads through a html file and returns all that data points that 'matter'
    * (ie the html between braces that have a start and end around them)
    * Ex. <html> <body> <p> Hello World </p> <a href = 'https://stackoverflow.com/'> Click Me </a> </body> </html>
    * Here, 'Hello World' and 'Click Me' are data points that matter
    * @param string $url The url to grab parse.
    * @param string $startTag The start of the area to grab, as an html tag (use id's, etc to specify which).
    * @param string $endTag The end of t he area to grab, as an html tag.
    * @param array linesToReplace Option values that can be passed in to be replaced with other values.  Use the format oldTag:newTag.
    * @param bool $mock If you're passing in mock html instead of a url for testing
    * @return string[] An array of all data one layer deep in html tags 
    */
   public static function parseHTML($url, $startTag = '', $endTag = '', $linesToReplace = array(), $mock = false) {
       if(!$mock)
           $page = TableGrabber::grabPage ($url, $startTag, $endTag, $linesToReplace);
       else
           $page = $url;

       // Replace all newlines with nothing
       $html = trim(preg_replace('/\n/', '', $page));

       // Find all < ... >
       $data = array(array());
       preg_match_all('/>(.*?)</', $html, $data);

       // Remove any remaining <>'s
       $out = array();
       $index = 0;
       foreach($data[0] as &$row) {
           // If the line only contains '&nbsp;' or '<' or '>', don't add it to the table
           if(preg_match('/^\\s+$/', $row) || ($row = trim(preg_replace('/(<|>)|(&nbsp;)/', '', $row))) != '') {
               $out[$index++] = trim($row);
           }
       }

       return $out;
   }

   /**
    * Parses the data points out of a table from a given url between two given html tags
    * Use the two tags to specify which table
    * @param string $url The url to grab the html from.
    * @param string $startTag The start of the area to grab, as an html tag (use id's, etc to specify which).
    * @param string $endTag The end of t he area to grab, as an html tag.
    * @param int $rowWidth The width of a row of the table you're parsing
    * @param array linesToReplace Option values that can be passed in to be replaced with other values.  Use the format oldTag:newTag.
    * @param bool $mock If you're passing in mock html instead of a url for testing
    * @return string[][] A 2d array of all data one layer deep in html tags 
    */
   public static function parseTable($url, $startTag, $endTag, $rowWidth, $linesToReplace = array(), $mock = false) {
       $data = TableGrabber::parseHTML($url, $startTag, $endTag, $linesToReplace, $mock);

       $out = array(array());

       $rowIndex = 0;
       $colIndex = 0;

       // Puts the data into a 2d array based on the given rowWidth
       foreach($data as $cell) {
           $out[$rowIndex][$colIndex++] = $cell;
           if($colIndex >= $rowWidth) {
               $colIndex = 0;
               $rowIndex++;
           }
       }

       return $out;
   }

   /**
    * Renders a table returned from parseTable()
    * @param type $table The table returned from parseTable()
    */
   public static function renderTable($table) {
       echo('<div class="tableRes"><table style="width:100%">');

       foreach($table as $row) {
           echo('<tr align="center" class="light">');
           foreach($row as $cell)
               echo ('<td>' . $cell . '</td>');
           echo('</tr>');
       }

       echo('</table></div>');
   }

   /**
    * Saves a given table to a given file in a CSV format with an optional delimiter.
    * @param string $fileName The name of the file to save to.
    * @param string[][] $table The table to save to file.
    * @param string $delimiter The delimiter to use (Default: '€').
    */
   public static function saveTable($fileName, $table, $delimiter = '€') {
       // Open the file with write permissions (can write to and create the file)
       
       if (!file_exists(TableGrabber::$path)) {
           mkdir(TableGrabber::$path, 0777, true);
       }

       $statsFile = fopen(TableGrabber::$path.'/'.$fileName, 'w+');

       // Write the data to the file
       foreach ($table as $column) {
           for ($i = 0; $i < count($column); $i++)
               fwrite($statsFile, trim($column[$i]) . ($i == count($column)-1 ? '' : $delimiter));
           fwrite($statsFile, "\n");
       }
   }

   /**
    * Loads a table from a given file.
    * @param string $fileName The name of the file to load.
    * @param string $delimiter The delimiter used in the file (Default '€').
    * @return String[][] The table read from the file.
    */
   public static function loadTable($fileName, $delimiter = '€') {
       // Open the file with read permissions (CANNOT GENERATE THE FILE)
       $statsFile = fopen(TableGrabber::$path.'/'.$fileName, "r");

       $out = array(array());

       // Read the whole file
       $i = 0;
       while (!feof($statsFile)) {
           // Line by line
           $line = fgets($statsFile);

           // Skip blank lines
           if (trim($line) == '')
               continue;

           // Split the line into an array seperated by #
           $statVars = explode($delimiter, $line);

           for ($j = 0; $j < count($statVars); $j++)
               $out[$i][$j] = $statVars[$j];
           $i++;
       }
       // Close the file
       fclose($statsFile);

       return $out;
   }
}