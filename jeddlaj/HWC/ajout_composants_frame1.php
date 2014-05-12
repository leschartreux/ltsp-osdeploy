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

entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "../CSS/g.css", "JeDDLaJ : Ajout de composants");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<CENTER><H1>Ajout de composants</H1></CENTER>\n");

print("<CENTER>\n");
print("<FORM METHOD=POST NAME=\"form\" ACTION=\"ajout_composants_frame2.php\" TARGET=\"selection\">\n");
print("<TABLE>\n");
print("<TR>\n<TD>Nom DNS : </TD>\n");
print("<TD><SELECT name=\"nom_dns\" onChange=\"javascript:document.form.id_composant.value=-1;document.form.submit();\">\n");
$request = "SELECT nom_dns FROM ordinateurs";
$result=mysql_query($request);
print("<OPTION SELECTED></OPTION>\n");
for ($i=0;$i<mysql_num_rows($result);$i++) {
	$line=mysql_fetch_array($result);	
	print("<OPTION value=$line[nom_dns]>$line[nom_dns]</OPTION>\n");
}
print("</SELECT>\n");
mysql_free_result($result);
print("</TR></TABLE><BR>\n");
$request = "SELECT * FROM composants ORDER BY type,nom";
$result=mysql_query($request);
$type="vide";
$entry=0;
print("<TABLE>\n");
for ($i=0;$i<mysql_num_rows($result);$i++) {
	$line=mysql_fetch_array($result);	
	if ($line["type"]!=$type) {
		if ($type!="vide") print("</TD><TD align=right><INPUT TYPE=button VALUE=\"+\" onClick=\"document.form.id_composant.value=document.form.entry_${entry}[document.form.entry_$entry.selectedIndex].value;document.form.submit()\"></SELECT></TD></TR>");
		$entry++;
		print("<TR><TD>$line[type]</TD><TD>:</TD><TD><SELECT NAME=entry_$entry>\n");
		$type=$line["type"];
	}
	print("<OPTION value=\"$line[id_composant]\">$line[id_composant] $line[nom]</OPTION>\n");
}
mysql_free_result($result);
print("</TD><TD align=right><INPUT TYPE=button VALUE=\"+\" onClick=\"document.form.id_composant.value=document.form.entry_${entry}[document.form.entry_$entry.selectedIndex].value;document.form.submit()\"></SELECT></TD></TR></TABLE>\n");
print("<INPUT TYPE=hidden NAME=id_composant VALUE=-1>\n");
print("</FORM></CENTER>\n");

print("<P><CENTER><A HREF=\"javascript:parent.location.href='accueil.php'\">Retour</A></CENTER></P>\n");

DisconnectMySQL();

PiedPage();

?>
