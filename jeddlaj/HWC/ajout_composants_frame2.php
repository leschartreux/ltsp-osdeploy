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
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "../CSS/g.css", "JeDDLaJ : Ajout de composants");

$nom_dns=$_POST["nom_dns"];

print("<CENTER>\n");

if ($nom_dns!="") {
	if (isset($_POST["id_composant"]) && $_POST["id_composant"]!="-1") {
		$id_composant=$_POST["id_composant"];
		$request = "INSERT INTO composant_est_installe_sur(id_comp_sur,nom_dns,id_composant,ajout) SELECT MAX(id_comp_sur)+1,\"$nom_dns\",\"$id_composant\",\"oui\" FROM composant_est_installe_sur WHERE nom_dns=\"$nom_dns\"";
		$result=mysql_query($request);
	}
	if (isset($_POST["id_comp_sur"])) {
		$id_comp_sur=$_POST["id_comp_sur"];
		$request = "DELETE FROM composant_est_installe_sur WHERE id_comp_sur=\"$id_comp_sur\"";
		$result=mysql_query($request);
	}
	$list="(\"-1\"";
	print("<FORM METHOD=POST NAME=\"form\" ACTION=\"ajout_composants_frame2.php\" TARGET=\"selection\">\n");
	$request = "SELECT * FROM composant_est_installe_sur AS a, composants AS b, packages AS c, package_est_installe_sur AS d WHERE a.id_composant=b.id_composant AND a.nom_dns=\"$nom_dns\" AND ajout=\"oui\" AND a.id_composant AND c.id_package=d.id_package AND c.specificite=\"id_composant\" AND c.valeur_specificite=a.id_composant AND d.nom_dns=\"$nom_dns\" ORDER BY nom DESC";
	$result=mysql_query($request);
	if (mysql_num_rows($result)>0) { 
		$s=mysql_num_rows($result)>1?"s":"";
		print("<TABLE>\n");
		print("<TR><TD>composant$s utilisé$s</TD><TD>:</TD><TD><SELECT>\n");
		for ($i=0;$i<mysql_num_rows($result);$i++) {
			$line=mysql_fetch_array($result);	
			print("<OPTION>$line[id_composant] $line[nom]</OPTION>\n");
			$list.=",\"$line[id_composant]\"";
		}
		print("</TD></TR>\n");
		$used=true;
	} else $used=false;
	$list.=")";
	$request = "SELECT * FROM composant_est_installe_sur AS a, composants AS b WHERE a.id_composant=b.id_composant AND nom_dns=\"$nom_dns\" AND ajout=\"oui\" AND a.id_composant NOT IN $list ORDER BY type,id_comp_sur DESC";
	$result=mysql_query($request);
	if (mysql_num_rows($result)>0) { 
		$type="";
		$entry=0;
		if (!$used) print("<TABLE>\n");
		for ($i=0;$i<mysql_num_rows($result);$i++) {
			$line=mysql_fetch_array($result);	
			if ($line["type"]!=$type) {
				if ($type!="") print("</TD><TD align=right><INPUT TYPE=button VALUE=\"-\" onClick=\"document.form.id_comp_sur.value=document.form.entry_${entry}[document.form.entry_$entry.selectedIndex].value;document.form.submit()\"></SELECT></TD></TR>");
				$entry++;
				print("<TR><TD>$line[type]</TD><TD>:</TD><TD><SELECT NAME=entry_$entry>\n");
				$type=$line["type"];
			}
			print("<OPTION value=\"$line[id_comp_sur]\">$line[id_composant] $line[nom]</OPTION>\n");
		}
		print("</TD><TD align=right><INPUT TYPE=button VALUE=\"-\" onClick=\"document.form.id_comp_sur.value=document.form.entry_${entry}[document.form.entry_$entry.selectedIndex].value;document.form.submit()\"></SELECT></TD></TR></TABLE>\n");
		print("<INPUT TYPE=hidden NAME=id_comp_sur VALUE=-1>\n");
		print("<INPUT TYPE=hidden NAME=nom_dns VALUE=$nom_dns>\n");
		$add=true;
	} else $add=false;
	if ($used || $add) print("</TABLE>\n");
	else print("Pas de composant ajouté sur cette machine.");
	print("</FORM>\n");
	mysql_free_result($result);
} else print("Aucun ordinateur sélectionné.\n");

print("</CENTER>\n");
DisconnectMySQL();

PiedPage();

?>
