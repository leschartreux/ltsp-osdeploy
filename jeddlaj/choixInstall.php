<?php
/**
 * ************************** GPL STUFF **********************************
 *
 * ********************************* ENGLISH *********************************
 * 
 * --- Copyright notice :
 * 
 * Copyright 2003, 2004, 2005 Gérard Milhaud - Frédéric Bloise
 * 
 * 
 * --- Statement of copying permission
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
 * --- Notice de Copyright :
 * 
 * Copyright 2003, 2004, 2005 Gérard Milhaud - Frédéric Bloise
 * Copyright 2010, 2011  Frédéric Bloise - Gérard Milhaud - Arnaud Salvucci
 * 
 * 
 * --- Déclaration de permission de copie
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
 * ******************* END OF GPL STUFF ***************************************
 */
include("UtilsHTML.php");
include("UtilsJeDDLaJ.php");
entete("Frédéric Bloise & Gérard Milhaud & Arnaud Salvucci : dosicalu@univmed.fr", "CSS/g.css", "JeDDLaJ : setup");

echo '<h1>JeDDLaJ Setup</h1>';

echo '<div>';

echo '<fieldset>';
echo '<legend>Choisir un type d\'installation</legend>';

echo '<form action="build.php" method="post">';

//echo '<p>';
//echo '<label for="update"><input type="radio" name="typeInstall" id="update" value="update" />Mise à jour</label>';
//echo '</p>';

//echo '<p>';
//echo '<label for="version">Version actuelle : </label>';
//echo '<select name="version" id="version">';

//echo '<option value=""> -- </option>';

//foreach ($tab_maj as $version => $script) {

//    echo '<option value="'.$version.'">'.$version.'</option>';
            
//}

echo '</select>';
echo '<p>';

echo '<p>';
echo '<label for="new"><input type="radio" name="typeInstall" id="new" value="new" />Nouvelle installation</label>';
echo '</p>';

echo '<p>';
echo '<input type="submit" name="choixInstallation" value="Choisir un type d\'installation" />';
echo '</p>';

echo '</form>';
echo '</fieldset>';

echo '</div>';

PiedPage();
?>
