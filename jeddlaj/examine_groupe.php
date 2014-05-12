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

include("UtilsMySQL.php");
include("UtilsJeDDLaJ.php");
include ("DBParDefaut.consult.php");


print("<HTML>\n");
print("<HEAD>\n");
print("<TITLE>JeDDLaJ : Examine Groupe</TITLE>\n");
print("<!--[if IE]><LINK REL=\"shortcut icon\" TYPE=\"image/x-icon\" HREF=\"ICONES/favicon.ico\" /><![endif]-->\n");
print("<LINK REL=\"icon\" TYPE=\"image/png\" HREF=\"ICONES/favicon.png\">\n");
print("\n");

function insereLigne($level,$name,$isleaf,$opened) {
	print("  { level:$level, opened:$opened, name:\"$name\", isleaf:$isleaf },\n");
}

if (isset($_GET["nom_groupe"])) {
	$nom_groupe = $_GET["nom_groupe"];
	ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
	SelectDb($GLOBALS['db']);
	print("<SCRIPT LANGUAGE=\"javascript\">\n");
	print("\n");
	print("var fields = [\n");
	$request = "SELECT * FROM groupes WHERE nom_groupe=\"$nom_groupe\"";
	$result = mysql_query($request);
	$line = mysql_fetch_array($result);
	insereLigne(0,$nom_groupe,"false","true");
	$result1=groupes_incluant($nom_groupe);
	insereLigne(1,"Sur Groupes (".sizeof($result1).")","false","false");
	for ($i1=0;$i1<sizeof($result1);$i1++) 
		insereLigne(2,"<A HREF='javascript:parent.location.href=\\\"examine_groupe.php?nom_groupe=".$result1[$i1]."\\\"'>".$result1[$i1]."</A>","true","false");
	$result1=groupes_inclus($nom_groupe);
	insereLigne(1,"Sous Groupes (".sizeof($result1).")","false","false");
	for ($i1=0;$i1<sizeof($result1);$i1++) 
		insereLigne(2,"<A HREF='javascript:parent.location.href=\\\"examine_groupe.php?nom_groupe=".$result1[$i1]."\\\"'>".$result1[$i1]."</A>","true","false");
	$request1 = "SELECT a.nom_dns,etat_install FROM ord_appartient_a_gpe AS a,ordinateurs AS b WHERE nom_groupe=\"$nom_groupe\" AND a.nom_dns=b.nom_dns ORDER BY nom_dns";
	$result1 = mysql_query($request1);
	insereLigne(1,"Ordinateurs (".mysql_num_rows($result1).")","false","false");
	for ($i1=0;$i1<mysql_num_rows($result1);$i1++) {
	  $line1 = mysql_fetch_array($result1);
		insereLigne(2,"<A HREF='javascript:parent.location.href=\\\"examine_machine.php?nom_dns=".$line1["nom_dns"]."\\\"'>".$line1["nom_dns"]."</A> <I>(".$line1["etat_install"].")</I>","true","false");
	}
	mysql_free_result($result1);
	insereLigne(1,"Distributions et Logiciels","false","false");
	$request1 = "SELECT DISTINCT nom_os FROM idb_est_installe_sur AS a, images_de_base AS b, logiciels, ord_appartient_a_gpe AS c WHERE nom_groupe=\"$nom_groupe\" AND a.nom_dns=c.nom_dns AND a.id_idb=b.id_idb AND id_os=id_logiciel";
	$result1 = mysql_query($request1);
	for ($i1=0;$i1<mysql_num_rows($result1);$i1++) {
	  $line1 = mysql_fetch_array($result1);
		insereLigne(2,$line1["nom_os"],"false","false");
		$request2 = "SELECT DISTINCT id_logiciel,nom_logiciel,version FROM idb_est_installe_sur AS a, images_de_base AS b, logiciels, ord_appartient_a_gpe AS d WHERE a.nom_dns=d.nom_dns AND nom_groupe=\"$nom_groupe\" AND a.id_idb=b.id_idb AND id_logiciel=id_os AND nom_os=\"$line1[nom_os]\" ORDER BY nom_logiciel,version";
		$result2 = mysql_query($request2);
		insereLigne(3,"Distributions  (".mysql_num_rows($result2).")","false","false");
		for ($i2=0;$i2<mysql_num_rows($result2);$i2++) {
		  $line2 = mysql_fetch_array($result2);
			insereLigne(4,"<A HREF='javascript:parent.location.href=\\\"examine_distribution.php?id_logiciel=".$line2["id_logiciel"]."\\\"'>".$line2["nom_logiciel"]." ".$line2["version"]."</A>","true","false");
		}
		mysql_free_result($result2);
		$request2 = "SELECT DISTINCT c.id_logiciel,nom_logiciel,version FROM package_est_installe_sur AS a, packages AS b, logiciels AS c, ord_appartient_a_gpe AS d WHERE a.nom_dns=d.nom_dns AND nom_groupe=\"$nom_groupe\" AND a.id_package=b.id_package AND b.id_logiciel=c.id_logiciel AND nom_os=\"$line1[nom_os]\" ORDER BY nom_logiciel,version";
		$result2 = mysql_query($request2);
		insereLigne(3,"Logiciels (".mysql_num_rows($result2).")","false","false");
		for ($i2=0;$i2<mysql_num_rows($result2);$i2++) {
		  $line2 = mysql_fetch_array($result2);
			insereLigne(4,"<A HREF='javascript:parent.location.href=\\\"examine_logiciel.php?id_logiciel=".$line2["id_logiciel"]."\\\"'>".$line2["nom_logiciel"]." ".$line2["version"]."</A>","true","false");
		}
		mysql_free_result($result2);
	}
	mysql_free_result($result);
	DisconnectMySQL();
	print("  { level:-1, opened:false, name:\"end\", isleaf:true } ]\n");
	print("	\n");
	print("  var tree=new Array()\n");
	print("  var rootImage=new Image()\n");
	print("  var openedNodeImage=new Image()\n");
	print("  var closedNodeImage=new Image()\n");
	print("  var spaceImage=new Image()\n");
	print("  rootImage.src=\"ICONES/pcs.gif\"\n");
	print("  openedNodeImage.src=\"ICONES/cfolder.gif\"\n");
	print("  closedNodeImage.src=\"ICONES/ofolder.gif\"\n");
	print("  spaceImage.src=\"ICONES/space.gif\"\n");
	print("  function newNode(level,opened,name,isleaf) {\n");
	print("    this.level=level\n");
	print("    this.opened=opened\n");
	print("    this.name=name\n");
	print("    this.isleaf=isleaf\n");
	print("  }\n\n");
	print("  function changePage(url) {\n");
	print("    parent.location.href=url\n");
	print("  }\n\n");
	print("  for (i=0;i<fields.length;i++) {\n");
	print("    tree[i]=new newNode(fields[i].level,fields[i].opened, fields[i].name, fields[i].isleaf)\n");
	print("   }\n\n");
	print("</SCRIPT> \n");
} else $nom_groupe="";
print("<HTML>\n");
print("<FRAMESET frameborder=0 border=0 framespacing=0 rows=\"170,*\">\n");
print("  <FRAME SRC=\"examine_groupe_frame1.php?nom_groupe=$nom_groupe\" name=\"examine_groupe\" SCROLLING=\"NO\" marginwidth=0 marginheight=0  noresize>\n");
print("  <FRAME src=\"explorer.html\" name=\"explorer\" marginwidth=0 marginheight=0  noresize>\n");
print("</FRAMESET>\n");
print("</HTML>\n");
?>

