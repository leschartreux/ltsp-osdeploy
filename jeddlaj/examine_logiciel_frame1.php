<!--
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
-->

<?php
include("UtilsHTML.php");
include("UtilsMySQL.php");
include ("DBParDefaut.consult.php");

entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Examine Logiciel");

print("<CENTER><H1>Examine Logiciel</H1></CENTER>\n");

print("<CENTER>\n");

$id_logiciel=$_GET["id_logiciel"];

ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<FORM NAME=\"form\">\n");
print("<TABLE>\n");
print("<TD>Nom logiciel : </TD>\n");
print("<TD><SELECT name=\"id_logiciel\" onChange=\"parent.location.href='examine_logiciel.php?id_logiciel='+document.form.id_logiciel.options[document.form.id_logiciel.selectedIndex].value;\">\n");
if ($id_logiciel=="") print("<OPTION SELECTED></OPTION>\n");
$request = "SELECT nom_logiciel,version,nom_os,a.id_logiciel,visible FROM logiciels AS a, packages AS b WHERE a.id_logiciel=b.id_logiciel GROUP BY a.id_logiciel ORDER BY nom_os,nom_logiciel,version";
$result = mysql_query($request);
$nom_os="";
for ($i=0;$i<mysql_num_rows($result);$i++) {
  $line = mysql_fetch_array($result);
	if ($nom_os!=$line["nom_os"]) {
		if ($i>0) print("</OPTGROUP>\n");
		$nom_os=$line["nom_os"];
		print("<OPTGROUP LABEL='$nom_os'>\n");
	}
  print("<OPTION style=\"color:".($line["visible"]=="oui"?"black":"red").";\" value=\"".$line["id_logiciel"]."\"");
	if ($line["id_logiciel"]==$id_logiciel) print("SELECTED");
	print("> $line[nom_logiciel] $line[version]</OPTION>\n");
}
print("</SELECT></TD>");
mysql_free_result($result);
print("</TR>\n</TABLE>\n");
print("</FORM>");

print("</CENTER>\n");

print("<P><CENTER><A HREF=\"javascript:parent.location.href='accueil.php'\">Retour</A></CENTER></P>\n");

DisconnectMySQL();

PiedPage();
?>		
