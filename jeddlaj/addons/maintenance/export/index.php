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
 * @subpackage Export
 * @author     Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license    GPL v2
 */
require_once '../UtilsMaintenance.php';

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
$pageTitle = 'Export d\'une base de données';

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

/*****************************************
 *                                       *
 *            Formulaire                 *
 *                                       *
 *****************************************/

$content .= '<h2>Export d\'une base de données locale</h2>';

$content .= '<form action="index.php" method="post">';

$content .= '<p>';

$content .= '<label for="prefixdblocal">Préfix des tables de la base de donnée : </label>';

$content .= '<input type="text" id="prefixdblocal" name="prefixdblocal" />';

$content .= '<input type="submit" name="exportlocal" value="Exporter la base locale" />';

$content .= '</p>';

$content .= '</form>';

$content .= '<h2>Export d\'une base de données distante</h2>';

$content .= '<form action="index.php" method="post">';

$content .= '<p>';

$content .= '<label for="login">Login : </label>';

$content .= '<input type="text" name="login" id="login" />';

$content .= '</p><p>';

$content .= '<label for="password">Mot de passe : </label>';

$content .= '<input type="password" name="password" id="password" />';

$content .= '</p><p>';

$content .= '<label for="host">Hôte distant : </label>';

$content .= '<input type="text" name="host" id="host" />';

$content .= '</p><p>';

$content .= '<label for="db">Nom de la base de données : </label>';

$content .= '<input type="text" name="db" id="db" />';

$content .= '</p><p>';

$content .= '<label for="prefixdbdist">Préfix de la base de donnée distante : </label>';

$content .= '<input type="text" name="prefixdbdist" id="prefixdbdist" />';

$content .= '</p><p>';

$content .= '<input type="submit" name="exportdistant" value="Exporter la base distante" />';

$content .= '</p>';

$content .= '</form>';

$content .= '<p><a href="../index.php" title="retour">Retour</a></p>';


/*****************************************
 *                                       *
 *            Traitement                 *
 *                                       *
 *****************************************/

if (isset($_POST['exportlocal']) || isset($_POST['exportdistant'])) {

    if (isset($_POST['exportlocal'])) {

        require_once '../../../DBParDefaut.php';

        if (preg_match('#^[a-zA-Z]*$#', $_POST['prefixdblocal']) || $_POST['prefixdblocal'] === '') {

            $prefix = $_POST['prefixdblocal'];

        } else {

            $prefix = false;

            $notification .= 'Le préfixe doit contenir uniquement des lettres minuscules et/ou majuscules';
        }

    } else if (isset($_POST['exportdistant'])) {

        $user = trim(htmlspecialchars($_POST['login'], ENT_QUOTES));

        $pwd = $_POST['password'];

        $host = trim(htmlspecialchars($_POST['host'], ENT_QUOTES));

        $db = trim(htmlspecialchars($_POST['db'], ENT_QUOTES));

        if (preg_match('#^[a-zA-Z]*$#', $_POST['prefixdbdist'])) {

            $prefix = $_POST['prefixdbdist'];

        } else {

            $prefix = false;

            $notification .= 'Le préfixe doit contenir uniquement des lettres minuscules et/ou majuscules';
        }
    }
 
    if ($prefix !== false) {

        mysql_connect($host, $user, $pwd) or die('Connexion à la base impossible');

        mysql_select_db($db);

        exporte_dump($prefix);

        $notification .= 'Export réalisé avec succès<br />';
    }
    
 }

require_once '../layout.php';
?>