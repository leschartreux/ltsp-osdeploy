<?php
/**
* ****************************** GPL STUFF ********************************
*
* ********************************* ENGLISH *********************************
*
* Copyright notice :
*
* Copyright 2003, 2004, 2005 G�rard Milhaud - Fr�d�ric Bloise
*
*
*  Statement of copying permission
*
* This file is part of JeDDLaJ.
*
* JeDDLaJ is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* JeDDLaJ is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with JeDDLaJ; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
*
* *********** TRADUCTION FRAN�AISE PERSONNELLE SANS VALEUR L�GALE ***********
*
*  Notice de Copyright :
*
* Copyright 2003, 2004, 2005 G�rard Milhaud - Fr�d�ric Bloise
*
*
*  D�claration de permission de copie
*
* Ce fichier fait partie de JeDDLaJ.
*
* JeDDLaJ est un logiciel libre : vous pouvez le redistribuer ou le modifier
* selon les termes de la Licence Publique G�n�rale GNU telle qu'elle est
* publi�e par la Free Software Foundation ; soit la version 2 de la Licence,
* soit (� votre choix) une quelconque version ult�rieure.
*
* JeDDLaJ est distribu� dans l'espoir qu'il soit utile, mais SANS AUCUNE
* GARANTIE ; sans m�me la garantie implicite de COMMERCIALISATION ou
* d'ADAPTATION DANS UN BUT PARTICULIER. Voir la Licence publique G�n�rale GNU
* pour plus de d�tails.
*
* Vous devriez avoir re�u une copie de la Licence Publique G�n�rale GNU avec
* JeDDLaJ ; si �a n'�tait pas le cas, �crivez � la Free Software Foundation,
* Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
* ***************************** END OF GPL STUFF ***************************
*/

require_once 'global/init.php';

//on d�finit les variables
$cleanModule = '';
$cleanAction = '';

//On filtre les entr�es
if (isset($_GET['module'])) {

    $cleanModule = htmlspecialchars($_GET['module']);
 }

if (isset($_GET['action'])) {

    $cleanAction = htmlspecialchars($_GET['action']);
 }


// le module n'est pas vide
if (!empty($cleanModule)) { 

    //si c'est un module valide
    if (is_dir('modules/'.$cleanModule) && $cleanModule !== '') {

        $module = 'modules/'.$cleanModule.'/';

        //on d�finit l'action pour les modules autres que classique
         if (is_file($module.ucfirst($cleanAction).'Controller.class.php')) {
            
            $action = ucfirst($cleanAction);
            
        } else { //si l'action n'est pas valide elle vaut Index

            $action = 'Index';
        }
        
    } else { // le module n'est pas valide on d�finit le module et l'action par d�faut
        
        $module = 'modules/index/';
        $action = 'Index';
    }

    //on d�finit la variable controller
    $controller = $action.'Controller';

 
    require_once $module.$action.'Controller.class.php';

        //instanciation du controller
        $frontController = new $controller();

    
 } else { //le module est vide
    require_once dirname(__FILE__).'/modules/index/IndexController.class.php';

    //instanciation du controller
    $frontController = new IndexController();
    
 }

$frontController->request();
$frontController->viewsManagement();

?>