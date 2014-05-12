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
include("UtilsHTML.php");
include ("DBParDefaut.consult.php");


print("<HTML>\n");
print("<HEAD>\n");
print("<TITLE>JeDDLaJ : Examine Logiciel</TITLE>\n");
print("<META http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-15\">\n");
print("<!--[if IE]><LINK REL=\"shortcut icon\" TYPE=\"image/x-icon\" HREF=\"ICONES/favicon.ico\" /><![endif]-->\n");
print("<LINK REL=\"icon\" TYPE=\"image/png\" HREF=\"ICONES/favicon.png\">\n");
print("\n");

function insereLigne($level,$name,$isleaf,$opened) {
	print("  { level:$level, opened:$opened, name:\"$name\", isleaf:$isleaf },\n");
}

if (isset($_GET["id_logiciel"])) {
	$id_logiciel = $_GET["id_logiciel"];
	ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
	SelectDb($GLOBALS['db']);
	print("<SCRIPT LANGUAGE=\"javascript\">\n");
	print("\n");
	print("var fields = [\n");
	$request = "SELECT * FROM logiciels WHERE id_logiciel=\"$id_logiciel\"";
	$result = mysql_query($request);
	$line = mysql_fetch_array($result);
	insereLigne(0,$line["nom_logiciel"]." ".$line["version"],"false","true");
	insereLigne(1,"Caractéristiques","false","true");
	insereLigne(2,"Nom logiciel : ".$line["nom_logiciel"],"true","false");
	insereLigne(2,"Version : ".$line["version"],"true","false");
	insereLigne(2,"OS : <b>".$line["nom_os"]."</b>","true","false");
	insereLigne(2,"visible : ".$line["visible"],"true","false");
	if ($line["description"]!="") {
		insereLigne(2,"Description/Remarques","false","true");
		insereLigne(3,str_replace("\r\n","<br>",$line["description"]),"true","false");
	}
	$request1 = "SELECT nom_dns,etat_package,date_install,date_creation FROM package_est_installe_sur AS a, packages AS b WHERE b.id_logiciel=\"$id_logiciel\" AND a.id_package=b.id_package ORDER BY nom_dns";
	$result1 = mysql_query($request1);
	insereLigne(1,"Ordinateurs (".mysql_num_rows($result1).")","false","false");
	for ($i1=0;$i1<mysql_num_rows($result1);$i1++) {
		$line1 = mysql_fetch_array($result1);
		$ood="";
		if ($line1["etat_package"]!="a_ajouter" && $line1["date_creation"]>$line1["date_install"]) $ood="<font color='red'>...mais obsolète !</font>";
		insereLigne(2,"<A HREF='javascript:parent.location.href=\\\"examine_machine.php?nom_dns=".$line1["nom_dns"]."\\\"'>".$line1["nom_dns"]."</A> <I>(".$line1["etat_package"]."$ood)</I>","true","false");
	}
	mysql_free_result($result1);
	$request1 = "SELECT * FROM packages WHERE id_logiciel=\"$id_logiciel\" ORDER BY id_package";
	$result1 = mysql_query($request1);
	insereLigne(1,"Packages (".mysql_num_rows($result1).")","false","false");
	for ($i1=0;$i1<mysql_num_rows($result1);$i1++) {
		$line1 = mysql_fetch_array($result1);
		insereLigne(2,$line1["nom_package"],"false","false");
		insereLigne(3,"Specificite : ".$line1["specificite"],"true","false");
		if ($line1["specificite"]!="aucune") {
			insereLigne(3,"Valeur specificite : ".$line1["valeur_specificite"],"true","false");
			$request2="SELECT DISTINCT b.nom_dns FROM packages AS a, ordinateurs AS b, composant_est_installe_sur AS c WHERE id_package=\"$line1[id_package]\" AND b.nom_dns=c.nom_dns AND ( (specificite=\"nom_dns\" AND valeur_specificite=b.nom_dns) OR ( specificite=\"signature\" AND valeur_specificite=signature) OR (valeur_specificite=c.id_composant AND b.nom_dns=c.nom_dns)) ORDER BY nom_dns";
			$result2 = mysql_query($request2);
			insereLigne(3,"Ordinateurs installables (".mysql_num_rows($result2).")","false","false");
			for ($i2=0;$i2<mysql_num_rows($result2);$i2++) {
	  		$line2 = mysql_fetch_array($result2);
				insereLigne(4,"<A HREF='javascript:parent.location.href=\\\"examine_machine.php?nom_dns=".$line2["nom_dns"]."\\\"'>".$line2["nom_dns"]."</A>","true","false");
			}
			mysql_free_result($result2);
		}
	}
	mysql_free_result($result1);
	insereLigne(1,"Scripts de post-installation","false","false");
	$request1 = "SELECT * FROM postinstall_scripts AS a, pis_est_associe_a AS b WHERE b.id_logiciel=$id_logiciel AND a.id_script=b.id_script ORDER BY a.id_script";
	$result1 = mysql_query($request1);
	for ($i1=0;$i1<mysql_num_rows($result1);$i1++) {
	  $line1 = mysql_fetch_array($result1);
		insereLigne(2,"Script $i1","false","false");
		insereLigne(3,"Nom script : ".$line1["nom_script"],"true","false");
		switch($line1["applicable_a"]) {
			case "nom_dns" :
				insereLigne(3,"Applicable à l'ordinateur : <A HREF='javascript:parent.location.href=\\\"examine_machine.php?nom_dns=".$line1["valeur_application"]."\\\"'>".$line1["valeur_application"]."</A>","true","false");
				break;
			case "nom_groupe" :
				insereLigne(3,"Applicable au groupe : <A HREF='javascript:parent.location.href=\\\"examine_groupe.php?nom_groupe=".$line1["valeur_application"]."\\\"'>".$line1["valeur_application"]."</A>","true","false");
				break;
			default :
				insereLigne(3,"Applicable à rien pour l'instant","true","false");
		}
	}
	insereLigne(1,"Scripts de pré-désinstallation","false","false");
	$request1 = "SELECT * FROM predeinstall_scripts AS a, pdis_est_associe_a AS b WHERE b.id_logiciel=$id_logiciel AND a.id_script=b.id_script ORDER BY a.id_script";
	$result1 = mysql_query($request1);
	for ($i1=0;$i1<mysql_num_rows($result1);$i1++) {
	  $line1 = mysql_fetch_array($result1);
		insereLigne(2,"Script $i1","false","false");
		insereLigne(3,"Nom script : ".$line1["nom_script"],"true","false");
		switch($line1["applicable_a"]) {
			case "nom_dns" :
				insereLigne(3,"Applicable à l'ordinateur : <A HREF='javascript:parent.location.href=\\\"examine_machine.php?nom_dns=".$line1["valeur_application"]."\\\"'>".$line1["valeur_application"]."</A>","true","false");
				break;
			case "nom_groupe" :
				insereLigne(3,"Applicable au groupe : <A HREF='javascript:parent.location.href=\\\"examine_groupe.php?nom_groupe=".$line1["valeur_application"]."\\\"'>".$line1["valeur_application"]."</A>","true","false");
				break;
			default :
				insereLigne(3,"Applicable à rien pour l'instant","true","false");
		}
	}
	print("  { level:-1, opened:false, name:\"end\", isleaf:true } ]\n");
	print("	\n");
	print("  var tree=new Array()\n");
	print("  var rootImage=new Image()\n");
	print("  var openedNodeImage=new Image()\n");
	print("  var closedNodeImage=new Image()\n");
	print("  var spaceImage=new Image()\n");
	print("  rootImage.src=\"ICONES/".$line["icone"]."\"\n");
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
	mysql_free_result($result);
	DisconnectMySQL();
} else $id_logiciel="";
print("<HTML>\n");
print("<FRAMESET frameborder=0 border=0 framespacing=0 rows=\"170,*\">\n");
print("  <FRAME SRC=\"examine_logiciel_frame1.php?id_logiciel=$id_logiciel\" name=\"examine_logiciel\" SCROLLING=\"NO\" marginwidth=0 marginheight=0  noresize>\n");
print("  <FRAME src=\"explorer.html\" name=\"explorer\" marginwidth=0 marginheight=0  noresize>\n");
print("</FRAMESET>\n");
print("</HTML>\n");

PiedPage();
?>

