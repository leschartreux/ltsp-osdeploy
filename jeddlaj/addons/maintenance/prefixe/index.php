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
 * @subpackage Prefixe
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
$pageTitle = 'Pr�fixage des tables des groupes locaux';

/**
 * fichier(s) css associ� � la page
 */
$style = '<link rel="stylesheet" href="../../../CSS/g.css"  type="text/css" media="screen" />';

/**
 * fichier(s) JS associ� � la page
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

$content .= '<h2>Pr�fixage des tables des groupes locaux</h2>';

$content .= '<form action="index.php" method="post">';

$content .= '<p>';

$content .= '<label for="prefixtablelocal">Pr�fix des tables de la base de donn�e : </label>';

$content .= '<input type="text" id="prefixtablelocal" name="prefixtablelocal" />';

$content .= '</p>';

$content .= '<p>';

$content .= '<input type="submit" name="prefixelocal" value="Prefixer les tables locales" />';

$content .= '</p>';

$content .= '</form>';

$content .= '<p><a href="../index.php" title="retour">Retour</a></p>';


/*****************************************
 *                                       *
 *            Traitement                 *
 *                                       *
 *****************************************/
if (isset($_POST['prefixelocal'])) {

    require_once '../../../DBParDefaut.php';

    if (preg_match('#^[a-zA-Z]*$#', $_POST['prefixtablelocal'])) {

        $prefix = $_POST['prefixtablelocal'];

    } else {

        $prefix = false;

        $notification .= 'Le pr�fixe doit contenir uniquement des lettres minuscules et/ou majuscules';
    }
 

    if ($prefix !== false) {

        mysql_connect($host, $user, $pwd) or die('Connexion � la base impossible');

        mysql_select_db($db);

        /***********************************************
         *                                             *
         * Pr�fixage des groupes dans la table groupes *
         *                                             *
         ***********************************************/
        $sql = 'UPDATE `groupes` '.
               'SET nom_groupe = if( nom_groupe LIKE "['.strtoupper($prefix).']%", nom_groupe, '.
               'CONCAT( "['.strtoupper($prefix).'] ", nom_groupe ))';

        $query = mysql_query($sql);


        /***************************************************************
         *                                                             *
         * Pr�fixage des groupes dans la table gpe_est_inclus_dans_gpe *
         *                                                             *
         ***************************************************************/
        $sql = 'UPDATE `gpe_est_inclus_dans_gpe` '.
               'SET nom_groupe_inclus = if( nom_groupe_inclus LIKE "['.strtoupper($prefix).']%", nom_groupe_inclus, '.
               'CONCAT( "['.strtoupper($prefix).'] ", nom_groupe_inclus )), '.
               'nom_groupe = if( nom_groupe LIKE "['.strtoupper($prefix).']%", nom_groupe, '.
               'CONCAT( "['.strtoupper($prefix).'] ", nom_groupe ))';

        $query = mysql_query($sql);


        /************************************************************
         *                                                          *
         * Pr�fixage des groupes dans la table ord_appartient_a_gpe *
         *                                                          *
         ************************************************************/
        $sql = 'UPDATE `ord_appartient_a_gpe` '.
               'SET nom_groupe = if( nom_groupe LIKE "['.strtoupper($prefix).']%", nom_groupe, '.
               'CONCAT( "['.strtoupper($prefix).'] ", nom_groupe ))';

        $query = mysql_query($sql);


        /***********************************************************
         *                                                         *
         * Pr�fixage des groupes dans la table postinstall_scripts *
         *                                                         *
         ***********************************************************/
        $sql = 'UPDATE `postinstall_scripts` '.
               'SET valeur_application = if( valeur_application LIKE "['.strtoupper($prefix).']%", valeur_application, '.
               'CONCAT( "['.strtoupper($prefix).'] ", valeur_application )) '.
               'WHERE applicable_a = "nom_groupe"';

        $query = mysql_query($sql);


        /************************************************************
         *                                                          *
         * Pr�fixage des groupes dans la table predeinstall_scripts *
         *                                                          *
         ************************************************************/
        $sql = 'UPDATE `predeinstall_scripts` '.
               'SET valeur_application = if( valeur_application LIKE "['.strtoupper($prefix).']%", valeur_application, '.
               'CONCAT( "['.strtoupper($prefix).'] ", valeur_application )) '.
               'WHERE applicable_a = "nom_groupe"';

        $query = mysql_query($sql);

        /******************************************************************
         *                                                                *
         * Recr�ation du groupe tous les ordinateurs dans la table groupe *
         *                                                                *
         ******************************************************************/
        $sql = 'INSERT IGNORE INTO `groupes` '.
               'VALUES ("tous les ordinateurs", "Groupe par d�faut pour tous les ordinateurs", "classroom.jpg")';

        $query = mysql_query($sql);


        /********************************************
         *                                          *
         * Insertion des ordinateurs appartenant    *
         * aux groupe [PREFIX] tous les ordinateurs *
         * dans le groupe tous les ordinateurs      *
         *                                          *
         ********************************************/
        $sql = 'INSERT IGNORE INTO `ord_appartient_a_gpe` '.
               '(SELECT nom_dns, "tous les ordinateurs" AS nom_groupe '.
               'FROM `ord_appartient_a_gpe` '.
               'WHERE nom_groupe = "['.strtoupper($prefix).'] tous les ordinateurs")';

        $query = mysql_query($sql);

        $notification .= 'Les tables ont �t� pr�fix�es avec succ�s<br />';
    }
 }

require_once '../layout.php';
?>