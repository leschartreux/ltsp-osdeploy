<?php
# ################################ GPL STUFF ################################
#
# ********************************* ENGLISH *********************************
# 
# --- Copyright notice :
# 
# Copyright 2003, 2004, 2005 Gérard Milhaud - Frédéric Bloise
# 
# 
# --- Statement of copying permission
# 
# This file is part of JeDDLaJ.
# 
# JeDDLaJ is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# JeDDLaJ is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with JeDDLaJ; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# 
# *********** TRADUCTION FRANÇAISE PERSONNELLE SANS VALEUR LÉGALE ***********
#
# --- Notice de Copyright :
# 
# Copyright 2003, 2004, 2005 Gérard Milhaud - Frédéric Bloise
# 
# 
# --- Déclaration de permission de copie
# 
# Ce fichier fait partie de JeDDLaJ.
# 
# JeDDLaJ est un logiciel libre : vous pouvez le redistribuer ou le modifier
# selon les termes de la Licence Publique Générale GNU telle qu'elle est
# publiée par la Free Software Foundation ; soit la version 2 de la Licence,
# soit (à votre choix) une quelconque version ultérieure.
# 
# JeDDLaJ est distribué dans l'espoir qu'il soit utile, mais SANS AUCUNE
# GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou 
# d'ADAPTATION DANS UN BUT PARTICULIER. Voir la Licence publique Générale GNU
# pour plus de détails.
# 
# Vous devriez avoir reçu une copie de la Licence Publique Générale GNU avec 
# JeDDLaJ ; si ça n'était pas le cas, écrivez à la Free Software Foundation,
# Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
# 
# ############################ END OF GPL STUFF #############################

include("../UtilsHTML.php");
include("../UtilsMySQL.php");
include ("../DBParDefaut.php");

entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "../CSS/g.css", "JeDDLaJ : Modification d'un composant dans la base");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<CENTER><H1>Modification d'un composant dans la base</H1></CENTER>\n");

$id_composant="";
if (isset($_GET["id_composant"])) $id_composant=$_GET["id_composant"];

print("<CENTER>\n");
print("<FORM METHOD=GET NAME=\"form\" ACTION=\"modification_composant_frame2.php\" TARGET=\"selection\">\n");
print("<TABLE>\n");
print("<TR><TD>ID composant</TD><TD>:</TD><TD><SELECT name=\"id_composant\" onChange=\"document.form.submit()\">\n");
$request="SELECT id_composant FROM composants ORDER BY id_composant";
if ($nom_dns=="") print("<OPTION VALUE=\"\" SELECTED></OPTION>\n");
$result=mysql_query($request);
for ($i=0;$i<mysql_num_rows($result);$i++) {
	$line=mysql_fetch_array($result);
	print("<OPTION VALUE=\"$line[id_composant]\"");
	if ($id_composant==strtoupper($line["id_composant"])) print(" SELECTED");
	print(">$line[id_composant]</OPTION>\n");
}
print("</SELECT></TD></TR></TABLE>\n");

print("</CENTER>\n");

print("<P><CENTER><A HREF=\"javascript:parent.location.href='accueil.php'\">Retour</A></CENTER></P>\n");

if ($id_composant!="") print("<script> document.form.submit(); </script>\n");

DisconnectMySQL();

PiedPage();

?>
