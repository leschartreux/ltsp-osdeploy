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
entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "../CSS/g.css", "JeDDLaJ : Sortie de l'état en cours vers l'état dépannage");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<CENTER><H1>Sortie de l'état en cours vers l'état dépannage</H1></CENTER>\n");

print("<CENTER>\n");

if (isset($_POST["sortie"])) {
	if (isset($_POST["nom_dns_checked"])) {
		$nom_dns_checked=$_POST["nom_dns_checked"];
		for ($i=0;$i<count($nom_dns_checked);$i++) {
			mysql_query("UPDATE ordinateurs SET etat_install=\"depannage\" WHERE nom_dns=\"$nom_dns_checked[$i]\"");	
			mysql_query("INSERT INTO depannage (nom_dns,num_disque,num_partition,erreur) SELECT nom_dns,num_disque,num_partition,\"sortie état en cours forcée\" FROM idb_est_installe_sur WHERE nom_dns=\"$nom_dns_checked[$i]\" AND etat_idb!=\"installe\" ORDER BY num_disque,num_partition ASC LIMIT 1") ;
		}
	}
}
$request = "SELECT nom_dns FROM ordinateurs WHERE etat_install=\"en_cours\" ORDER BY nom_dns";
$result = mysql_query($request);
if (mysql_num_rows($result)>0) {
	print("<FORM NAME=\"form\" METHOD=POST ACTION=\"sortie_en_cours.php\">\n");
	print("<TABLE>\n");
	print("<TR><TD ALIGN=\"center\"  BGCOLOR=\"#CC00AA\"><b>Sélection</b></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Nom DNS</b></TD></TR>\n");
		for ($i=0;$i<mysql_num_rows($result);$i++) {
		  $line=mysql_fetch_array($result);
		  print("<TR><TD ALIGN=\"center\"><INPUT TYPE=\"checkbox\" NAME=\"nom_dns_checked[]\" VALUE=\"$line[nom_dns]\"></TD><TD>$line[nom_dns]</TD></TR>\n");
		}
	mysql_free_result($result);
	print("</TABLE>\n");
	print("<BR>\n");
	print("<INPUT TYPE=\"hidden\"  NAME =\"sortie\">\n");
	print("<INPUT TYPE=\"submit\"  VALUE=\"VALIDER\">\n");
	print("</FORM>\n");
	} else print("Aucun ordinateur en cours d'installation dans la base.<BR>\n");

print("</CENTER>\n");

print("<P><CENTER><A HREF=\"javascript:parent.location.href='accueil.php'\">Retour</A></CENTER></P>\n");

DisconnectMySQL();

PiedPage();

?>
