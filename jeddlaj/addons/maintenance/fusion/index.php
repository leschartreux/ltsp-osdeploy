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




/*****************************************
 *                                       *
 *            Formulaire                 *
 *                                       *
 *****************************************/

$content .= '<h2>Fusion de deux bases de donn�es</h2>';

$content .= '<p>Avant de proc�der � la fusion des bases de donn�es, assurez-vous que les versions de JeDDLaJ utilis�es soit les m�mes</p>';

$content .= '<form method="post" action="index.php">';

$content .= '<p>';

$content .= 'Voulez-vous : <br />';

$content .= '<input type="radio" id="nouvelleFusion" name="typeFusion" ';

if (isset($_POST['typeFusion'])) {

    if ($_POST['typeFusion'] === 'nouvelle') {

        $content .= 'checked="checked"';
    }
 }

$content .= ' value="nouvelle" />';

$content .= '<label for="nouvelleFusion">Voulez-vous commencer une nouvelle fusion</label>';

$content .= '<input type="radio" id="ancienneFusion" name="typeFusion" ';

if (isset($_POST['typeFusion'])) {

    if ($_POST['typeFusion'] === 'ancienne') {

        $content .= 'checked="checked"';
    }
 }

$content .= 'value="ancienne" />';

$content .= '<label for="ancienneFusion">Voulez-vous reprendre une fusion existante</label>';

$content .= '</p>';

$content .= '<p>';

$content .= '<input type="submit" name="submit" value="Continuer" />';

$content .= '</p>';

$content .= '</form>';


if (isset($_POST['submit'])) {

    if (isset($_POST['typeFusion'])) {

        $content .= '<p>Si la liste d�roulante des dumps est vide, veuillez <a href="../export/index.php" title="Export d\'une base de donn�es distante">exporter votre base de donn�es distantes</a></p>';

        $content .= '<form action="step2.php" method="post">';

        $content .= '<p>';

        $content .= '<label for="filename">Choisissez un dump : </label>';

        $content .= '<select name="filename" id="filename">';

        foreach (glob("../../../DB_DUMPS/*_*-*-*-*-*-*-*.sql") as $file) {

            $content .= '<option value="'.$file.'">'.basename($file).'</option>';
        }

        $content .= '</select>';

        $content .= '</p><p>';

        $content .= '<input type="hidden" name="typeFusion" value="'.htmlspecialchars($_POST['typeFusion'], ENT_QUOTES).'" />';

        $content .= '<input type="submit" name="fusion" value="Fusionner les bases" />';

        $content .= '</p>';

        $content .= '</form>';

    } else {

        $notification .= 'Choisissez un type de fusion<br />';
    }
 }

$content .= '<p><a href="../index.php" title="retour">Retour</a></p>';

require_once '../layout.php';
?>