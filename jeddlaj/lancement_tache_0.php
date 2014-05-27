<?php
# ################################ GPL STUFF ################################
#
# ********************************* ENGLISH *********************************
# 
# --- Copyright notice :
# 
# Copyright 2003, 2004, 2005 G�rard Milhaud - Fr�d�ric Bloise
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
# *********** TRADUCTION FRAN�AISE PERSONNELLE SANS VALEUR L�GALE ***********
#
# --- Notice de Copyright :
# 
# Copyright 2003, 2004, 2005 G�rard Milhaud - Fr�d�ric Bloise
# 
# 
# --- D�claration de permission de copie
# 
# Ce fichier fait partie de JeDDLaJ.
# 
# JeDDLaJ est un logiciel libre : vous pouvez le redistribuer ou le modifier
# selon les termes de la Licence Publique G�n�rale GNU telle qu'elle est
# publi�e par la Free Software Foundation ; soit la version 2 de la Licence,
# soit (� votre choix) une quelconque version ult�rieure.
# 
# JeDDLaJ est distribu� dans l'espoir qu'il soit utile, mais SANS AUCUNE
# GARANTIE ; sans m�me la garantie implicite de COMMERCIALISATION ou 
# d'ADAPTATION DANS UN BUT PARTICULIER. Voir la Licence publique G�n�rale GNU
# pour plus de d�tails.
# 
# Vous devriez avoir re�u une copie de la Licence Publique G�n�rale GNU avec 
# JeDDLaJ ; si �a n'�tait pas le cas, �crivez � la Free Software Foundation,
# Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
# 
# ############################ END OF GPL STUFF #############################



include("UtilsHTML.php");
include("UtilsMySQL.php");

entete("Rapha�l RIGNIER - Les Chartreux : inforeseau@leschartreux.net", "CSS/g.css", "JeDDLaJ : Lancement de t�che - Etape 0");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<CENTER><H1>Lancement d'une t�che - Etape 0</H1></CEnTER>\n");

print("<CENTER>\n");
print("<TABLE>\n");
print("<FORM METHOD=GET NAME=\"form\" ACTION=\"lancement_tache_1.php\">\n");

print("<TR>\n<TD>Groupe : </TD>\n");
print("<TD><SELECT name=\"nom_groupe\" onChange=\"javascript:document.form.nom_dns.selectedIndex=0\">\n");
print("<OPTION value=\"\"></OPTION>\n");
$request = "SELECT nom_groupe FROM groupes";
$result = mysql_query($request);
for ($i=0;$i<mysql_num_rows($result);$i++) {
  $line = mysql_fetch_array($result);
  print("<OPTION value=\"".$line["nom_groupe"]."\">".$line["nom_groupe"]."</OPTION>\n");
}
mysql_free_result($result);
print("</SELECT></TD>\n");

print("<TD><FONT COLOR=\"#FF000\">OU</FONT></TD>\n");

print("<TD>Ordinateur : </TD>\n");
print("<TD><SELECT name=\"nom_dns\" onChange=\"javascript:document.form.nom_groupe.selectedIndex=0\">\n");
print("<OPTION value=\"\"></OPTION>\n");
$request = "SELECT nom_dns FROM ordinateurs";
$result = mysql_query($request);
for ($i=0;$i<mysql_num_rows($result);$i++) {
  $line = mysql_fetch_array($result);
  print("<OPTION value=\"".$line["nom_dns"]."\">".$line["nom_dns"]."</OPTION>\n");
}
print("</TR>\n</TABLE>\n");
mysql_free_result($result);
print("<BR>S�lectionnez une t�che : <SELECT name=\"id_typetache\">\n");
# 21-06-2006 : On ne montre maintenant que les distributions visibles 
$request = "SELECT * FROM type_tache";
$result = mysql_query($request);
$os="";
for ($i=0;$i<mysql_num_rows($result);$i++) {
  $line = mysql_fetch_array($result);
  print("<OPTION value=\"".$line["idtype_tache"]."\">".$line["desc"]."</OPTION>\n");
}
print("</SELECT>");
print("<BR><BR><INPUT TYPE=\"submit\" VALUE=\"VALIDER\">\n");
print("</FORM></CENTER>\n");


DisconnectMySQL();

print("<BR><HR><P><CENTER><A HREF=accueil.php>Retour</A></CENTER></P>\n");

PiedPage();

?>
