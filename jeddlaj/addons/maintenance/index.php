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
 * @category Addons
 * @package  Maintenance
 * @author   Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license  GPL v2
 */

/**
 * titre de la fenetre
 */
$title = '';

/**
 * chemin vers le favicon
 */
$favicon = '../../ICONES/favicon.ico';

/**
 * titre de la page
 */
$pageTitle = 'Maintenance';

/**
 * fichier(s) css associé à la page
 */
$style = '<link rel="stylesheet" href="../../CSS/g.css"  type="text/css" media="screen" />';

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


$content .= '<ul>';

$content .= '<li><a href="export/index.php" title="exporter une base de données">Exporter une base de donnée</a></li>';

$content .= '<li><a href="fusion/index.php" title="fusionner deux bases de données">Fusionner deux bases de données</a></li>';

$content .= '<li><a href="prefixe/index.php" title="préfixer les tables des groupes locaux">Préfixer les tables des groupes locaux</a></li>';

$content .= '</ul>';

$content .= '<p><a href="../../index.php" title="retour à l\'accueil de JeDDLaJ">Retour à l\'accueil</a></p>';

require_once 'layout.php';
?>
