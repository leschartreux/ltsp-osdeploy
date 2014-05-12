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
$style .= '<link rel="stylesheet" href="../style/fusion.css"  type="text/css" media="screen" />';

/**
 * fichier(s) JS associé à la page
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
 * traitetement du formulaire de l'étape 5  *
 *                                          *
 ********************************************/
if (isset($_POST['submit'])) {

    while ($row = current($_POST)) {

        if ($row === 'oui') {

            $idLogiciel = intval(key($_POST));

            $row = next($_POST);
            $idLogicielDist = intval($row);
            
            $row = next($_POST);

            $priorite = $row;

            $sql = 'UPDATE `'.$prefix.'_logiciels` '.
                   'SET id_logiciel = '.$idLogiciel.', '.
                   'priorite = '.$priorite.' '.
                   'WHERE id_logiciel = '.$idLogicielDist;
            
            mysql_query($sql);



            $sql = 'UPDATE `'.$prefix.'_packages` '.
                   'SET id_logiciel = '.$idLogiciel.' '.
                   'WHERE id_logiciel = '.$idLogicielDist;

            mysql_query($sql);

            $sql = 'UPDATE `'.$prefix.'_images_de_base` '.
                   'SET id_os = '.$idLogiciel.' '.
                   'WHERE id_os = '.$idLogicielDist;

            mysql_query($sql);

            $sql = 'UPDATE `'.$prefix.'_pis_est_associe_a` '.
                   'SET id_logiciel = '.$idLogiciel.' '.
                   'WHERE id_logiciel = '.$idLogicielDist;

            mysql_query($sql);


            $sql = 'UPDATE `'.$prefix.'_pdis_est_associe_a` '.
                   'SET id_logiciel = '.$idLogiciel.' '.
                   'WHERE id_logiciel = '.$idLogicielDist;

            mysql_query($sql);


        } else if ($row === 'non') {

            $row = next($_POST);

            $idLogicielDist = intval($row);

            $row = next($_POST);
            $row = next($_POST);

            $sql = 'UPDATE `'.$prefix.'_logiciels` '.
                   'SET nom_logiciel = CONCAT("['.strtoupper($prefix).'] ", nom_logiciel) '.
                   'WHERE id_logiciel = '.$idLogicielDist;

            $query = mysql_query($sql);

            $notification .= 'Le logiciel '.$row.' a été renommé ['.$prefix.'] '.$row.'<br />';
        }

        next($_POST);
    }
 }



/**********************************************************
 *                                                        *
 *             Etape 6                                    *
 *                                                        *
 **********************************************************/
$sql = 'INSERT INTO `fusion` (prefixe, etape) VALUES ("'.$prefix.'", 6)';

$query = mysql_query($sql);

// étape 6 : 

$sql = 'INSERT INTO `fusion` '.
       'SELECT "'.$prefix.'", 6,  a.id_package, b.id_package '.
       'FROM `packages` AS a, `'.$prefix.'_packages` AS b '.
       'WHERE a.id_logiciel = b.id_logiciel '.
       'AND a.specificite = b.specificite '.
       'AND ( a.specificite = "aucune" '.
       'OR a.valeur_specificite = b.valeur_specificite ) '.
       'AND a.nom_package = b.nom_package '.
       'AND a.id_package != b.id_package';

mysql_query($sql);


$sql = 'UPDATE '.$prefix.'_packages AS a, fusion AS b '.
       'SET a.id_package = arg1 '.
       'WHERE a.id_package = arg2';

mysql_query($sql);


$sql = 'UPDATE '.$prefix.'_package_est_installe_sur AS a, fusion AS b '.
       'SET a.id_package = arg1 '.
       'WHERE a.id_package = arg2';

mysql_query($sql);





/*****************************************************
 *                                                   *
 *     Formulaire de l'étape 6                       *
 *                                                   *
 *****************************************************/

$content .= '<h2>Recherche des packages différents relatifs aux mêmes logiciels</h2>';

$sql = 'SELECT a.id_package, '.
       'b.id_package AS id_package_dist, '.
       'a.nom_package AS nom_package_local, '.
       'b.nom_package AS nom_package_dist, '.
       'b.specificite AS specificite_dist, '.
       'b.valeur_specificite AS valeur_specificite_dist '.
       'FROM `packages` AS a, '.
       '`'.$prefix.'_packages` AS b '.
       'WHERE a.id_logiciel = b.id_logiciel '.
       'AND a.specificite = b.specificite '.
       'AND ( a.specificite = "aucune" '.
       'OR a.valeur_specificite = b.valeur_specificite ) '.
       'AND a.nom_package != b.nom_package '.
       'AND a.id_package != b.id_package';

$query = mysql_query($sql);

$nbResult =  mysql_num_rows($query);

if ($nbResult > 0) {

    $content .= '<form action="step7.php" method="post">';

    $content .= '<table>';

    $content .= '<thead>';

    $content .= '<tr>';

    $content .= '<td>Nom du package local</td>';

    $content .= '<td>Nom du package distant</td>';

    $content .= '<td>Spécificité</td>';

    $content .= '<td>Valeur de la spécificité</td>';

    $content .= '<td>Oui</td>';

    $content .= '<td>Non</td>';

    $content .= '</tr>';

    $content .= '</thead>';

    $content .= '<tbody>';

    while ($array = mysql_fetch_array($query)) {

        $content .= '<tr>';

        $content .= '<td>'.$array['nom_package_local'].'</td>';

        $content .= '<td>'.$array['nom_package_dist'].'</td>';

        $content .= '<td>'.$array['specificite_dist'].'</td>';

        $content .= '<td>'.$array['valeur_specificite_dist'].'</td>';

        $content .= '<td><input type="radio" name="'.$array['id_package'].'" checked="checked" value="oui" /></td>';

        $content .= '<td><input type="radio" name="'.$array['id_package'].'" value="non" /></td>';

        $content .= '</tr>';

        $content .= '<input type="hidden" name="dist_'.$array['id_package_dist'].'" value="'.$array['id_package_dist'].'" />';
    }

    $content .= '<tbody>';

    $content .= '</table>';

    $content .= '<input type="submit" name="submit" value="Valider" />';

    $content .= '</form>';

 } else {

    $content .= '<p>Aucune action n\'a été nécéssaire à cette étape</p>';

    $content .= '<p><a href="step7.php">Suivant</a></p>';
 }

require_once '../layout.php';

?>