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


$id_logiciel = $_GET["id_logiciel"];

include("UtilsHTML.php");
include("UtilsMySQL.php");

function afficheLigne($nom_dns,$etat_package) {
	print("<TR><TD>".$nom_dns." </TD>");
	switch ($etat_package) {
		case "installe" :
			$etat_actuel="coche_verte";
			$etat_voulu="coche_verte";
			break;
		case "a_ajouter" :
			$etat_actuel="coche_rouge";
			$etat_voulu="coche_verte";
			break;
		case "a_supprimer" :
			$etat_actuel="coche_verte";
			$etat_voulu="coche_rouge";
			break;
		case "NULL" :
			$etat_actuel="coche_rouge";
			$etat_voulu="coche_rouge";
			break;
	}
	print("<TD ALIGN=\"center\"><IMG SRC=\"ICONES/$etat_actuel.jpg\" WIDHT=\"20\" HEIGHT=\"20\"></TD>");
	print("<TD ALIGN=\"center\"><IMG SRC=\"ICONES/$etat_voulu.jpg\" WIDHT=\"20\" HEIGHT=\"20\"></TD>");
	print("</TR>\n");
}

entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Logiciel sur ordinateurs");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<CENTER><H1>Logiciel sur Ordinateurs</H1></CENTER>\n");

$mon_ip=getenv('REMOTE_ADDR');

print("<CENTER>\n");

$request="SELECT nom_dns FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\" AND NOW()-timestamp<=500 ORDER BY nom_dns";
$result=mysql_query($request);
if ( mysql_num_rows($result) ==  0) {
	print("La sélection a expiré.<BR>\n");
} else {
	$request2 = "SELECT nom_logiciel,version,icone FROM logiciels WHERE id_logiciel=\"$id_logiciel\"";
	$result2=mysql_query($request2);
	$line=mysql_fetch_array($result2);
	print("<IMG SRC=\"ICONES/".$line["icone"]."\" ALIGN=\"CENTER\"> ".$line["nom_logiciel"]." ".$line["version"]." <BR><BR>\n");
	mysql_free_result($result2);

	print("<TABLE><TR><TD align=\"center\" BGCOLOR=\"#CC00AA\"><b>Nom DNS</b></TD><TD align=\"center\" BGCOLOR=\"#CC00AA\"><b>Etat actuel</b></TD><TD align=\"center\" BGCOLOR=\"#CC00AA\"><b>Etat voulu</b></TD></TR>");
	
	$request2 = "SELECT a.nom_dns,etat_package FROM package_est_installe_sur AS a, packages AS b, logiciels AS c, ordinateurs_en_consultation AS d WHERE c.id_logiciel=\"$id_logiciel\"AND c.id_logiciel=b.id_logiciel AND b.id_package=a.id_package AND  ip_distante=\"$mon_ip\" AND a.nom_dns=d.nom_dns AND a.num_disque=d.num_disque AND a.num_partition=d.num_partition ORDER BY a.nom_dns";
	$result2=mysql_query($request2);
	for ($i=0,$j=0;$i<mysql_num_rows($result2);$i++,$j++){
		$line=mysql_fetch_array($result);
		$line2=mysql_fetch_array($result2);
		for (;$j<mysql_num_rows($result) && $line["nom_dns"]!=$line2["nom_dns"];$j++) {
			afficheLigne($line["nom_dns"],"NULL"); 
			$line=mysql_fetch_array($result);
		}
		afficheLigne($line2["nom_dns"],$line2["etat_package"]);
	}
	for (;$j<mysql_num_rows($result);$j++) {
		$line=mysql_fetch_array($result);
	  afficheLigne($line["nom_dns"],"NULL");
	}
	print("</TABLE>\n");
	mysql_free_result($result2);
}
mysql_free_result($result);

print("</CENTER>\n");
print("<p><CENTER><FORM><INPUT TYPE=\"button\" VALUE=\"FERMER\" onClick=\"javascript:window.close()\"></FORM></CENTER>\n");

DisconnectMySQL();

PiedPage();

?>

