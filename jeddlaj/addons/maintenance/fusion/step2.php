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

/************************************************
 *                                              *
 *    traitement du formulaire de l'étape 1     *
 *                                              *
 ************************************************/
if (isset($_POST['fusion'])) {

    if ($_POST['filename'] !== '') {

        $cleanPrefix = htmlspecialchars($_POST['filename'], ENT_QUOTES);

        $prefix = strtolower(substr($cleanPrefix, strrpos($cleanPrefix, '/') + 1, strrpos($cleanPrefix, '_') - strrpos($cleanPrefix, '/') -1));

        $filename = htmlspecialchars($_POST['filename'], ENT_QUOTES);
    }

    if ($_POST['typeFusion'] === 'nouvelle') {

        $sql = 'DELETE FROM `fusion` WHERE prefixe = "'.$prefix.'"';

        $query = mysql_query($sql);

        //import de la base distante dans la base locale
        import_dump($filename);

        $notification .= 'La base de données distante a été importée<br />';

    } elseif ($_POST['typeFusion'] === 'ancienne') {

        $sql = 'SELECT etape FROM `fusion` WHERE prefixe = "'.$prefix.'" ORDER BY etape DESC LIMIT 0,1';

        $query = mysql_query($sql);

        $step = mysql_result($query, 0);

        if ($step > 1) {

            header('Location: step'.$step.'.php');

        } else {

            header('Location: index.php');
        }
    }
 }

/****************************************************
 *                                                  *
 *          Etape 2                                 *
 *                                                  *
 ****************************************************/
//insertion du prefix dans la table fusion
$sql = 'INSERT INTO `fusion` (prefixe, etape) VALUES ("'.$prefix.'", 2)';

$query = mysql_query($sql);

// test noms de machine ou adresses mac identiques entre 
// les machines locale et les machines nouvellement importées
$content .= '<h2>Recherche de machine identique dans la base de données locale et la base de données distante</h2>';

$sql = 'SELECT a.nom_dns, a.adresse_mac '.
       'FROM `ordinateurs` AS a, '.
       '`'.$prefix.'_ordinateurs` AS b '.
       'WHERE a.nom_dns = b.nom_dns '.
       'OR (a.adresse_mac = b.adresse_mac '.
       'AND a.adresse_mac != "")';

$query = mysql_query($sql);

$nbMachineIdentique = mysql_num_rows($query);

if ($nbMachineIdentique !== 0) {

    $content .= 'Les machines suivantes comportent le même nom dns et/ou la même adresse mac.<br />';

    $content .= '<ul>';

    while ($row = mysql_fetch_array($query)) {

        $content .= '<li>La machine <strong>'.$row['nom_dns'].'</strong> avec l\'adresse mac <strong>'.$row['adresse_mac'].'</strong></li>';
    }

    $content .= '</ul>';

    $content .= 'Pour passer à l\'étape suivante vous devez effectuer une des actions suivantes pour chaque machine concernée : <br \>';

    $content .= '<ul>';

    $content .= '<li>Renommer la machine locale</li>';

    $content .= '<li>Changer l\'adresse mac de la machine locale</li>';

    $content .= '<li>Supprimer la machine locale ou distante. (La suppression de la machine distante nécéssite de réimporter la base)</li>';

    $content .= '</ul>';

    $content .= '<p><a href="../index.php">Retour</a></p>';

 } else {

    $content .= '<p><a href="step3.php">Suivant</a></p>';
 }

require_once '../layout.php';

?>