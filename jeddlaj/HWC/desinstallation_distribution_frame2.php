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
		return 0;
	}
  $requestlocal = "SELECT timestamp,ip_distante FROM ordinateurs_en_consultation WHERE nom_dns=\"$nom_dns\""; 
  $resultlocal = mysql_query($requestlocal);
  if (mysql_num_rows($resultlocal) > 0 ) {
 		$linelocal=mysql_fetch_array($resultlocal);
    $ip=$linelocal["ip_distante"];
  	mysql_free_result($resultlocal);
    print("<TD><IMG SRC=\"../ICONES/ordi_lock.png\"></TD><TD></TD><TD>$nom_dns</TD><TD>Cet ordinateur est en consultation depuis $ip </TD>\n");
		return 0;
	}
	$requestlocal="INSERT INTO ordinateurs_en_consultation (nom_dns,ip_distante,timestamp) VALUES(\"$nom_dns\",\"$mon_ip\",NOW())";
	mysql_query($requestlocal);
  print("<TD><IMG SRC=\"../ICONES/ordi_ok.png\"></TD><TD ALIGN=CENTER><INPUT TYPE=\"checkbox\" NAME=\"nom_dns_checked[]\" VALUE=\"$nom_dns\"></TD><TD>$nom_dns</TD><TD> OK </TD>");
	return 1;
}

include("../UtilsHTML.php");
include("../UtilsMySQL.php");
include ("../DBParDefaut.php");
entete("G�rard Milhaud & Fr�d�ric Bloise : La.Firme@esil.univ-mrs.fr", "../CSS/g.css", "JeDDLaJ : Reinstallation");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<CENTER>\n");

$mon_ip=getenv('REMOTE_ADDR');
$id_logiciel=$_POST["id_logiciel"];

if (isset($_POST["desinstall"])) {
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
				$request="DELETE package_est_installe_sur FROM package_est_installe_sur,idb_est_installe_sur AS a, images_de_base AS b WHERE package_est_installe_sur.nom_dns=\"$nom_dns_checked[$i]\" AND a.nom_dns=package_est_installe_sur.nom_dns AND a.id_idb=b.id_idb AND b.id_os=\"$id_logiciel\" AND package_est_installe_sur.num_disque=a.num_disque AND package_est_installe_sur.num_partition=a.num_partition";	
				mysql_query($request);
				$request="DELETE idb_est_installe_sur FROM idb_est_installe_sur, images_de_base AS b WHERE nom_dns=\"$nom_dns_checked[$i]\" AND idb_est_installe_sur.id_idb=b.id_idb AND b.id_os=\"$id_logiciel\"";	
				mysql_query($request);
				# Y a t il une modification sur l'ordinateur ?
				$request="SELECT COUNT(*) AS total FROM idb_est_installe_sur WHERE etat_idb!=\"installe\" AND nom_dns=\"$nom_dns_checked[$i]\"";
				$result=mysql_query($request);
  	  	$line=mysql_fetch_array($result);
				# Pas de modification
  	  	if ($line["total"]==0) {
  				$request2="UPDATE ordinateurs SET etat_install=\"installe\" WHERE nom_dns=\"$nom_dns_checked[$i]\" AND etat_install!=\"installe\"";
  				mysql_query($request2);
				}
				# Modification : ordinateur en etat modifie
				else {
				$request2="UPDATE ordinateurs SET etat_install=\"modifie\" WHERE nom_dns=\"$nom_dns_checked[$i]\" AND etat_install!=\"modifie\"";
				mysql_query($request2);
				}
  	  	mysql_free_result($result);
			}
			$request = "DELETE FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\""; 
			mysql_query($request);
			print ("<P><CENTER><I>Mofications ins�r�es dans la base. <FONT COLOR=RED>ATTENTION :</FONT> La distribution a �t� supprim�e sur les ordinateurs concern�s au niveau de la base. Elle ne sera r�ellement supprim�e sur les ordinateurs concern�s qu'apr�s installation d'une nouvelle distribution...</I></CENTER></P>\n");
		}
	} else print("La s�lection est vide.\n");
} else {	
	$nom_groupe=$_POST["nom_groupe"];
	$request = "DELETE FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\" OR NOW()-timestamp>500";
	mysql_query($request);
	$request = "SELECT a.nom_dns FROM idb_est_installe_sur AS a,images_de_base AS b, ord_appartient_a_gpe AS c WHERE a.nom_dns=c.nom_dns AND c.nom_groupe=\"$nom_groupe\" AND a.id_idb=b.id_idb AND id_os=\"$id_logiciel\" ORDER BY nom_dns";
	$result = mysql_query($request);
	if (mysql_num_rows($result)>0) {
		$ok=0;
		print("<FORM NAME=\"form\" METHOD=POST ACTION=\"desinstallation_distribution_frame2.php\" TARGET=\"selection\">\n");
		print("<TABLE>");
		print("<TR><TD></TD><TD ALIGN=\"center\"  BGCOLOR=\"#CC00AA\"><b>S�lection</b></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Nom DNS</b></TD><TD ALIGN=\"left\"  BGCOLOR=\"#CC00AA\"><b>Status</b></TD></TR>\n");
		for ($i=0;$i<mysql_num_rows($result);$i++) {
		  $line=mysql_fetch_array($result);
		  print("<TR>\n");
			$ok+=statusOrdinateur($line["nom_dns"],$mon_ip);
			print("</TR>\n");
		}
		mysql_free_result($result);
		print("</TABLE>\n");
		print("<TABLE><TR>\n");
  	print("<TD><INPUT TYPE=\"button\" VALUE=\"ACTUALISER\" onClick=\"javascript:location.reload()\"></TD>\n");
		if ($ok>0) {
			if ($ok > 1) {
				print("<TD><INPUT TYPE=button VALUE=\"INVERSER SELECTION\" onClick=\"javascript:for (i=0;typeof(document.form['nom_dns_checked[]'][i])!='undefined';i++) document.form['nom_dns_checked[]'][i].checked=!document.form['nom_dns_checked[]'][i].checked\"></TD>\n");
				print("<TD><INPUT TYPE=button VALUE=\"TOUT SELECTIONNER\" onClick=\"javascript:for (i=0;typeof(document.form['nom_dns_checked[]'][i])!='undefined';i++) document.form['nom_dns_checked[]'][i].checked=true\"></TD>\n");
			}
			print("<TD><INPUT TYPE=\"hidden\"  NAME =\"id_logiciel\" VALUE=\"$id_logiciel\">\n");
			print("<INPUT TYPE=\"hidden\"  NAME =\"desinstall\">\n");
			print("<INPUT TYPE=\"submit\"  VALUE=\"VALIDER\"></TD>\n");
			print("</TR></TABLE>\n");
			print("</FORM>\n");
		}
	} else print("Cette distribution n'est install�e sur aucun ordinateur du groupe s�lectionn�.<BR>\n");
}
print("</CENTER>\n");

DisconnectMySQL();

PiedPage();

?>
