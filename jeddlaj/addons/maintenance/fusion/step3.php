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


/*******************************************************
 *                                                     *
 *        Etape 3                                      *
 *                                                     *
 *******************************************************/
//insertion du prefix dans la table fusion
$sql = 'INSERT INTO `fusion` (prefixe, etape) VALUES ("'.$prefix.'", 3)';

$query = mysql_query($sql);


// tous les groupes sont renomm�s en [site distant] nom_groupe :
$sql = 'UPDATE `'.$prefix.'_groupes` '.
       'SET nom_groupe = CONCAT("['.strtoupper($prefix).'] ",nom_groupe) '.
       'WHERE nom_groupe NOT LIKE "['.strtoupper($prefix).'] %";';

$query = mysql_query($sql);

$sql = 'UPDATE `'.$prefix.'_gpe_est_inclus_dans_gpe` '.
       'SET nom_groupe = CONCAT("['.strtoupper($prefix).'] ",nom_groupe) '.
       'WHERE nom_groupe NOT LIKE "['.strtoupper($prefix).'] %";';

$query = mysql_query($sql);

$sql = 'UPDATE `'.$prefix.'_gpe_est_inclus_dans_gpe` '.
       'SET nom_groupe_inclus = CONCAT("['.strtoupper($prefix).'] ",nom_groupe_inclus) '.
       'WHERE nom_groupe_inclus NOT LIKE "['.strtoupper($prefix).'] %";';

$query = mysql_query($sql);

$sql = 'UPDATE `'.$prefix.'_ord_appartient_a_gpe` '.
       'SET nom_groupe = CONCAT("['.strtoupper($prefix).'] ",nom_groupe) '.
       'WHERE nom_groupe NOT LIKE "['.strtoupper($prefix).'] %";';

$query = mysql_query($sql);

$sql = 'UPDATE `'.$prefix.'_postinstall_scripts` '.
       'SET valeur_application = CONCAT("['.strtoupper($prefix).'] ",valeur_application) '.
       'WHERE applicable_a = "nom_groupe" '.
       'AND valeur_application NOT LIKE "['.strtoupper($prefix).'] %";';

$query = mysql_query($sql);

$sql = 'UPDATE `'.$prefix.'_predeinstall_scripts` '.
       'SET valeur_application = CONCAT("['.strtoupper($prefix).'] ",valeur_application) '.
       'WHERE applicable_a = "nom_groupe" '.
       'AND valeur_application NOT LIKE "['.strtoupper($prefix).'] %";';

$query = mysql_query($sql);

$notification .= 'Tous les groupes ont �t� renomm�s en ['.strtoupper($prefix).'] nom_groupe dans les tables : 
<ul>
<li>'.$prefix.'_groupes</li>
<li>'.$prefix.'_gpe_est_inclus_dans_gpe</li>
<li>'.$prefix.'_ord_appartient_a_gpe</li>
<li>'.$prefix.'_postinstall_scripts</li>
<li>'.$prefix.'_predeinstall_scripts</li>
</ul> <br />';

/************************************************
 *                                              *   
 *    Formulaire de l'�tape 3                   *
 *                                              *
 ************************************************/
$content .= '<h2>Suppression des logiciels cach�s de la base distante</h3>';

$content .= '<form action="step4.php" method="post">';

$content .='<p>Voulez-vous supprimer les logiciels cach�s de la base distante : ';

$content .= '<input type="submit" name="supprLog" value="oui" />';

$content .= '<input type="submit" name="supprLog" value="non" />';

$content .= '</p>';

$content .= '</form>';

require_once '../layout.php';

?>