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
$style  = '<link rel="stylesheet" href="../../../CSS/g.css"  type="text/css" media="screen" />';
$style .= '<link rel="stylesheet" href="../style/fusion.css"  type="text/css" media="screen" />';

/**
 * fichier(s) JS associé à la page
 */
$script = '<script type="text/javascript" src="../script/jquery.js"></script>';
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
 *           Etape 5                        *
 *                                          *
 ********************************************/
$sql = 'INSERT INTO `fusion` (prefixe, etape) VALUES ("'.$prefix.'", 5)';

$query = mysql_query($sql);


// recherche des logiciels déjà existants
$sql = 'UPDATE `logiciels` AS a, '.
       '`packages` AS b, '.
       '`'.$prefix.'_logiciels` AS c, '.
       '`'.$prefix.'_packages` AS d '.
       'SET c.nom_logiciel = CONCAT("['.strtoupper($prefix).'] ",c.nom_logiciel) '.
       'WHERE a.id_logiciel = b.id_logiciel '.
       'AND c.id_logiciel = d.id_logiciel '.
       'AND a.nom_logiciel = c.nom_logiciel '.
       'AND a.version = c.version '.
       'AND a.nom_os = c.nom_os '.
       'AND b.specificite != d.specificite '.
       'AND (b.specificite = "aucune" '.
       'OR d.specificite = "aucune")';

$query = mysql_query($sql);

$notification .= 'Les packages ayant le même nom mais une spécificité différentes ont été renommés en <strong>['.strtoupper($prefix).'] nom-du-package</strong><br />';

$sql = 'UPDATE `logiciels` AS a, '.
       '`images_de_base` AS b, '.
       '`'.$prefix.'_logiciels` AS c,'.
       '`'.$prefix.'_images_de_base` AS d '.
       'SET c.nom_logiciel = CONCAT("['.strtoupper($prefix).'] ", '.
       'c.nom_logiciel) '.
       'WHERE a.id_logiciel = b.id_os '.
       'AND c.id_logiciel = d.id_os '.
       'AND a.nom_logiciel = c.nom_logiciel '.
       'AND a.version = c.version '.
       'AND a.nom_os = c.nom_os '.
       'AND b.specificite != d.specificite '.
       'AND (b.specificite = "aucune" '.
       'OR d.specificite = "aucune")';

$query = mysql_query($sql);

$notification .= 'Les images de base ayant le même nom mais des packages différents ont été renommés en <strong>['.strtoupper($prefix).'] nom-de-l-image-de-base</strong><br />';

$sql = 'UPDATE `logiciels` AS a '.
       'INNER JOIN `'.$prefix.'_logiciels` AS b '.
       'LEFT JOIN `packages` AS c '.
       'ON a.id_logiciel = c.id_logiciel '.
       'LEFT JOIN `'.$prefix.'_packages` AS d '.
       'ON b.id_logiciel = d.id_logiciel '.
       'SET b.nom_logiciel = CONCAT("['.strtoupper($prefix).'] ",c.nom_package) '.
       'WHERE a.nom_logiciel = b.nom_logiciel '.
       'AND a.version = b.version '.
       'AND a.nom_os = b.nom_os '.
       'AND ((c.id_logiciel IS NULL OR d.id_logiciel IS NULL) '.
       'AND (c.id_logiciel != d.id_logiciel))';

$query = mysql_query($sql);


$notification .= 'Les logiciels ayant le même nom mais des packages différents ont été renommés en <strong>['.strtoupper($prefix).'] nom-du-logiciel</strong><br />';
   

/************************************************
 *                                              *   
 *    Formulaire de l'étape 5                   *
 *                                              *
 ************************************************/
$content .= '<h2>Recherche des logiciels déjà existants</h2>';

$content .= '<p>Dans ce formulaire : cochez oui pour confirmer que le logiciel distant est le même que le logiciel local. Si les priorités des deux logiciels sont différentes, cochez la priorité que vous voulez conserver.</p>';

$sql = 'SELECT a.id_logiciel, b.id_logiciel AS id_logiciel_dist , b.nom_logiciel, b.version, '.
       'b.nom_os, a.priorite AS priorite_local, b.priorite AS priorite_distant '.
       'FROM `logiciels` AS a, `'.$prefix.'_logiciels` AS b '.
       'WHERE a.nom_logiciel = b.nom_logiciel '.
       'AND a.version = b.version '.
       'AND a.nom_os = b.nom_os';

$query = mysql_query($sql);

$nbResult =  mysql_num_rows($query);

if ($nbResult > 0) {

    $content .= '<form action="step6.php" method="post">';

    $content .= '<table>';

    $content .= '<thead>';

    $content .= '<tr>';

    $content .= '<td>Nom du logiciel</td>';

    $content .= '<td>Version du logiciel</td>';

    $content .= '<td>OS</td>';

    $content .= '<td>Priorité locale</td>';

    $content .= '<td>Priorité distante</td>';

    $content .= '<td>Oui</td>';

    $content .= '<td>Non</td>';

    $content .= '</tr>';

    $content .= '</thead>';

    $content .= '<tbody>';

    while ($array = mysql_fetch_array($query)) {

        $content .= '<tr>';

        $content .= '<td>'.$array['nom_logiciel'].'</td>';

        $content .= '<td>'.$array['version'].'</td>';

        $content .= '<td>'.$array['nom_os'].'</td>';

        $content .= '<td>'.$array['priorite_local'].'</td>';

        $content .= '<td>'.$array['priorite_distant'].'</td>';

        $content .= '<td><input type="radio" name="'.$array['id_logiciel'].'" checked="checked" value="oui" /></td>';

        $content .= '<td><input type="radio" name="'.$array['id_logiciel'].'" value="non" /></td>';

        $content .= '</tr>';

        if ($array['priorite_local'] !== $array['priorite_distant']) {

            $content .= '<input type="hidden" name="dist_'.$array['id_logiciel_dist'].'" value="'.$array['id_logiciel_dist'].'" />';

            $content .= '<tr class="choixPriorite">';

            $content .= '<td colspan="2">Choisissez la priorité à conserver pour ce logiciel : </td>';

            $content .= '<td><input type="radio" name="p_'.$array['id_logiciel'].'" checked="checked" value="'.$array['priorite_local'].'" /></td>';

            $content .= '<td><input type="radio" name="p_'.$array['id_logiciel'].'" value="'.$array['priorite_distant'].'" /></td>';

            $content .= '<td colspan="2">&nbsp;</td>';

            $content .= '</tr>';
             
        }

        if ($array['priorite_local'] === $array['priorite_distant']) {

            $content .= '<input type="hidden" name="dist_'.$array['id_logiciel_dist'].'" value="'.$array['id_logiciel_dist'].'" />';

            $content .= '<input type="hidden" name="p_'.$array['id_logiciel'].'" value="'.$array['priorite_local'].'" />';
        }

        $content .= '<input type="hidden" name="nomLogiciel_'.$array['id_logiciel'].'" value="'.$array['nom_logiciel'].'" />';
    }

    $content .= '<tbody>';

    $content .= '</table>';

    $content .= '<input type="submit" name="submit" value="Valider" />';

    $content .= '</form>';

 } else {

    $content .= '<p><a href="step6.php">Suivant</a></p>';
 }

require_once '../layout.php';


?>