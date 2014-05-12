<?php
/**
 * ************************** GPL STUFF *************************
 * ************************** ENGLISH *********************************
 *
 * Copyright notice :
 *
 * Copyright 2003 - 2010 Gérard Milhaud - Frédéric Bloise
 * Copyright 2010 - 2011 Frédéric Bloise - Salvucci Arnaud - Gérard Milhaud
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
 * *********** TRADUCTION FRANÇAISE PERSONNELLE SANS VALEUR LÉGALE ***********
 *
 *  Notice de Copyright :
 *
 * Copyright 2003 - 2010 Gérard Milhaud - Frédéric Bloise
 * Copyright 2010 - 2011 Frédéric Bloise - Salvucci Arnaud - Gérard Milhaud
 *
 *
 *  Déclaration de permission de copie
 *
 * Ce fichier fait partie de JeDDLaJ.
 *
 * JeDDLaJ est un logiciel libre : vous pouvez le redistribuer ou le modifier
 * selon les termes de la Licence Publique Générale GNU telle qu'elle est
 * publiée par la Free Software Foundation ; soit la version 2 de la Licence,
 * soit (à votre choix) une quelconque version ultérieure.
 *
 * JeDDLaJ est distribué dans l'espoir qu'il soit utile, mais SANS AUCUNE
 * GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou
 * d'ADAPTATION DANS UN BUT PARTICULIER. Voir la Licence publique Générale GNU
 * pour plus de détails.
 *
 * Vous devriez avoir reçu une copie de la Licence Publique Générale GNU avec
 * JeDDLaJ ; si ça n'était pas le cas, écrivez à la Free Software Foundation,
 * Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * ************************** END OF GPL STUFF *******************************
 *
 * @category   Addons
 * @package    Maintenance
 * @subpackage Fusion
 * @author     Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license    GPL v2
 */
require_once '../UtilsMaintenance.php';
require_once 'connexion.php';
require_once 'global.php';

/**
 * titre de la page
 */
$title = '';

/**
 * chemin vers le favicon
 */
$favicon = '../../../ICONES/favicon.ico';

/**
 * titre de la page
 */
$pageTitle = 'Fusion de deux bases de données';

/**
 * fichier(s) css associé à la page
 */
$style = '<link rel="stylesheet" href="../../../CSS/g.css"  type="text/css" media="screen" />';

/**
 * fichier(s) JS associé à la page
 */
$script = '';

/**
 * notification
 */
$notification = '';

/**
 * contenu de la page
 */
$content = '';

/********************************************
 *                                          *
 *    Traitement de l'étape 9               *
 *                                          *
 ********************************************/

if (isset($_POST['submit'])) {

    while ($row = current($_POST)) {

        if ($row === 'oui') {

            $row = next($_POST);

            $idIdbDist = intval($row);

            $sql = 'INSERT INTO `fusion` (prefixe, etape, arg1, arg2) '.
                   'VALUES ("'.$prefix.'", 9, '.$idIdbDist.', "oui")';

            mysql_query($sql);

            $notification .= 'Les images de base distantes et locales vont être fusionnées<br />';

        } else if ($row === 'non') {

            $row = next($_POST);

            $idIdbDist = intval($row);

            $sql = 'INSERT INTO `fusion` (prefixe, etape, arg1, arg2) '.
                   'VALUES ("'.$prefix.'", 9, '.$idIdbDist.', "non")';

            mysql_query($sql);
        }

        next($_POST);
    }

    $sql = 'DELETE FROM `fusion` '.
           'WHERE etape = 9 '.
           'AND arg1 IS NULL '.
           'AND arg2 IS NULL';

    mysql_query($sql);
 }





/********************************************
 *                                          *
 *           Etape 10                       *
 *                                          *
 ********************************************/
$sql = 'INSERT INTO `fusion` (prefixe, etape) VALUES ("'.$prefix.'", 10)';

$query = mysql_query($sql);

$content .= '<h2>Téléchargement du fichier d\'instruction pour le serveur rembo</h2>';


//récupération des identifiants au serveur rembo
$content .= '<form action="step10.php" method="post">';

$content .= '<p>';

$content .= '<label for="adrServDist">Adresse du serveur distant : </label>';

$content .= '<input type="text" id="adrServDist" name="adrServDist" />';
  
$content .= '</p><p>';

$content .= '<label for="packageDist">Répertoire des packages sur le serveur distant : </label>';

$content .= '<input type="text" id="packageDist" name="packageDist" />';
  
$content .= '</p><p>';

$content .= '<label for="idbDist">Répertoire des images de base sur le serveur distant : </label>';

$content .= '<input type="text" id="idbDist" name="idbDist" />';
  
$content .= '</p><p>';

$content .= '<label for="pisDist">Répertoire des postinstall scripts sur le serveur distant : </label>';

$content .= '<input type="text" id="pisDist" name="pisDist" />';
  
$content .= '</p><p>';

$content .= '<label for="pdisDist">Répertoire des predeinstall scripts sur le serveur distant : </label>';

$content .= '<input type="text" id="pdisDist" name="pdisDist" />';
  
$content .= '</p><p>';

$content .= '<label for="adrServLocal">Addresse du serveur local : </label>';

$content .= '<input type="text" id="adrServLocal" name="adrServLocal" />';

$content .= '</p></p>';

$content .= '<label for="packageLocal">Répertoire des packages sur le serveur local : </label>';

$content .= '<input type="text" id="packageLocal" name="packageLocal" />';
  
$content .= '</p><p>';

$content .= '<label for="idbLocal">Répertoire des images de base sur le serveur local : </label>';

$content .= '<input type="text" id="idbLocal" name="idbLocal" />';
  
$content .= '</p><p>';

$content .= '<label for="pisLocal">Répertoire des postinstall scripts sur le serveur local : </label>';

$content .= '<input type="text" id="pisLocal" name="pisLocal" />';
  
$content .= '</p><p>';

$content .= '<label for="pdisLocal">Répertoire des predeinstall scripts sur le serveur local : </label>';

$content .= '<input type="text" id="pdisLocal" name="pdisLocal" />';
  
$content .= '</p><p>';

$content .= '<label for="mdp">Mot de passe : </label>';

$content .= '<input type="password" id="mdp" name="mdp" />';

$content .= '</p><p>';

$content .= '<label for="telecharger">Télécharger le fichier</label>';

$content .= '<input type="submit" name="telecharger" value="Télécharger" />';

$content .= '</p>';

$content .= '</form>';

$content .= '<p><a href="step11.php">Suivant</a></p>';
    

if (isset($_POST['telecharger'])) {

    if ($_POST['adrServDist'] !== '' && $_POST['packageDist'] !== '' && $_POST['idbDist'] !== '' && $_POST['pisDist'] !== '' && $_POST['pdisDist'] !== '' && $_POST['adrServLocal'] !== '' && $_POST['packageLocal'] !== '' && $_POST['idbLocal'] !== '' && $_POST['pisLocal'] !== '' && $_POST['pdisLocal'] !== ''&& $_POST['mdp'] !== '') {

        //vérification de la validité des champs
        if (preg_match('#^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$#', $_POST['adrServDist']) && preg_match('#^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$#', $_POST['adrServLocal'])) {


            //création du fichier $prefix.rbc
            $fileContent = ''; //le contenu du fichier

            //création des répertoires des packages
            $fileContent .= "open ".$_POST['adrServLocal']." ".$_POST['mdp']."\r\n";

            $sql = 'SELECT distinct repertoire '.
                   'FROM `'.$prefix.'_packages` '.
                   'LEFT JOIN `fusion` ON id_package = arg1 '.
                   'AND etape = 7 '.
                   'WHERE (arg2 IS NULL '.
                   'OR arg2 = "non") '.
                   'AND id_package > '.
                   '(SELECT arg2 FROM `fusion` WHERE etape = 4 AND arg1 = "maxIdPackage")';

            $query = mysql_query($sql);

            while ($array = mysql_fetch_array($query)) {

                $fileContent .= "cd /".$_POST['packageLocal']."\r\n";

                $arrayDirectory = explode('/', $array['repertoire'], -1);
                    
                foreach ($arrayDirectory as $directory) {

                    $fileContent .= "md ".$directory."\r\n";

                    $fileContent .= "cd ".$directory."\r\n";
                }
            }

                
            $sql = 'SELECT repertoire, nom_package, '.
                   'if( nom_package LIKE "'.$prefix.'\_%", nom_package, '.
                   'CONCAT( "'.$prefix.'_", nom_package )) AS nouveau_nom '.
                   'FROM `'.$prefix.'_packages` '.
                   'LEFT JOIN `fusion` ON id_package = arg1 '.
                   'AND etape = 7 '.
                   'WHERE (arg2 IS NULL '.
                   'OR arg2 = "non") '.
                   'AND id_package > '.
                   '(SELECT arg2 FROM `fusion` WHERE etape = 4 AND arg1 = "maxIdPackage")';
   
            $query = mysql_query($sql);

            $arrayExtension = array('.def', '.sam', '.sec', '.sof', '.sys', '.fil', '.ini');

            while ($array = mysql_fetch_array($query)) {

                $fileContent .= "open ".$_POST['adrServDist']." ".$_POST['mdp']."\r\n";

                $fileContent .= "cd /".$_POST['packageDist']."/".$array['repertoire']."\r\n";

                $fileContent .= "mget ".$array['nom_package'].".*\r\n";

                $fileContent .= "open ".$_POST['adrServLocal']." ".$_POST['mdp']."\r\n";

                foreach ($arrayExtension as $extension) {

                    $fileContent .= "!mv ".$array['nom_package'].$extension." ".$array['nouveau_nom'].$extension."\r\n";

                }

                $fileContent .= "cd /".$_POST['packageLocal']."/".$array['repertoire']."\r\n";

                $fileContent .= "mput ".$array['nouveau_nom'].".*\r\n";

                $fileContent .= "sync ".$_POST['adrServDist']." ".$array['nouveau_nom'].".fil\r\n";

                foreach ($arrayExtension as $extension) {

                    $fileContent .= "!rm ".$array['nouveau_nom'].$extension."\r\n"; //suppression locale
                }
            }


            //création des répertoires des images de base
            $fileContent .= "open ".$_POST['adrServLocal']." ".$_POST['mdp']."\r\n";

            $sql = 'SELECT distinct repertoire '.
                   'FROM `'.$prefix.'_images_de_base` '.
                   'LEFT JOIN `fusion` '.
                   'ON id_idb = arg1 '.
                   'AND etape = 9 '.
                   'WHERE (arg2 IS NULL '.
                   'OR arg2 = "non") '.
                   'AND id_idb > '.
                   '(SELECT arg2 FROM `fusion` WHERE etape = 4 AND arg1 = "maxIdIdb")';

            $query = mysql_query($sql);

            while ($array = mysql_fetch_array($query)) {

                $fileContent .= "cd /".$_POST['idbLocal']."\r\n";

                $arrayDirectory = explode('/', $array['repertoire'], -1);
                    
                foreach ($arrayDirectory as $directory) {

                    $fileContent .= "md ".$directory."\r\n";

                    $fileContent .= "cd ".$directory."\r\n";
                }
            }


            $sql = 'SELECT repertoire, nom_idb, '.
                   'if (nom_idb LIKE "'.$prefix.'\_%",nom_idb,CONCAT("'.$prefix.'_",nom_idb)) AS nouveau_nom '.
                   'FROM `'.$prefix.'_images_de_base` '.
                   'LEFT JOIN `fusion` '.
                   'ON id_idb = arg1 '.
                   'AND etape = 9 '.
                   'WHERE (arg2 IS NULL '.
                   'OR arg2 = "non") '.
                   'AND id_idb > '.
                   '(SELECT arg2 FROM `fusion` WHERE etape = 4 AND arg1 = "maxIdIdb")';

            $query = mysql_query($sql);


            while ($array = mysql_fetch_array($query)) {

                $fileContent .= "open ".$_POST['adrServDist']." ".$_POST['mdp']."\r\n";

                $fileContent .= "cd /".$_POST['idbDist']."/".$array['repertoire']."\r\n";

                $fileContent .= "get ".$array['nom_idb']."\r\n";

                $fileContent .= "open ".$_POST['adrServLocal']." ".$_POST['mdp']."\r\n";

                $fileContent .= "!mv ".$array['nom_idb']." ".$array['nouveau_nom']."\r\n";

                $fileContent .= "cd /".$_POST['idbLocal']."/".$array['repertoire']."\r\n";

                $fileContent .= "put ".$array['nouveau_nom']."\r\n";

                $fileContent .= "sync ".$_POST['adrServDist']." ".$array['nouveau_nom']."\r\n";

                $fileContent .= "!rm ".$array['nouveau_nom']."\r\n";
            }



            //création des répertoires pour les postinstall scripts
            $fileContent .= "open ".$_POST['adrServLocal']." ".$_POST['mdp']."\r\n";

            $sql = 'SELECT distinct repertoire '.
                   'FROM `'.$prefix.'_postinstall_scripts`';

            $query = mysql_query($sql);

            while ($array = mysql_fetch_array($query)) {

                $fileContent .= "cd /".$_POST['pisLocal']."\r\n";

                // ici on split
                $arrayDirectory = explode('/', $array['repertoire'], -1);
                    
                // ici on se déplace pour créer chaque 
                foreach ($arrayDirectory as $directory) {

                    $fileContent .= "md ".$directory."\r\n";

                    $fileContent .= "cd ".$directory."\r\n";
                }
            }



            $sql = 'SELECT repertoire, nom_script, '.
                   'if (nom_script LIKE "'.$prefix.'\_%", nom_script, CONCAT("'.$prefix.'_",nom_script)) AS nouveau_nom '.
                   'FROM `'.$prefix.'_postinstall_scripts`';

            $query = mysql_query($sql);


            while ($array = mysql_fetch_array($query)) {

                $fileContent .= "open ".$_POST['adrServDist']." ".$_POST['mdp']."\r\n";

                $fileContent .= "cd /".$_POST['pisDist']."/".$array['repertoire']."\r\n";

                $fileContent .= "get ".$array['nom_script']."\r\n";

                $fileContent .= "open ".$_POST['adrServLocal']." ".$_POST['mdp']."\r\n";

                $fileContent .= "!mv ".$array['nom_script']." ".$array['nouveau_nom']."\r\n";

                $fileContent .= "cd /".$_POST['pisLocal']."/".$array['repertoire']."\r\n";

                $fileContent .= "put ".$array['nouveau_nom']."\r\n";

                $fileContent .= "!rm ".$array['nouveau_nom']."\r\n";
            }


            //création des répertoires pour les predeinstall scripts
            $fileContent .= "open ".$_POST['adrServLocal']." ".$_POST['mdp']."\r\n";

            $sql = 'SELECT distinct repertoire '.
                   'FROM `'.$prefix.'_predeinstall_scripts`';

            $query = mysql_query($sql);

            while ($array = mysql_fetch_array($query)) {

                $fileContent .= "cd /".$_POST['pdisLocal']."\r\n";

                // ici on split
                $arrayDirectory = explode('/', $array['repertoire'], -1);
                    
                // ici on se déplace pour créer chaque 
                foreach ($arrayDirectory as $directory) {

                    $fileContent .= "md ".$directory."\r\n";

                    $fileContent .= "cd ".$directory."\r\n";
                }
            }


            $sql = 'SELECT repertoire, nom_script, '.
                   'if (nom_script LIKE "'.$prefix.'\_%", nom_script, CONCAT("'.$prefix.'_",nom_script)) AS nouveau_nom '.
                   'FROM `'.$prefix.'_predeinstall_scripts`';

            $query = mysql_query($sql);

            while ($array = mysql_fetch_array($query)) {

                $fileContent .= "open ".$_POST['adrServDist']." ".$_POST['mdp']."\r\n";

                $fileContent .= "cd /".$_POST['pdisDist']."/".$array['repertoire']."\r\n";

                $fileContent .= "get ".$array['nom_script']."\r\n";

                $fileContent .= "open ".$_POST['adrServLocal']." ".$_POST['mdp']."\r\n";

                $fileContent .= "!mv ".$array['nom_script']." ".$array['nouveau_nom']."\r\n";

                $fileContent .= "cd /".$_POST['pdisLocal']."/".$array['repertoire']."\r\n";

                $fileContent .= "put ".$array['nouveau_nom']."\r\n";

                $fileContent .= "!rm ".$array['nouveau_nom']."\r\n";
            }

            $fileContent .= "exit\r\n";

                

            header("Content-Disposition: attachment; filename=".$prefix.'.rbc');
            header("content-type: text/plain; charset=ISO-8859-15");
            flush(); 
            echo $fileContent;
            exit();

        } else {

            $notification .= 'Veuillez saisir des adresses IP valides <br />';
        }
    } else {

        $notification .= 'Veuillez remplir tous les champs <br />';
    }
 }
    

require_once '../layout.php';
?>