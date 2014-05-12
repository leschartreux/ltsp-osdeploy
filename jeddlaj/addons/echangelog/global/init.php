<?php
require_once 'global/config.php';

if (is_file('global/dbconfig.php')) {
    require 'global/dbconfig.php';
 }




function stripslashesDeep($value)
{
    $value = is_array($value) ?
                array_map('stripslashesDeep', $value) :
                stripslashes($value);

    return $value;
}


if (get_magic_quotes_gpc())
{
    $_GET = stripslashesDeep($_GET);
    $_POST = stripslashesDeep($_POST);
    $_COOKIE = stripslashesDeep($_COOKIE);
    $_REQUEST = stripslashesDeep($_REQUEST);
}

require_once LIBS_PATH.'Dao.class.php';
?>
