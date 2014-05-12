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
 *      Préfixage des noms de packages      *
 *                                          *
 ********************************************/
$sql = 'INSERT INTO `fusion` (prefixe, etape) VALUES ("'.$prefix.'", 11)';

$query = mysql_query($sql);

$content .= '<h2>Finalisation de la fusion</h2>';

$content .= '<ul>';

$content .= '<li>Préfixage des noms des packages de la base distantes</li>';

$sql = 'UPDATE `'.$prefix.'_packages` '.
       'SET nom_package = CONCAT("'.$prefix.'_",nom_package) '.
       'WHERE nom_package NOT LIKE "'.$prefix.'\_%"';

$query = mysql_query($sql);


/********************************************
 *                                          *
 *  Préfixage du nom des images de base     *
 *                                          *
 ********************************************/
$content .= '<li>Préfixage des noms des images de la base distantes</li>';

$sql = 'UPDATE `'.$prefix.'_images_de_base` '.
       'SET nom_idb = CONCAT("'.$prefix.'_",nom_idb) '.
       'WHERE nom_idb NOT LIKE "'.$prefix.'\_%"';

$query = mysql_query($sql);


/***********************************************
 *                                             *
 *  Préfixage des noms de postinstall script   *
 *                                             *
 ***********************************************/
$content .= '<li>Préfixage des noms de postinstall scripts distants</li>';

$sql = 'UPDATE `'.$prefix.'_postinstall_scripts` '.
       'SET nom_script = CONCAT("'.$prefix.'_",nom_script) '.
       'WHERE nom_script NOT LIKE "'.$prefix.'\_%"';

$query = mysql_query($sql);


/******************************************************
 *                                                    *
 *     Préfixage des noms de predeinstall scripts     *
 *                                                    *
 ******************************************************/
$content .= '<li>Préfixage des noms de predeinstall scripts distants</li>';

$sql = 'UPDATE `'.$prefix.'_predeinstall_scripts` '.
       'SET nom_script = CONCAT("'.$prefix.'_",nom_script) '.
       'WHERE nom_script NOT LIKE "'.$prefix.'\_%"';

$query = mysql_query($sql);


/********************************************
 *                                          *
 *         Insertion des données            *
 *                                          *
 ********************************************/

// ordinateur
$sql = 'INSERT IGNORE INTO `ordinateurs` '.
       '(SELECT * FROM `'.$prefix.'_ordinateurs`)';

$query = mysql_query($sql);


// composants
$sql = 'INSERT IGNORE INTO `composants` '.
       '(SELECT * FROM `'.$prefix.'_composants`)';

$query = mysql_query($sql);


// logiciels
$sql = 'INSERT IGNORE INTO `logiciels` '.
       '(SELECT * FROM `'.$prefix.'_logiciels`)';

$query = mysql_query($sql);

// composant_est_installe_sur
$sql = 'INSERT IGNORE INTO `composant_est_installe_sur` '.
       '(SELECT id_composant, subsys, a.nom_dns, id_comp_sur, ajout '.
       'FROM `'.$prefix.'_composant_est_installe_sur` AS a '.
       'INNER JOIN `ordinateurs` AS b '.
       'ON a.nom_dns = b.nom_dns)';

$query = mysql_query($sql);


// ord_appartient_a_gpe
$sql = 'INSERT IGNORE INTO `ord_appartient_a_gpe` '.
       '(SELECT b.nom_dns, b.nom_groupe '.
       'FROM `ordinateurs` AS a '.
       'INNER JOIN `'.$prefix.'_ord_appartient_a_gpe` AS b '.
       'ON a.nom_dns = b.nom_dns '.
       'INNER JOIN `'.$prefix.'_groupes` AS c '.
       'ON b.nom_groupe = c.nom_groupe)';

$query = mysql_query($sql);

// groupes
$sql = 'INSERT IGNORE INTO `groupes` '.
       '(SELECT c.nom_groupe, description_groupe, photo '.
       'FROM `ordinateurs` AS a '.
       'INNER JOIN `'.$prefix.'_ord_appartient_a_gpe` AS b '.
       'ON a.nom_dns = b.nom_dns '.
       'INNER JOIN `'.$prefix.'_groupes` AS c '.
       'ON b.nom_groupe = c.nom_groupe)';

$query = mysql_query($sql);

// groupe_est_inclus_dans_gpe
$sql = 'INSERT IGNORE INTO `gpe_est_inclus_dans_gpe` '.
       '(SELECT b.nom_groupe_inclus, b.nom_groupe '.
       'FROM `groupes` AS a '.
       'INNER JOIN `'.$prefix.'_gpe_est_inclus_dans_gpe` AS b '.
       'ON a.nom_groupe = b.nom_groupe_inclus '.
       'INNER JOIN `groupes` AS c '.
       'ON b.nom_groupe = c.nom_groupe)';

$query = mysql_query($sql);


// insertion des ordinateurs du groupe "[PREFIX] tous les ordinateurs" dans le groupe local "tous les ordinateurs"
$sql = 'INSERT IGNORE INTO `ord_appartient_a_gpe` '.
       '(SELECT b.nom_dns, "tous les ordinateurs" AS nom_groupe '.
       'FROM `'.$prefix.'_ordinateurs` AS a '.
       'INNER JOIN `'.$prefix.'_ord_appartient_a_gpe` AS b '.
       'ON a.nom_dns = b.nom_dns '.
       'WHERE b.nom_groupe = "['.strtoupper($prefix).'] tous les ordinateurs")';

$query = mysql_query($sql);


// packages
$sql = 'INSERT IGNORE INTO `packages` '.
       '(SELECT id_package, a.id_logiciel, nom_package, repertoire, specificite, valeur_specificite '.
       'FROM `'.$prefix.'_packages` AS a '.
       'INNER JOIN `logiciels` AS b '.
       'ON a.id_logiciel = b.id_logiciel)';

$query = mysql_query($sql);

// package_est_installe_sur
$sql = 'INSERT IGNORE INTO `package_est_installe_sur` '.
       '(SELECT b.id_package, b.nom_dns, num_disque, num_partition, etat_package, date_install '.
       'FROM `packages` AS a '.
       'INNER JOIN `'.$prefix.'_package_est_installe_sur` AS b '.
       'ON a.id_package = b.id_package '.
       'INNER JOIN `ordinateurs` AS c '.
       'ON b.nom_dns = c.nom_dns)';

$query = mysql_query($sql);

// images de base
$sql = 'INSERT IGNORE INTO `images_de_base` '.
       '(SELECT id_idb, id_os, nom_idb, repertoire, specificite, valeur_specificite '.
       'FROM `'.$prefix.'_images_de_base` AS a '.
       'INNER JOIN `logiciels` AS b '.
       'ON a.id_os = b.id_logiciel)';

$query = mysql_query($sql);

// images de base est installé sur
$sql = 'INSERT IGNORE INTO `idb_est_installe_sur` '.
       '(SELECT b.id_idb, b.nom_dns, num_disque, num_partition, etat_idb, date_install, idb_active, cache, boot_options '.
       'FROM `images_de_base` AS a '.
       'INNER JOIN `'.$prefix.'_idb_est_installe_sur` AS b '.
       'ON a.id_idb = b.id_idb '.
       'INNER JOIN `ordinateurs` AS c '.
       'ON b.nom_dns = c.nom_dns)';

$query = mysql_query($sql);

// postinstall_scripts
$sql = 'INSERT IGNORE INTO `postinstall_scripts` '.
       '(SELECT id_script, repertoire, nom_script, applicable_a, valeur_application '.
       'FROM `'.$prefix.'_postinstall_scripts` AS a '.
       'INNER JOIN `ordinateurs` AS b '.
       'ON b.nom_dns = a.valeur_application '.
       'AND applicable_a = "nom_dns")';

$query = mysql_query($sql);

$sql = 'INSERT IGNORE INTO `postinstall_scripts` '.
       '(SELECT id_script, repertoire, nom_script, applicable_a, valeur_application '.
       'FROM `'.$prefix.'_postinstall_scripts` AS a '.
       'INNER JOIN `groupes` AS b '.
       'ON b.nom_groupe = a.valeur_application '.
       'AND applicable_a = "nom_groupe")';

$query = mysql_query($sql);

$sql = 'INSERT IGNORE INTO `postinstall_scripts` '.
       '(SELECT id_script, repertoire, nom_script, applicable_a, valeur_application '.
       'FROM `'.$prefix.'_postinstall_scripts` AS a '.
       'WHERE a.applicable_a = "rien_pour_l_instant")';

$query = mysql_query($sql);


// pis_est_associe_a
$sql = 'INSERT IGNORE INTO `pis_est_associe_a` '.
       '(SELECT b.id_script, b.id_logiciel '.
       'FROM `logiciels` AS a '.
       'INNER JOIN `'.$prefix.'_pis_est_associe_a` AS b '.
       'ON a.id_logiciel = b.id_logiciel '.
       'INNER JOIN `postinstall_scripts` AS c '.
       'ON b.id_script = c.id_script)';

$query = mysql_query($sql);

// predeinstall_scripts
$sql = 'INSERT IGNORE INTO `predeinstall_scripts` '.
       '(SELECT id_script, repertoire, nom_script, applicable_a, valeur_application '.
       'FROM `'.$prefix.'_predeinstall_scripts` AS a '.
       'INNER JOIN `ordinateurs` AS b '.
       'ON b.nom_dns = a.valeur_application '.
       'AND applicable_a = "nom_dns")';

$query = mysql_query($sql);

$sql = 'INSERT IGNORE INTO `predeinstall_scripts` '.
       '(SELECT id_script, repertoire, nom_script, applicable_a, valeur_application '.
       'FROM `'.$prefix.'_predeinstall_scripts` AS a '.
       'INNER JOIN `groupes` AS b '.
       'ON b.nom_groupe = a.valeur_application '.
       'AND applicable_a = "nom_groupe")';

$query = mysql_query($sql);

$sql = 'INSERT IGNORE INTO `predeinstall_scripts` '.
       '(SELECT id_script, repertoire, nom_script, applicable_a, valeur_application '.
       'FROM `'.$prefix.'_predeinstall_scripts` AS a '.
       'WHERE a.applicable_a = "rien_pour_l_instant")';

$query = mysql_query($sql);

// pdis_est_associe_a
$sql = 'INSERT IGNORE INTO `pdis_est_associe_a` '.
       '(SELECT b.id_script, b.id_logiciel FROM `logiciels` AS a '. 
       'INNER JOIN `'.$prefix.'_pdis_est_associe_a` AS b '.
       'ON a.id_logiciel = b.id_logiciel '.
       'INNER JOIN `predeinstall_scripts` AS c '.
       'ON b.id_script = c.id_script)';

$query = mysql_query($sql);

// partitions
$sql = 'INSERT IGNORE INTO `partitions` '.
       '(SELECT DISTINCT a.nom_dns, '.
       'a.num_disque, a.num_partition, '.
       'a.taille_partition, a.type_partition, '.
       'a.systeme, a.nom_partition, '.
       'a.linux_device '.
       'FROM `'.$prefix.'_partitions` AS a '.
       'INNER JOIN `ordinateurs` AS b '.
       'ON a.nom_dns = b.nom_dns '.
       'INNER JOIN `'.$prefix.'_stockages_de_masse` AS c '.
       'ON b.nom_dns = c.nom_dns)';

$query = mysql_query($sql);

// stockages_de_masse
$sql = 'INSERT IGNORE INTO `stockages_de_masse` '.
       '(SELECT a.nom_dns, a.type, '.
       'a.connectique, a.capacite, '.
       'a.num_disque, a.linux_device, '.
       'a.dd_a_partitionner '.
       'FROM `'.$prefix.'_stockages_de_masse` AS a '.
       'INNER JOIN `ordinateurs` AS b '.
       'ON a.nom_dns = b.nom_dns)';

$query = mysql_query($sql);

//depannage
$sql = 'INSERT IGNORE INTO `depannage` '.
       '(SELECT * '.
       'FROM `'.$prefix.'_depannage`)';
       
$query = mysql_query($sql);

$content .= '<li>Insertion des données distantes dans la base de données locale</li>';


/***************************************************
 *                                                 *
 *    Mise à jour de la visibilité des logiciels   *
 *                                                 *
 ***************************************************/
$sql = 'UPDATE `logiciels` as a, '.
       '`'.$prefix.'_logiciels` as b '.
       'SET a.visible = "oui" '.
       'WHERE a.visible = "non" '.
       'AND b.visible = "oui" '.
       'AND a.id_logiciel = b.id_logiciel';

$query = mysql_query($sql);

$content .= '<li>Mise à jour des visibilités des logiciels</li>';


/***********************************************
 *                                             *
 *   Mise à jour des priorités des logiciels   *
 *                                             *
 ***********************************************/
$sql = 'UPDATE `logiciels` AS a, '.
       '`'.$prefix.'_logiciels` AS b '.
       'SET a.priorite = b.priorite '.
       'WHERE a.id_logiciel = b.id_logiciel';

$query = mysql_query($sql);

$content .= '<li>Mise à jour des priorités des logiciels</li>';

$content .= '</ul>';


/********************************************
 *                                          *
 *     Suppression des tables prefixées     *
 *                                          *
 ********************************************/
$sql = 'SHOW TABLE STATUS LIKE "'.$prefix.'\_%"';

$query = mysql_query($sql);


while ($row = mysql_fetch_array($query)) {

    $sql = 'DROP TABLE `'.$row['Name'].'`';
 
      mysql_query($sql);

 }

$sql = 'DELETE FROM `fusion` WHERE prefixe = "'.$prefix.'"';

$query = mysql_query($sql);

//fin de la fusion
$content .= '<h2>fusion réalisé avec succès!</h2>';

$content .= '<p><a href="../index.php" title="retour">Retour</a></p>';

require_once '../layout.php';

?>