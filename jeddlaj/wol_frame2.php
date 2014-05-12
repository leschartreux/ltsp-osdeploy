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


function statusOrdinateur($nom_dns,$etat_install,$adresse_ip,$netmask,$adresse_mac,$ip_distante,$mon_ip) {
	if ($ip_distante!="") {
    print("<TD><IMG SRC=\"ICONES/ordi_lock.png\"></TD><TD></TD><TD>$nom_dns</TD><TD><i>$etat_install</i></TD><TD>Cet ordinateur est en consultation depuis $ip_distante</TD>\n");
		return 0;
	}
	if ($adresse_ip=="" || $adresse_mac=="") {
    print("<TD><IMG SRC=\"ICONES/ordi_confless.png\"></TD><TD></TD><TD>$nom_dns</TD><TD><i>$etat_install</i></TD><TD>Adresse MAC ou IP non définie</TD>\n");
		return 0;
	}
	$requestlocal="INSERT INTO ordinateurs_en_consultation (nom_dns,ip_distante,timestamp) VALUES(\"$nom_dns\",\"$mon_ip\",NOW())";
	mysql_query($requestlocal);
  print("<TD><IMG SRC=\"ICONES/ordi_ok.png\"></TD><TD ALIGN=\"center\"><INPUT TYPE=\"checkbox\" NAME=\"nom_dns_checked[]\" VALUE=\"$adresse_ip@$netmask@$adresse_mac\" CHECKED></TD><TD>$nom_dns</TD><TD><i>$etat_install</i></TD><TD> OK </TD>");
	return 1;
}

include("UtilsHTML.php");
include("UtilsMySQL.php");
include("DBParDefaut.php");
include("wol_fonctions.php");
entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Réveil des machines par le réseau");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<CENTER>\n");

$mon_ip=getenv('REMOTE_ADDR');

if (isset($_POST["wol"])) {
	if (isset($_POST["nom_dns_checked"])) {
		$nom_dns_checked=$_POST["nom_dns_checked"];
		$request="SELECT * FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\" AND NOW()-timestamp<=500";
		$result=mysql_query($request);
		$expired=(mysql_num_rows($result)==0);
		mysql_free_result($result);
		if ($expired) {
  		print("La sélection a expiré.<BR>\n");
		} else {
			for ($i=0;$i<count($nom_dns_checked);$i++) {
				$addr=split("@",$nom_dns_checked[$i]);
				wake_on_lan($addr[0],$addr[1],$addr[2]);
			}
			print ("<P><CENTER><I>Un signal de réveil a été envoyé.</CENTER></P>\n");
			$request = "DELETE FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\""; 
			mysql_query($request);
		}
	} else print("La sélection est vide.");
} else {	
	$etat_install=$_POST["etat_install"];
	$nom_groupe=$_POST["nom_groupe"];
	$request = "DELETE FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\" OR NOW()-timestamp>500";
	mysql_query($request);
	$request = "SELECT a.nom_dns,etat_install,adresse_ip,netmask,adresse_mac,ip_distante FROM ordinateurs AS a INNER JOIN ord_appartient_a_gpe AS b LEFT JOIN ordinateurs_en_consultation AS c ON a.nom_dns=c.nom_dns WHERE a.nom_dns=b.nom_dns AND nom_groupe=\"$nom_groupe\" AND etat_install LIKE \"%$etat_install%\" ORDER BY a.nom_dns";
	$result = mysql_query($request);
	if (mysql_num_rows($result)>0) {
		print("<FORM NAME=\"form\" METHOD=POST ACTION=\"wol_frame2.php\" TARGET=\"selection\">\n");
		print("<TABLE>");
		print("<TR><TD></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Sélection</b></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Nom DNS</b></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b><i>État</i></b></TD><TD ALIGN=\"left\" BGCOLOR=\"#CC00AA\"><b>Status</b></TD></TR>\n");
		$ok=0;
		for ($i=0;$i<mysql_num_rows($result);$i++) {
		  $line=mysql_fetch_array($result);
		  print("<TR>\n");
			$ok+=statusOrdinateur($line["nom_dns"],$line["etat_install"],$line["adresse_ip"],$line["netmask"],$line["adresse_mac"],$line["ip_distante"],$mon_ip);
			print("</TR>\n");
		}
		mysql_free_result($result);
		print("</TABLE>\n");
		print("<BR><TABLE><TR>\n");
		if ($ok>1) {
      		print("<TD><INPUT TYPE=button VALUE=\"TOUT SELECTIONNER\" onClick=\"javascript:for (i=0;typeof(document.form['nom_dns_checked[]'][i])!='undefined';i++) document.form['nom_dns_checked[]'][i].checked=true\"></TD>\n");
      		print("<TD><INPUT TYPE=button VALUE=\"INVERSER SELECTION\" onClick=\"javascript:for (i=0;typeof(document.form['nom_dns_checked[]'][i])!='undefined';i++) document.form['nom_dns_checked[]'][i].checked=!document.form['nom_dns_checked[]'][i].checked\"></TD>\n");
    	}
		print("<TD><INPUT TYPE=\"hidden\" NAME =\"wol\"><INPUT TYPE=\"submit\" VALUE=\"ENVOYER SIGNAL\"></TD>\n");
		print("</TR></TABLE>\n");
		print("</FORM>\n");
	} else print("Aucun ordinateur du groupe sélectionné n'est dans l'état choisi.<BR>\n");
}
print("</CENTER>\n");

DisconnectMySQL();

PiedPage();

?>
