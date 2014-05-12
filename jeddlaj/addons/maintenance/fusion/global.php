<?php

$sql = 'SELECT prefixe FROM `fusion` ORDER BY etape DESC LIMIT 1';

$query = mysql_query($sql);

$nbRow = mysql_num_rows($query);

if ($nbRow > 0) {

    $prefix = mysql_result($query, 0);
 }
?>