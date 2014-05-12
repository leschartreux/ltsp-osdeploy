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

entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Examine Machine");

print("<CENTER><H1>Examine Machine</H1></CENTER>\n");



ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

$nom_dns=$_GET["nom_dns"];

print("<CENTER>\n");
print("<FORM NAME=\"form\">\n");
print("<TABLE>\n");
print("<TD>Nom DNS : </TD>\n");
print("<TD><SELECT name=\"nom_dns\" onChange=\"parent.location.href='examine_machine.php?nom_dns='+document.form.nom_dns.options[document.form.nom_dns.selectedIndex].value\">\n");
$request = "SELECT nom_dns FROM ordinateurs";
if ($nom_dns=="") print("<OPTION SELECTED></OPTION>\n");
$result = mysql_query($request);
for ($i=0;$i<mysql_num_rows($result);$i++) {
  $line = mysql_fetch_array($result);
  print("<OPTION value=\"".$line["nom_dns"]."\"");
	if ($line["nom_dns"]==$nom_dns) print("SELECTED");
	print(">".$line["nom_dns"]."</OPTION>\n");
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
