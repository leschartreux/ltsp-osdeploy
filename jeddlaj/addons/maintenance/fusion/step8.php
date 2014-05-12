<?php
/**
 * ************************** GPL STUFF *************************
 * ************************** ENGLISH *********************************
 *
 * Copyright notice :
 *
 * Copyright 2003 - 2010 G�rard Milhaud - Fr�d�ric Bloise
 * Copyright 2010 - 2011 Fr�d�ric Bloise - Salvucci Arnaud - G�rard Milhaud
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
 * Copyright 2003 - 2010 G�rard Milhaud - Fr�d�ric Bloise
 * Copyright 2010 - 2011 Fr�d�ric Bloise - Salvucci Arnaud - G�rard Milhaud
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
$pageTitle = 'Fusion de deux bases de donn�es';

/**
 * fichier(s) css associ� � la page
 */
$style = '<link rel="stylesheet" href="../../../CSS/g.css"  type="text/css" media="screen" />';
$style .= '<link rel="stylesheet" href="../style/fusion.css"  type="text/css" media="screen" />';

/**
 * fichier(s) JS associ� � la page
 */
$script  = '<script type="text/javascript" src="../script/jquery.js"></script>';
$script .= '<script type="text/javascript" src="../script/fusion.js"></script>';

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
 *    Traitement de l'�tape 7               *
 *                                          *
 ********************************************/

if (isset($_POST['submit'])) {

    while ($row = current($_POST)) {

        if ($row === 'oui') {

            $row = next($_POST);

            $idPackageDist = intval($row);

            $sql = 'INSERT INTO `fusion` (prefixe, etape, arg1, arg2) '.
                   'VALUES ("'.$prefix.'", 7, '.$idPackageDist.', "oui")';

            mysql_query($sql);

            $notification .= 'Les packages distants et locaux vont �tre fusionn�s<br />';

        } else if ($row === 'non') {

            $row = next($_POST);

            $idPackageDist = intval($row);

            $sql = 'INSERT INTO `fusion` (prefixe, etape, arg1, arg2) '.
                   'VALUES ("'.$prefix.'", 7, '.$idPackageDist.', "non")';

            mysql_query($sql);
        }

        next($_POST);
    }

    $sql = 'DELETE FROM `fusion` '.
           'WHERE etape = 7 '.
           'AND arg1 IS NULL '.
           'AND arg2 IS NULL';

    mysql_query($sql);
 }

/********************************************
 *                                          *
 *           Etape 8                        *
 *                                          *
 ********************************************/
$sql = 'INSERT INTO `fusion` (prefixe, etape) VALUES ("'.$prefix.'", 8)';

$query = mysql_query($sql);

$sql = 'INSERT INTO `fusion` '.
       'SELECT "'.$prefix.'", 8,  a.id_idb, b.id_idb '.
       'FROM `images_de_base` AS a, `'.$prefix.'_images_de_base` AS b '.
       'WHERE a.id_os = b.id_os '.
       'AND a.specificite = b.specificite '.
       'AND ( a.specificite = "aucune" '.
       'OR a.valeur_specificite = b.valeur_specificite ) '.
       'AND a.nom_idb = b.nom_idb '.
       'AND a.id_idb != b.id_idb';

mysql_query($sql);


$sql = 'UPDATE '.$prefix.'_images_de_base AS a, fusion AS b '.
       'SET a.id_idb = arg1 '.
       'WHERE a.id_idb = arg2';

mysql_query($sql);


$sql = 'UPDATE '.$prefix.'_idb_est_installe_sur AS a, fusion AS b '.
       'SET a.id_idb = arg1 '.
       'WHERE a.id_idb = arg2';

mysql_query($sql);


/*****************************************************
 *                                                   *
 *     Formulaire de l'�tape 8                       *
 *                                                   *
 *****************************************************/
$content .= '<h2>Recherche d\'images de base diff�rentes relatives au m�me logiciel</h2>';

$sql = 'SELECT a.id_idb, '.
       'b.id_idb AS id_idb_dist, '.
       'a.nom_idb AS nom_idb_local, '.
       'b.nom_idb AS nom_idb_dist, '.
       'b.specificite AS specificite_dist, '.
       'b.valeur_specificite AS valeur_specificite_dist '.
       'FROM `images_de_base` AS a, '.
       '`'.$prefix.'_images_de_base` AS b '.
       'WHERE a.id_os = b.id_os '.
       'AND a.specificite = b.specificite '.
       'AND ( a.specificite = "aucune" '.
       'OR a.valeur_specificite = b.valeur_specificite ) '.
       'AND a.nom_idb != b.nom_idb '.
       'AND a.id_idb != b.id_idb';

$query = mysql_query($sql);



$content .= '<p>Dans cette �tape, on recherche les images de base concernant le m�me logiciel ayant la m�me sp�cificit� et valeur de sp�cificit� mais un nom diff�rent.<br />

Dans ce formulaire : cochez oui pour remplacer les images de base distantes par les images de base locales.</p>';

$nbResult =  mysql_num_rows($query);

if ($nbResult > 0) {

    $content .= '<form action="step9.php" method="post">';

    $content .= '<table>';

    $content .= '<thead>';

    $content .= '<tr>';

    $content .= '<td>Nom de l\'image de base locale</td>';

    $content .= '<td>Nom de l\'image de base distante</td>';

    $content .= '<td>Sp�cificit�</td>';

    $content .= '<td>Valeur de la sp�cificit�</td>';

    $content .= '<td>Oui</td>';

    $content .= '<td>Non</td>';

    $content .= '</tr>';

    $content .= '</thead>';

    $content .= '<tbody>';

    while ($array = mysql_fetch_array($query)) {

        $content .= '<tr>';

        $content .= '<td>'.$array['nom_idb_local'].'</td>';

        $content .= '<td>'.$array['nom_idb_dist'].'</td>';

        $content .= '<td>'.$array['specificite_dist'].'</td>';

        $content .= '<td>'.$array['valeur_specificite_dist'].'</td>';

        $content .= '<td><input type="radio" name="'.$array['id_idb'].'" checked="checked" value="oui" /></td>';

        $content .= '<td><input type="radio" name="'.$array['id_idb'].'" value="non" /></td>';

        $content .= '</tr>';

        $content .= '<input type="hidden" name="dist_'.$array['id_idb_dist'].'" value="'.$array['id_idb_dist'].'" />';

    }

    $content .= '<tbody>';

    $content .= '</table>';

    $content .= '<input type="submit" name="submit" value="Valider" />';

    $content .= '</form>';

 } else {

    $content .= '<p><a href="step9.php">Suivant</a></p>';
 }

require_once '../layout.php';
?>