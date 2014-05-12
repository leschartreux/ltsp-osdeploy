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



include("UtilsHTML.php");
include("UtilsMySQL.php");

entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Dépannage");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<CENTER><H1>Dépannage</H1></CENTER>\n");

print("<CENTER>\n");

$mon_ip=getenv('REMOTE_ADDR');
$request="SELECT * FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\" AND NOW()-timestamp<=500";
$result=mysql_query($request);
$expired=(mysql_num_rows($result)==0);
mysql_free_result($result);
if ($expired) {
   print("La sélection a expiré.<BR>\n");
}
else {
	$nom_dns=$_POST["nom_dns"];
	$action=$_POST["action"];
	$liste="";	
	if (sizeof($nom_dns)>0) {
		for ($i=0;$i<sizeof($nom_dns)-1;$i++) $liste.="\"$nom_dns[$i]\",";
		$liste.="\"$nom_dns[$i]\"";
		$s=sizeof($nom_dns)>1?"s":"";
		switch ($action) {
			case "en_cours" :
				$message="Redémarrez le$s poste$s <b>".str_replace('"','',$liste)."</b> afin que l'installation reprenne son cours.";
			case "modifie" :
				$request="UPDATE ordinateurs SET etat_install=\"$action\" WHERE nom_dns IN ($liste)";
				mysql_query($request);
				$request="DELETE FROM depannage WHERE nom_dns IN ($liste)";
				mysql_query($request);
				if ($action=="modifie") $message="Vous pouvez maintenant modifier le$s poste$s <b>".str_replace('"','',$liste)."</b> depuis l'interface JeDDLaJ";
				break;
			case "a_synchroniser" :
			case "a_ajouter" :
				# Vivement les jointures dans les UPDATE ou les requetes imbriquées
				$request="SELECT nom_dns,num_disque,num_partition FROM depannage WHERE nom_dns IN ($liste)";  
				$result=mysql_query($request);
				for ($i=0;$i<mysql_num_rows($result);$i++) {
					$line=mysql_fetch_array($result);
					$request2="UPDATE idb_est_installe_sur SET etat_idb=\"$action\" WHERE nom_dns=\"$line[nom_dns]\" AND num_disque=\"$line[num_disque]\" AND num_partition=\"$line[num_partition]\"";
					mysql_query($request2);
				}
				mysql_free_result($result);
				$request="UPDATE ordinateurs SET etat_install=\"modifie\" WHERE nom_dns IN ($liste)";
				mysql_query($request);
				$request="DELETE FROM depannage WHERE nom_dns IN ($liste)";
				mysql_query($request);
				$message="Redémarrez le$s poste$s <b>".str_replace('"','',$liste)."</b> afin que la ".($action=="a_ajouter"?"réinstallation":"synchronistation")." commence.";
				break;
			case "vide_cache" :
			case "chkdsk_ntfs" :
			case "depannage" :
			case "script" :
				if ($action=="script") $action=$_POST["nom_script"];
				$request="UPDATE depannage SET nom_script=\"$action\" WHERE nom_dns IN ($liste)";
				mysql_query($request);
				$message="Redémarrez le$s poste$s <b>".str_replace('"','',$liste)."</b> afin que le script <b>$action.rbx</b> y soit exécuté.";
				break;
			}
		print("<P><I>$message</I></P>\n");
	} else print("<P><CENTER>La sélection est vide.</CENTER></P>\n");
	$request = "DELETE FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\"";
	mysql_query($request);
}

DisconnectMySQL();

print("<BR><HR><P><CENTER><A HREF=accueil.php>Retour</A></CENTER></P>\n");

PiedPage();

?>
