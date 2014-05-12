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
include("UtilsJeDDLaJ.php");

entete("G�rard Milhaud & Fr�d�ric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Addons");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

foreach ( $_POST as $post => $val )  {           
	$$post = $val;
	unset($nom);
	unset($version);
	unset($date_version);
	unset($start_page);
	include("addons/$post/include.php");
	switch ($_POST["$post"]) {
		case "install" :
			mysql_query("INSERT INTO addons(nom,version,date_version,start_page) VALUES (\"$nom\",\"$version\",\"$date_version\",\"$post/$start_page\")");
			if (file_exists("addons/$post/dump.sql")) importe_dump("addons/$post/dump.sql");
			break;
		case "uninstall" :
			mysql_query("DELETE FROM addons WHERE nom=\"$nom\"");
			mysql_query("DELETE FROM configuration WHERE application=\"$nom\"");
			break;
		case "active" :
			mysql_query("UPDATE addons SET actif=\"oui\" WHERE nom=\"$nom\"");
			break;
		case "desactive" :
			mysql_query("UPDATE addons SET actif=\"non\" WHERE nom=\"$nom\"");
			break;
		case "maj" :
			if (isset($tab_maj)) {
				$request="SELECT version FROM addons WHERE nom=\"$nom\"";
        $result=mysql_query($request);
        $line=mysql_fetch_array($result);
				$cumulatif=false;
				foreach($tab_maj as $ancienne_version => $maj_sql) 
					if ($line["version"]==$ancienne_version || $cumulatif) {
						$cumulatif=true;
						importe_dump("addons/$post/$maj_sql");
					}
			}
			mysql_free_result($result);
			mysql_query("UPDATE addons SET version=\"$version\",date_version=\"$date_version\",start_page=\"$post/$start_page\" WHERE nom=\"$nom\"");
			break;
	}
}

print("<CENTER><H1>Addons</H1></CENTER>\n");

print("<FORM METHOD=POST NAME=\"form\">\n");

$nb_addons=0;
print("<CENTER><TABLE BORDER=1>\n");
print("<TR><TD>Nom</TD><TD>Version</TD><TD>Date version</TD><TD>Version install�e</TD><TD>Date version install�e</TD><TD>Actif</TD><TD>Activ�</TD>\n");
if ($handle = opendir('addons')) {
	while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != ".." && file_exists("addons/$file/include.php")) {
			unset($nom);
			unset($version);
			unset($date_version);
			unset($start_page);
			include("addons/$file/include.php");
			if (isset($nom) && isset($version) && isset($date_version) && isset($start_page)) {
				$nb_addons++;
				print("<TR><TD>$nom</TD><TD>$version</TD><TD>$date_version</TD>\n");
				$request="SELECT * FROM addons WHERE nom=\"$nom\"";
				$result=mysql_query($request);
				if (mysql_num_rows($result)>0) {
					$line=mysql_fetch_array($result);
					print("<TD>$line[version]</TD><TD>$line[date_version]</TD><TD>$line[actif]</TD>");
				} else print ("<TD></TD><TD></TD><TD></TD>");
				print("<TD><SELECT NAME=\"$file\">\n");
				print("<OPTION VALUE=\"\" SELECTED></OPTION>\n");
				if (mysql_num_rows($result)>0) {
					if ($date_version>$line["date_version"]) 
						print("<OPTION VALUE='maj'>METTRE � JOUR</OPTION>\n");
					else {
						print("<OPTION VALUE='uninstall'>DESINSTALLER</OPTION>\n");
						if ($line["actif"]!="oui") print ("<OPTION VALUE='active'>ACTIVER</OPTION>\n");
						else print ("<OPTION VALUE='desactive'>D�SACTIVER</OPTION>\n");	
					}
				} else {
					print("<OPTION VALUE='install'>INSTALLER</OPTION>\n");
				}
				mysql_free_result($result);
			} 
			print("</TD></SELECT></TR>\n");
    }
	}
	closedir($handle);
}
print("</TABLE>\n");
print("<BR>\n");
if ($nb_addons>0) {
	print("<INPUT TYPE=submit VALUE=\"VALIDER\">\n");
} else ("aucun addon d�tect�.\n");
print("</FORM>\n");
print("</CENTER>\n");

DisconnectMySQL();

print("<BR><HR><P><CENTER><A HREF=accueil.php>Retour</A></CENTER></P>\n");

PiedPage();

?>
