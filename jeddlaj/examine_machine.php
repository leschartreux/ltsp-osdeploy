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



include("UtilsMySQL.php");
include("UtilsJeDDLaJ.php");
include ("DBParDefaut.consult.php");


print("<HTML>\n");
print("<HEAD>\n");
print("<TITLE>JeDDLaJ : Examine Machine</TITLE>\n");
print("<META http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-15\">\n");
print("<!--[if IE]><LINK REL=\"shortcut icon\" TYPE=\"image/x-icon\" HREF=\"ICONES/favicon.ico\" /><![endif]-->\n");
print("<LINK REL=\"icon\" TYPE=\"image/png\" HREF=\"ICONES/favicon.png\">\n");
print("\n");

function insereLigne($level,$name,$isleaf,$opened) {
	print("  { level:$level, opened:$opened, name:\"$name\", isleaf:$isleaf },\n");
}

if (isset($_GET["nom_dns"])) {
	$nom_dns = $_GET["nom_dns"];
	ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
	SelectDb($GLOBALS['db']);
	print("<SCRIPT LANGUAGE=\"javascript\">\n");
	print("\n");
	print("var fields = [\n");
	$request = "SELECT * FROM ordinateurs WHERE nom_dns=\"$nom_dns\"";
	$result = mysql_query($request);
	$line = mysql_fetch_array($result);
	insereLigne(0,$nom_dns." <I>( $line[etat_install] )</I>","false","true");
	insereLigne(1,"Configuration réseau","false","false");
	insereLigne(2,"Adrese IP : ".$line["adresse_ip"],"true","false");
	insereLigne(2,"Masque de sous-réseau : ".$line["netmask"],"true","false");
	insereLigne(2,"Passerelle : ".$line["gateway"],"true","false");
	insereLigne(2,"Adresse MAC : ".$line["adresse_mac"],"true","false");
	insereLigne(2,"Nom NetBios : ".$line["nom_netbios"],"true","false");
	insereLigne(2,"SID : ".$line["sid"],"true","false");
	insereLigne(2,"Affiliation Windows : ".$line["affiliation_windows"],"true","false");
	insereLigne(2,"Nom Affiliation: ".$line["nom_affiliation"],"true","false");
	#insereLigne(2,"OU : ".$line["ou"],"true","false");
	insereLigne(1,"Paramètres d'affichage","false","false");
	insereLigne(2,"Résolution horizontale : ".($line["hres"]==0?"<i>non définie</i>":$line["hres"]." px"),"true","false");
	insereLigne(2,"Résolution verticale : ".($line["vres"]==0?"<i>non définie</i>":$line["vres"]." px"),"true","false");
	insereLigne(2,"Fréquence verticale : ".($line["vfreq"]==0?"<i>non définie</i>":$line["vfreq"]." Hz"),"true","false");
	insereLigne(2,"Fréquence horizontale : ".($line["hfreq"]==0?"<i>non définie</i>":$line["hfreq"]." kHz"),"true","false");
	insereLigne(2,"Nombre de couleurs : ".($line["bpp"]==0?"<i>non défini</i>":$line["bpp"]." bpp"),"true","false");
	insereLigne(2,"Modeline : ".($line["modeline"]==""?"<i>non défini</i>":$line["modeline"]),"true","false");
	insereLigne(1,"Caractéristiques constructeur","false","false");
	insereLigne(2,"Marque : ".$line["marque"],"true","false");
	insereLigne(2,"Modèle : ".$line["modele"],"true","false");
	insereLigne(2,"Numéro de Série : ".$line["numero_serie"],"true","false");
	insereLigne(1,"Processeur","false","false");
	insereLigne(2,"Nombre : ".$line["nombre_proc"],"true","false");
	insereLigne(2,"Type : ".$line["processeur"],"true","false");
	insereLigne(2,"Fréquence : ".$line["frequence"]." MHz","true","false");
	insereLigne(2,"Socket : ".$line["socket"],"true","false");
	insereLigne(1,"Mémoire","false","false");
	insereLigne(2,"RAM : ".$line["ram"]." Mo","true","false");
	insereLigne(2,"Type : ".$line["type_ram"],"true","false");
	insereLigne(2,"Nombre de slots : ".$line["nombre_slots"],"true","false");
	insereLigne(2,"Nombre de slots libres : ".$line["slots_libres"],"true","false");
	$request1 = "SELECT type,nom,a.id_composant FROM composant_est_installe_sur AS a, composants AS b WHERE a.id_composant=b.id_composant AND nom_dns=\"$nom_dns\" ORDER BY type";
	$result1 = mysql_query($request1);
	insereLigne(1,"Composants (".mysql_num_rows($result1).")","false","false");
	insereLigne(2,"Signature : ".$line["signature"],"true","false");
	for ($i1=0;$i1<mysql_num_rows($result1);$i1++) {
	  $line1 = mysql_fetch_array($result1);
		insereLigne(2,$line1["type"]." : ".$line1["nom"]." <A HREF='javascript:parent.location.href=\\\"HWC/modification_composant.php?id_composant=".$line1["id_composant"]."\\\"'>".$line1["id_composant"]."</A>","true","false");
	}
	mysql_free_result($result1);
	insereLigne(1,"Stockages de masse","false","false");
	insereLigne(2,"Disques durs","false","false");
	$request1 = "SELECT connectique,capacite,num_disque,dd_a_partitionner FROM stockages_de_masse WHERE nom_dns=\"$nom_dns\" AND type=\"disque dur\" ORDER BY num_disque";
	$result1 = mysql_query($request1);
	for ($i1=0;$i1<mysql_num_rows($result1);$i1++) {
	  $line1 = mysql_fetch_array($result1);
		insereLigne(3,"Disque ".$line1["num_disque"].($line1["dd_a_partitionner"]=="oui"?" <I>( a_partitionner )</I>":""),"false","false");
		insereLigne(4,"Connectique : ".$line1["connectique"],"true","false");
		insereLigne(4,"Capacité : ".$line1["capacite"]." Go","true","false");
		insereLigne(4,"Partitions","false","false");
		$request2 = "SELECT num_partition,nom_partition,taille_partition,type_partition,systeme FROM partitions WHERE nom_dns=\"$nom_dns\" AND num_disque=\"".$line1["num_disque"]."\" ORDER BY num_partition";
		$result2 = mysql_query($request2);
		for ($i2=0;$i2<mysql_num_rows($result2);$i2++) {
		  $line2 = mysql_fetch_array($result2);
			insereLigne(5,"Partition ".$line2["num_partition"],"false","false");
			insereLigne(6,"Nom : ".$line2["nom_partition"],"true","false");
			insereLigne(6,"Taille : ".$line2["taille_partition"],"true","false");
			insereLigne(6,"Type : ".$line2["type_partition"],"true","false");
			insereLigne(6,"Systeme : ".$line2["systeme"],"true","false");
			$request3 = "SELECT nom_logiciel,nom_os,version FROM idb_est_installe_sur AS a, images_de_base AS b, logiciels WHERE nom_dns=\"$nom_dns\" AND a.id_idb=b.id_idb AND id_os=id_logiciel AND num_disque=\"".$line1["num_disque"]."\" AND num_partition=\"".$line2["num_partition"]."\"";
			$result3 = mysql_query($request3);
			if (mysql_num_rows($result3) > 0) {
				$line3 = mysql_fetch_array($result3);
				insereLigne(6,"Système exploitation : ".$line3["nom_logiciel"]." ".$line3["version"]." (".$line3["nom_os"]." )","true","false");
			}
			mysql_free_result($result3);
		}
		mysql_free_result($result2);
	}
	mysql_free_result($result1);
	insereLigne(2,"Autres","false","false");
	$request1 = "SELECT type,connectique FROM stockages_de_masse WHERE nom_dns=\"$nom_dns\" AND type!=\"disque dur\"";
	$result1 = mysql_query($request1);
	for ($i1=0;$i1<mysql_num_rows($result1);$i1++) {
	  $line1 = mysql_fetch_array($result1);
		insereLigne(3,"Lecteur ".$i1,"false","false");
		insereLigne(4,"Type : ".$line1["type"],"true","false");
		insereLigne(4,"Connectique : ".$line1["connectique"],"true","false");
	}
	mysql_free_result($result1);
	$request1 = "SELECT nom_groupe  FROM ord_appartient_a_gpe WHERE nom_dns=\"$nom_dns\"";
	$result1 = mysql_query($request1);
	insereLigne(1,"Groupes (".mysql_num_rows($result1).")","false","false");
	for ($i1=0;$i1<mysql_num_rows($result1);$i1++) {
	  $line1 = mysql_fetch_array($result1);
		insereLigne(2,"<A HREF='javascript:parent.location.href=\\\"examine_groupe.php?nom_groupe=".$line1["nom_groupe"]."\\\"'>".$line1["nom_groupe"]."</A>","true","false");
	}
	mysql_free_result($result1);
	insereLigne(1,"Distributions et Logiciels","false","false");
	$request1 = "SELECT id_logiciel,num_disque,num_partition,nom_os,nom_logiciel,version,etat_idb,cache,boot_options,date_install FROM idb_est_installe_sur AS a, images_de_base AS b, logiciels  WHERE nom_dns=\"$nom_dns\" AND a.id_idb=b.id_idb AND id_os=id_logiciel ORDER BY num_disque,num_partition";
	#echo "<br>req1 : $request1<br>";
	$result1 = mysql_query($request1);
	for ($i1=0;$i1<mysql_num_rows($result1);$i1++) {
		$line1 = mysql_fetch_array($result1);
		$request2 = " SELECT c.id_logiciel,nom_logiciel,version,etat_package FROM package_est_installe_sur AS a, packages AS b, logiciels AS c WHERE nom_dns=\"$nom_dns\" AND num_disque=".$line1["num_disque"]." AND num_partition=".$line1["num_partition"]." AND a.id_package=b.id_package AND b.id_logiciel=c.id_logiciel ORDER BY nom_logiciel,version";
		#echo "<br>req2 : $request2<br>";
		$result2 = mysql_query($request2);
		$ood="";
#		if ($line1["etat_idb"]!="a_ajouter" ) $ood="<font color='red'>...mais obsolète !</font>";
		insereLigne(2,"<A HREF='javascript:parent.location.href=\\\"examine_distribution.php?id_logiciel=".$line1["id_logiciel"]."\\\"'>".$line1["nom_logiciel"]." ".$line1["version"]."</A> <I>($line1[etat_idb]$ood)</I>","false","false");
		insereLigne(3,"Paramètres de boot","false","false");
		insereLigne(4,"Cachée : ".$line1["cache"],"true","false");
		if (EstUnLinux($line1["nom_os"]))
			insereLigne(4,"Options du noyau : ".$line1["boot_options"],"true","false");
		insereLigne(3,"Logiciels (".mysql_num_rows($result2).") ","false","false");
		for ($i2=0;$i2<mysql_num_rows($result2);$i2++) {
			$line2 = mysql_fetch_array($result2);
			$ood="";
			if ($line2["etat_package"]!="a_ajouter" && $line2["date_creation"]>$line2["date_install"]) $ood="<font color='red'>...mais obsolète !</font>";
			insereLigne(4,"<A HREF='javascript:parent.location.href=\\\"examine_logiciel.php?id_logiciel=".$line2["id_logiciel"]."\\\"'>".$line2["nom_logiciel"]." ".$line2["version"]."</A> <I>($line2[etat_package]$ood)</I>","true","false");
		}
		mysql_free_result($result2);
	}
	mysql_free_result($result1);
	insereLigne(1,"Autres","false","false");
	#insereLigne(2,"Extinction : ".$line["poweroff"],"true","false");
	mysql_free_result($result);
	DisconnectMySQL();
	print("  { level:-1, opened:false, name:\"end\", isleaf:true } ]\n");
	print("	\n");
	print("  var tree=new Array()\n");
	print("  var rootImage=new Image()\n");
	print("  var openedNodeImage=new Image()\n");
	print("  var closedNodeImage=new Image()\n");
	print("  var spaceImage=new Image()\n");
	print("  rootImage.src=\"ICONES/pc.gif\"\n");
	print("  openedNodeImage.src=\"ICONES/cfolder.gif\"\n");
	print("  closedNodeImage.src=\"ICONES/ofolder.gif\"\n");
	print("  spaceImage.src=\"ICONES/space.gif\"\n");
	print("  function newNode(level,opened,name,isleaf) {\n");
	print("    this.level=level\n");
	print("    this.opened=opened\n");
	print("    this.name=name\n");
	print("    this.isleaf=isleaf\n");
	print("  }\n\n");
	print("  for (i=0;i<fields.length;i++) {\n");
	print("    tree[i]=new newNode(fields[i].level,fields[i].opened, fields[i].name, fields[i].isleaf)\n");
	print("   }\n\n");
	print("</SCRIPT> \n");
} else $nom_dns="";
print("<HTML>\n");
print("<FRAMESET frameborder=0 border=0 framespacing=0 rows=\"160,*\">\n");
print("  <FRAME SRC=\"examine_machine_frame1.php?nom_dns=$nom_dns\" name=\"examine_machine\" SCROLLING=\"NO\" marginwidth=0 marginheight=0  noresize>\n");
print("  <FRAME src=\"explorer.html\" name=\"explorer\" marginwidth=0 marginheight=0  noresize>\n");
print("</FRAMESET>\n");
print("</HTML>\n");
?>

