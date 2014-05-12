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

function statusOrdinateur($nom_dns,$mon_ip,$nom_os,$cache,$boot_options) {
	$td_boot_options="";
  $requestlocal = "SELECT timestamp,ip_distante FROM ordinateurs_en_consultation WHERE nom_dns=\"$nom_dns\""; 
  $resultlocal = mysql_query($requestlocal);
  if (mysql_num_rows($resultlocal) > 0 ) {
 		$linelocal=mysql_fetch_array($resultlocal);
    $ip=$linelocal["ip_distante"];
  	mysql_free_result($resultlocal);
		if (EstUnLinux($nom_os)) $td_boot_options="<TD></TD>";
    print("<TD><IMG SRC=\"ICONES/ordi_lock.png\"></TD><TD></TD><TD></TD>$boot_options<TD>$nom_dns</TD><TD>Cet ordinateur est en consultation depuis $ip </TD>\n");
		return 0;
	}
	$requestlocal="INSERT INTO ordinateurs_en_consultation (nom_dns,ip_distante,timestamp) VALUES(\"$nom_dns\",\"$mon_ip\",NOW())";
	mysql_query($requestlocal);
	if (EstUnLinux($nom_os)) $td_boot_options="<TD>$boot_options</TD>";
  print("<TD><IMG SRC=\"ICONES/ordi_ok.png\"></TD><TD ALIGN=center><INPUT TYPE=\"checkbox\" NAME=\"nom_dns_checked[]\" VALUE=\"$nom_dns\"></TD><TD>$nom_dns</TD><TD TD ALIGN=center>$cache</TD>$td_boot_options</TD><TD> OK </TD>");
	return 1;
}

include("UtilsHTML.php");
include("UtilsMySQL.php");
include("UtilsJeDDLaJ.php");
include ("DBParDefaut.php");
entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Paramètres de boot");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<CENTER>\n");

$mon_ip=getenv('REMOTE_ADDR');
$id_logiciel=$_POST["id_logiciel"];
$nom_groupe=$_POST["nom_groupe"];

if (isset($_POST["type_action"])) {
	if (isset($_POST["nom_dns_checked"])) {
		$nom_dns_checked=$_POST["nom_dns_checked"];
		$request="SELECT * FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\" AND NOW()-timestamp<=500";
		$result=mysql_query($request);
		$expired=(mysql_num_rows($result)==0);
		mysql_free_result($result);
		if ($expired) {
  		print("La sélection a expiré.<BR>\n");
		} else {
			if ($_POST["type_action"]=="oui" || $_POST["type_action"]=="non") {
				for ($i=0;$i<count($nom_dns_checked);$i++) {
					$request="UPDATE idb_est_installe_sur AS a,images_de_base AS b SET cache=\"$_POST[type_action]\" WHERE nom_dns=\"$nom_dns_checked[$i]\" AND id_os=\"$id_logiciel\" AND a.id_idb=b.id_idb";
					mysql_query($request);
				}
			} else {
				for ($i=0;$i<count($nom_dns_checked);$i++) {
					$request="UPDATE idb_est_installe_sur AS a,images_de_base AS b SET boot_options=\"$_POST[boot_options]\" WHERE nom_dns=\"$nom_dns_checked[$i]\" AND id_os=\"$id_logiciel\" AND a.id_idb=b.id_idb";
					mysql_query($request);
				}
			}
			$request = "DELETE FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\""; 
			mysql_query($request);
		}
	}
}
$request = "DELETE FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\" OR NOW()-timestamp>500";
mysql_query($request);
$request="SELECT nom_os FROM logiciels WHERE id_logiciel=\"$id_logiciel\"";
$result = mysql_query($request);
$line=mysql_fetch_array($result);
$nom_os=$line["nom_os"];
mysql_free_result($result);
$td_boot_options="";
if (EstUnLinux($nom_os)) $td_boot_options="<TD ALIGN=\"center\"  BGCOLOR=\"#CC00AA\"><b>Options du noyau</b></TD>";
$request = "SELECT a.nom_dns,cache,boot_options FROM idb_est_installe_sur AS a,images_de_base AS b,ord_appartient_a_gpe AS c WHERE a.nom_dns=c.nom_dns AND c.nom_groupe=\"$nom_groupe\" AND a.id_idb=b.id_idb AND id_os=\"$id_logiciel\" ORDER BY nom_dns";
$result = mysql_query($request);
if (mysql_num_rows($result)>0) {
	$ok=0;
	print("<FORM NAME=\"form\" METHOD=POST ACTION=\"boot_frame2.php\" TARGET=\"selection\">\n");
	print("<TABLE>");
	print("<TR><TD></TD><TD ALIGN=\"center\"  BGCOLOR=\"#CC00AA\"><b>Sélection</b></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Nom DNS</b></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>cachée</b></TD>$td_boot_options<TD ALIGN=\"left\"  BGCOLOR=\"#CC00AA\"><b>Status</b></TD></TR>\n");
	for ($i=0;$i<mysql_num_rows($result);$i++) {
		$line=mysql_fetch_array($result);
	  print("<TR>\n");
		$ok+=statusOrdinateur($line["nom_dns"],$mon_ip,$nom_os,$line["cache"],$line["boot_options"]);
		print("</TR>\n");
	}
	mysql_free_result($result);
	print("</TABLE>\n");
	if (EstUnLinux($nom_os)) {
		print("<TABLE><TR>\n");
		print("<TD>Options du noyau : </TD>");
		print("<TD><INPUT TYPE=\"text\" size=50 NAME=\"boot_options\"></TD>");
		print("<TD><INPUT TYPE=\"button\" VALUE=\"INSERER\" onClick=\"document.form.type_action.value='inserer_options';document.form.submit()\"></TD>");
		print("</TR></TABLE>\n");
	}
	print("<TABLE><TR>\n");
	print("<TD><INPUT TYPE=\"button\" VALUE=\"ACTUALISER\" onClick=\"javascript:location.reload()\"></TD>\n");
	if ($ok>0) {
		if ($ok > 1) {
			print("<TD><INPUT TYPE=button VALUE=\"INVERSER SELECTION\" onClick=\"javascript:for (i=0;typeof(document.form['nom_dns_checked[]'][i])!='undefined';i++) document.form['nom_dns_checked[]'][i].checked=!document.form['nom_dns_checked[]'][i].checked\"></TD>\n");
			print("<TD><INPUT TYPE=button VALUE=\"TOUT SELECTIONNER\" onClick=\"javascript:for (i=0;typeof(document.form['nom_dns_checked[]'][i])!='undefined';i++) document.form['nom_dns_checked[]'][i].checked=true\"></TD>\n");
		}
		print("<TD><INPUT TYPE=\"hidden\" NAME =\"id_logiciel\" VALUE=\"$id_logiciel\">\n");
		print("<TD><INPUT TYPE=\"hidden\" NAME =\"nom_groupe\" VALUE=\"$nom_groupe\">\n");
		print("<INPUT TYPE=\"hidden\" NAME =\"type_action\">\n");
		print("<INPUT TYPE=\"button\" VALUE=\"CACHER\" onClick=\"document.form.type_action.value='oui';document.form.submit()\"></TD>\n");
		print("<TD><INPUT TYPE=\"button\" VALUE=\"NE PAS CACHER\" onClick=\"document.form.type_action.value='non';document.form.submit()\"></TD>\n");
	}
	print("</TR></TABLE>\n");
	print("</FORM>\n");
} else 
		print("Cette distribution n'est installée sur aucun ordinateur du groupe sélectionné.<BR>\n");

print("</CENTER>\n");

DisconnectMySQL();

PiedPage();

?>
