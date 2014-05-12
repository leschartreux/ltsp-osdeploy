<?php
// on défini les variables
$cleanModule = '';


// on filtre les variables
if (isset($_GET['module'])) {

    $cleanModule = htmlspecialchars($_GET['module']);
}

//gestion du choix du module
if (is_dir('modules/'.$cleanModule) && !empty($cleanModule)) {

    $module = $cleanModule;
    
} else {

    $module = 'index';
}



//mettre ici du code qui va bien pour tester en fonction des modules et action

define('VIEW_PATH', 'modules/'.$module.'/view/');
define('MODEL_PATH', 'model/');
define('LIBS_PATH', 'lib/');
define('LOG_PATH', '/packages/');
define('PIS_PATH', '/postinstall/');
define('PDIS_PATH', '/predeinstall/');
define('EXPECT_PATH', '../../');
?>
