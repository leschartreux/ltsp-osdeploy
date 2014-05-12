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



function statusOrdinateur($nom_dns,$mon_ip) {
  $requestlocal = "SELECT etat_install FROM ordinateurs WHERE nom_dns=\"$nom_dns\" AND etat_install NOT IN (\"installe\",\"modifie\")"; 
  $resultlocal = mysql_query($requestlocal);
  if (mysql_num_rows($resultlocal) > 0 ) {
		$line=mysql_fetch_array($resultlocal);
		print("<TD><IMG SRC=\"../ICONES/ordi_lock.png\"></TD><TD></TD><TD>$nom_dns</TD><TD>Cet ordinateur est en �tat $line[etat_install]</TD>\n");
  	mysql_free_result($resultlocal);
		return false;
	}
  $requestlocal = "SELECT timestamp,ip_distante FROM ordinateurs_en_consultation WHERE nom_dns=\"$nom_dns\""; 
  $resultlocal = mysql_query($requestlocal);
  if (mysql_num_rows($resultlocal) > 0 ) {
 		$linelocal=mysql_fetch_array($resultlocal);
    $ip=$linelocal["ip_distante"];
  	mysql_free_result($resultlocal);
    print("<TD><IMG SRC=\"../ICONES/ordi_lock.png\"></TD><TD></TD><TD>$nom_dns</TD><TD>Cet ordinateur est en consultation depuis $ip </TD>\n");
		return false;
	}
	$requestlocal="INSERT INTO ordinateurs_en_consultation (nom_dns,ip_distante,timestamp) VALUES(\"$nom_dns\",\"$mon_ip\",NOW())";
  mysql_query($requestlocal);
  print("<TD><IMG SRC=\"../ICONES/ordi_ok.png\"></TD><TD ALIGN=CENTER><INPUT TYPE=\"checkbox\" NAME=\"nom_dns_checked[]\" VALUE=\"$nom_dns\"></TD><TD>$nom_dns</TD><TD> OK </TD>");
	return true;
}

include("../UtilsHTML.php");
include("../UtilsMySQL.php");
include ("../DBParDefaut.php");
entete("G�rard Milhaud & Fr�d�ric Bloise : La.Firme@esil.univ-mrs.fr", "../CSS/g.css", "JeDDLaJ : Mise en d�pannage mode administrateur");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<CENTER><H1>Mise en d�pannage mode administrateur</H1></CENTER>\n");

print("<CENTER>\n");

$mon_ip=getenv('REMOTE_ADDR');

if (isset($_POST["depannage"])) {
	if (isset($_POST["nom_dns_checked"])) {
		$nom_dns_checked=$_POST["nom_dns_checked"];
		$request="SELECT * FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\" AND NOW()-timestamp<=500";
		$result=mysql_query($request);
		$expired=(mysql_num_rows($result)==0);
		mysql_free_result($result);
		if ($expired) {
  		print("La s�lection a expir�.<BR>\n");
		} else {
			for ($i=0;$i<count($nom_dns_checked);$i++) {
				$request="UPDATE ordinateurs SET etat_install=\"depannage\" WHERE nom_dns=\"$nom_dns_checked[$i]\"";	
				mysql_query($request);
				$request="INSERT INTO depannage SET nom_dns=\"$nom_dns_checked[$i]\",erreur=\"Mise en �t� d�pannage forc�e\",nom_script=\"depannage\"";
				mysql_query($request);
			}
			$request = "DELETE FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\""; 
			mysql_query($request);
			print ("<P><CENTER><I>Mofications ins�r�es dans la base. <FONT COLOR=RED>ATTENTION :</FONT> Apr�s red�marrage de la machine vous pourrez utiliser l'interface Rembo en mode administrateur...</I></CENTER></P>\n");
		} 
	} else print("La s�lection est vide.\n");
} else {	
	$request = "DELETE FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\" OR NOW()-timestamp>500";
	mysql_query($request);
	$request = "SELECT nom_dns FROM ordinateurs ORDER BY nom_dns";
	$result = mysql_query($request);
	if (mysql_num_rows($result)>0) {
		print("<FORM NAME=\"form\" METHOD=POST ACTION=\"depannage_mode_admin.php\">\n");
		print("<TABLE>");
		print("<TR><TD></TD><TD ALIGN=\"center\"  BGCOLOR=\"#CC00AA\"><b>S�lection</b></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Nom DNS</b></TD><TD ALIGN=\"left\"  BGCOLOR=\"#CC00AA\"><b>Status</b></TD></TR>\n");
		for ($i=0;$i<mysql_num_rows($result);$i++) {
		  $line=mysql_fetch_array($result);
		  print("<TR>\n");
			statusOrdinateur($line["nom_dns"],$mon_ip);
			print("</TR>\n");
		}
		mysql_free_result($result);
		print("</TABLE>\n");
		print("<INPUT TYPE=\"hidden\"  NAME =\"depannage\">\n");
		print("<INPUT TYPE=\"submit\"  VALUE=\"VALIDER\">\n");
		print("</FORM>\n");
	} else print("Aucun ordinateur dans la base.<BR>\n");
}
print("</CENTER>\n");

print("<P><CENTER><A HREF=\"javascript:parent.location.href='accueil.php'\">Retour</A></CENTER></P>\n");

DisconnectMySQL();

PiedPage();

?>
