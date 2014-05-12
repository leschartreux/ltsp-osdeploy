<?php

// http://davidwalsh.name/backup-mysql-database-php
function exporte_dump($prefixe)
{
    
    if ($prefixe !== '') {

        $prefixe = $prefixe.'_';

    } else {

        $prefixe = '';
    }

	$tables = array();
	$result = mysql_query("SHOW TABLES");

	while($row = mysql_fetch_row($result)) {

        $tables[] = $row[0];
	}

	$return = '';

	foreach($tables as $table) {

        $result     = mysql_query("SELECT * FROM $table");
        $num_fields = mysql_num_fields($result);

        $return .= "DROP TABLE `$prefixe"."$table`;";

        $row2 = mysql_fetch_row(mysql_query("SHOW CREATE TABLE $table"));

        $return.= "\n\n".str_replace("CREATE TABLE `$table`","CREATE TABLE `$prefixe"."$table`",$row2[1]).";\n\n";

        for ($i = 0; $i < $num_fields; $i++) {

            while($row = mysql_fetch_row($result)) {

                $return.= "INSERT INTO `$prefixe"."$table` VALUES(";

                for($j=0; $j<$num_fields; $j++) {

                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = preg_replace('#\\n#','\\n',$row[$j]); //ligne à modifier

                    if (isset($row[$j])) { 

                        $return.= '"'.$row[$j].'"' ;

                    } else {

                        $return.= '""';
                    }

					if ($j<($num_fields-1)) {

                        $return.= ',';
                    }
				}

				$return.= ");\n";
			}
		}

		$return.="\n";
	}
	
	$handle = fopen('../../../DB_DUMPS/'.$prefixe.'jeddlaj-'.date('Y-m-d-H-i-s').'.sql','w+');

	fwrite($handle,$return);

	fclose($handle);
}




/*
 * Restore MySQL dump using PHP
 * (c) 2006 Daniel15
 * Last Update: 9th December 2006
 * Version: 0.2
 * Edited: Cleaned up the code a bit. 
 *
 * Please feel free to use any part of this, but please give me some credit :-)
 */

function import_dump($filename)
{
    // Temporary variable, used to store current query
    $templine = '';
    
    // Read in entire file
    $lines = file($filename);
    
    // Loop through each line
    foreach ($lines as $line) {
    
        // Skip it if it's a comment
        if (substr($line, 0, 2) == '--' || $line == '')
            continue;
 
        // Add this line to the current segment
        $templine .= $line;

        // If it has a semicolon at the end, it's the end of the query
        if (substr(trim($line), -1, 1) == ';') {

            // Perform the query
            mysql_query($templine);
            // Reset temp variable to empty
            $templine = '';
        }
    }
}
?>