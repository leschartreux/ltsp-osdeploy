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

/************************************************
 *                                              *
 *    traitement du formulaire de l'étape 3     *
 *                                              *
 ************************************************/
if (isset($_POST['supprLog'])) {

    if ($_POST['supprLog'] === 'oui') {

        $sql = 'DELETE a,b,c,d,e '.
               'FROM `'.$prefix.'_logiciels` AS a '.
               'LEFT JOIN `'.$prefix.'_packages` AS b '.
               'ON a.id_logiciel = b.id_logiciel '.
               'LEFT JOIN `'.$prefix.'_images_de_base` AS c '.
               'ON a.id_logiciel = c.id_os '.
               'LEFT JOIN `'.$prefix.'_pis_est_associe_a` AS d '.
               'ON a.id_logiciel = d.id_logiciel '.
               'LEFT JOIN `'.$prefix.'_pdis_est_associe_a` AS e '.
               'ON a.id_logiciel = e.id_logiciel '.
               'WHERE a.visible = "non"';

        $query = mysql_query($sql);

        $notification .= 'Les logiciels cachés de la base distante ont été supprimés<br />';

    } else {

        $notification .= 'Les logiciels cachés de la base distante n\'ont pas été supprimés<br />';
    }
 }

/*********************************************************
 *                                                       *
 *      Etape 4                                          *
 *                                                       *
 *********************************************************/
//insertion du prefix dans la table fusion
$sql = 'INSERT INTO `fusion` (prefixe, etape) VALUES ("'.$prefix.'", 4)';

$query = mysql_query($sql);

// recalage des id_logiciel sur la base distante
$sql           = 'SELECT MAX(id_logiciel) AS max_local FROM `logiciels`;';
$query         = mysql_query($sql);
$maxIdLogLocal = mysql_result($query, 0);

$sql             = 'SELECT MAX(id_logiciel) AS max_distant FROM `'.$prefix.'_logiciels`';
$query           = mysql_query($sql);
$maxIdLogDistant = mysql_result($query, 0);

$offsetLogiciel = ($maxIdLogLocal > $maxIdLogDistant) ? $maxIdLogLocal : $maxIdLogDistant;

$sql = 'UPDATE `'.$prefix.'_logiciels` '.
       'SET id_logiciel = id_logiciel + '.$offsetLogiciel.' '.
       'WHERE id_logiciel <= '.$offsetLogiciel.';';

$query = mysql_query($sql);



$sql = 'UPDATE `'.$prefix.'_packages` '.
       'SET id_logiciel = id_logiciel + '.$offsetLogiciel.' '.
       'WHERE id_logiciel <= '.$offsetLogiciel.';';

$query = mysql_query($sql);

$sql = 'UPDATE `'.$prefix.'_images_de_base` '.
       'SET id_os = id_os + '.$offsetLogiciel.' '.
       'WHERE id_os <= '.$offsetLogiciel.';';

$query = mysql_query($sql);


$sql = 'UPDATE `'.$prefix.'_pis_est_associe_a` '.
       'SET id_logiciel = id_logiciel + '.$offsetLogiciel.' '.
       'WHERE id_logiciel <= '.$offsetLogiciel.';';

$query = mysql_query($sql);




$sql = 'UPDATE `'.$prefix.'_pdis_est_associe_a` '.
       'SET id_logiciel = id_logiciel + '.$offsetLogiciel.' '.
       'WHERE id_logiciel <= '.$offsetLogiciel.';';

$query = mysql_query($sql);



//recalage des id_package sur la base distante
$sql           = 'SELECT MAX(id_package) AS max_local FROM `packages`;';
$query         = mysql_query($sql);
$maxIdPacLocal = mysql_result($query, 0);

$sql             = 'SELECT MAX(id_package) AS max_distant FROM `'.$prefix.'_packages`;';
$query           = mysql_query($sql);
$maxIdPacDistant = mysql_result($query, 0);

$offsetPackage = ($maxIdPacLocal > $maxIdPacDistant) ? $maxIdPacLocal : $maxIdPacDistant;


$sql = 'UPDATE `'.$prefix.'_packages` '.
       'SET id_package = id_package + '.$offsetPackage.' '.
       'WHERE id_package <= '.$offsetPackage.';';

$query = mysql_query($sql);


$sql = 'UPDATE `'.$prefix.'_package_est_installe_sur` '.
       'SET id_package = id_package + '.$offsetPackage.' '.
       'WHERE id_package <= '.$offsetPackage.';';

$query = mysql_query($sql);



//recalage des id_idb
$sql           = 'SELECT MAX(id_idb) AS max_local FROM `images_de_base`;';
$query         = mysql_query($sql);
$maxIdIdbLocal = mysql_result($query, 0);

$sql             = 'SELECT MAX(id_idb) AS max_distant FROM `'.$prefix.'_images_de_base`;';
$query           = mysql_query($sql);
$maxIdIdbDistant = mysql_result($query, 0);

$offsetIdb = ($maxIdIdbLocal > $maxIdIdbDistant) ? $maxIdIdbLocal : $maxIdIdbDistant;


$sql = 'UPDATE `'.$prefix.'_images_de_base` '.
       'SET id_idb = id_idb + '.$offsetIdb.' '.
       'WHERE id_idb <= '.$offsetIdb.';';

$query = mysql_query($sql);


$sql = 'UPDATE `'.$prefix.'_idb_est_installe_sur` '.
       'SET id_idb = id_idb + '.$offsetIdb.' '.
       'WHERE id_idb <= '.$offsetIdb.';';

$query = mysql_query($sql);



//recalage des id_script des postinstall_scripts
$sql           = 'SELECT MAX(id_script) AS max_local FROM `postinstall_scripts`;';
$query         = mysql_query($sql);
$maxIdPisLocal = mysql_result($query, 0);

$sql             = 'SELECT MAX(id_script) AS max_distant FROM `'.$prefix.'_postinstall_scripts`;';
$query           = mysql_query($sql);
$maxIdPisDistant = mysql_result($query, 0);

$offsetPis = ($maxIdPisLocal > $maxIdPisDistant) ? $maxIdPisLocal : $maxIdPisDistant;


$sql = 'UPDATE `'.$prefix.'_postinstall_scripts` '.
       'SET id_script = id_script + '.$offsetPis.' '.
       'WHERE id_script <= '.$offsetPis.';';

$query = mysql_query($sql);


$sql = 'UPDATE `'.$prefix.'_pis_est_associe_a` '.
       'SET id_script = id_script + '.$offsetPis.' '.
       'WHERE id_script <= '.$offsetPis.';';

$query = mysql_query($sql);




//recalage des id_script des predeinstall_scripts
$sql            = 'SELECT MAX(id_script) AS max_local FROM `predeinstall_scripts`;';
$query          = mysql_query($sql);
$maxIdPdisLocal = mysql_result($query, 0);

$sql              = 'SELECT MAX(id_script) AS max_distant FROM `'.$prefix.'_predeinstall_scripts`;';
$query            = mysql_query($sql);
$maxIdPdisDistant = mysql_result($query, 0);

$offsetPdis = ($maxIdPdisLocal > $maxIdPdisDistant) ? $maxIdPdisLocal : $maxIdPdisDistant;

$sql = 'UPDATE `'.$prefix.'_predeinstall_scripts` '.
       'SET id_script = id_script + '.$offsetPdis.' '.
       'WHERE id_script <= '.$offsetPdis.';';

$query = mysql_query($sql);


$sql = 'UPDATE `'.$prefix.'_pdis_est_associe_a` '.
       'SET id_script = id_script + '.$offsetPdis.' '.
       'WHERE id_script <= '.$offsetPdis.';';

$query = mysql_query($sql);


$content .= '<h2>Décalage des id dans la base distante</h2>';

$content .= '<p>Les identifiants des tables '.$prefix.'_logiciel, '.$prefix.'_packages, '.$prefix.'_images_de_base, '.$prefix.'_postinstall_scripts et '.$prefix.'_predeinstall_scripts (ainsi que des tables de liaisons associées) ont été décalés de façon à ce que le plus petit id des table distantes soit supérieur à l\'id le plus grand des tables locales</p>';

$content .= '<p><a href="step5.php">Suivant</a></p>';

require_once '../layout.php';
?>