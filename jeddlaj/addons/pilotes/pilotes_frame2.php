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


include("../../UtilsHTML.php");
include("../../UtilsMySQL.php");
include("../../UtilsJeDDLaJ.php");
include ("../../DBParDefaut.php");

entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "../../CSS/g.css", "JeDDLaJ : Pilotes");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

$nom_dns=$_POST["nom_dns"];
$nom_os=$_POST["nom_os"];

function affiche_pilote($pilote,$osubsys,$psubsys,$id_pilote,$id_pilote_utilise) {
	if ($pilote=="" || $pilote=="/") return;
	print("<TR><TD><INPUT TYPE=checkbox NAME=\"pilotes[]\" VALUE=\"$id_pilote\"");
	if ($id_pilote==$id_pilote_utilise) print(" CHECKED ");
	if ($osubsys=="0000:0000" && $psubsys!="0000:0000") print("><FONT COLOR=RED>$pilote</FONT>");
	else if ($osubsys!="0000:0000" && $osubsys==$psubsys) print("><B>$pilote</B>");
	else print(">$pilote");
	print("</TD><TR>\n");
}

if (isset($_POST["signature"])) {
	$signature=$_POST["signature"];
	mysql_query("DELETE pilote_a_utiliser_sur AS a FROM pilote_a_utiliser_sur AS a,pilotes AS b WHERE a.id_pilote=b.id_pilote AND signature=\"$signature\" AND nom_os=\"$nom_os\"");
	if (isset($_POST["pilotes"])) {
		$pilotes=$_POST["pilotes"];
		for ($i=0;$i<sizeof($pilotes);$i++) 
			mysql_query("INSERT INTO pilote_a_utiliser_sur(id_pilote,signature) VALUES(\"$pilotes[$i]\",\"$signature\")");
	}
}  

print("<CENTER>\n");
print("<FORM METHOD=\"POST\" NAME=\"form\">\n");
if (!EstUnLinux($nom_os)) { 
	print("<INPUT TYPE=BUTTON VALUE=\"Tout selectionner\" OnClick=\"javascript:for (i=2;i<document.form.length-1;i++) document.form[i].checked=true;\">&nbsp;");
	print("<INPUT TYPE=BUTTON VALUE=\"Inverser la selection\" OnClick=\"javascript:for (i=2;i<document.form.length-1;i++) document.form[i].checked=!document.form[i].checked;\"><BR><BR>");
}
print("<TABLE BORDER=1>\n");
print("<TR><TD ALIGN=CENTER>Type de composant</TD><TD ALIGN=CENTER>Nom du composant</TD><TD ALIGN=CENTER>ID</TD><TD ALIGN=CENTER>Subsys</TD><TD ALIGN=CENTER>Pilotes</TD><TD ALIGN=CENTER>Nombre pilotes</TD></TR>\n");
if (EstUnLinux($nom_os)) 
	$request = "SELECT b.id_composant,nom,type,module_linux AS pilote,\"0000:0000\" as osubsys FROM composant_est_installe_sur AS a,composants AS b WHERE nom_dns=\"$nom_dns\" AND b.id_composant=a.id_composant ORDER BY id_composant";
else 
	$request = "SELECT c.signature,a.id_composant,a.subsys AS osubsys, d.subsys AS psubsys,nom,type,inf_path,inf_file AS pilote,d.id_pilote,e.id_pilote AS id_pilote_utilise FROM composant_est_installe_sur AS a INNER JOIN composants AS b INNER join ordinateurs AS c LEFT JOIN pilotes AS d ON a.id_composant=d.id_composant AND (a.subsys=d.subsys OR d.subsys=\"0000:0000\" OR a.subsys=\"0000:0000\") AND nom_os=\"$nom_os\" LEFT JOIN pilote_a_utiliser_sur AS e ON c.signature=e.signature AND d.id_pilote=e.id_pilote WHERE a.nom_dns=\"$nom_dns\" AND a.id_composant=b.id_composant AND a.nom_dns=c.nom_dns GROUP BY a.id_composant,inf_path,inf_file ORDER BY a.id_composant ASC,d.subsys DESC";
$id_composant="";
$total=0;
$totalall=0;
$result=mysql_query($request);
for ($i=0;$i<mysql_num_rows($result);$i++) {
	$line=mysql_fetch_array($result);
	if ($id_composant!=$line["id_composant"]) {
		if ($id_composant!="") {
			print("</TABLE>\n");
			print("<TD ALIGN=CENTER>$total</TD>\n");
			print("</TD></TR>\n");
		}
		print("<TR><TD>$line[type]</TD><TD>$line[nom]</TD><TD>$line[id_composant]</TD><TD>$line[osubsys]</TD>\n");
		print("<TD><TABLE>\n");
		if (!EstUnLinux($nom_os)) affiche_pilote("$line[inf_path]/$line[pilote]",$line["osubsys"],$line["psubsys"],$line["id_pilote"],$line["id_pilote_utilise"]);
		else print("<TR><TD>$line[pilote]</TD></TR>\n");
		$id_composant=$line["id_composant"];
		if ($line["pilote"]!="") $total=1;
		else $total=0;
	} else {
		if ($line["pilote"]!="") $total++;
		affiche_pilote("$line[inf_path]/$line[pilote]",$line["osubsys"],$line["psubsys"],$line["id_pilote"],$line["id_pilote_utilise"]);
	}	
	$totalall+=$total;
}
print("</TABLE></TD><TD ALIGN=CENTER>$total</TD></TR></TABLE>\n");
print("<BR>\n");
if (!EstUnLinux($nom_os)) { 
	print("<INPUT TYPE=HIDDEN NAME=\"nom_dns\" VALUE=\"$nom_dns\">");
	print("<INPUT TYPE=HIDDEN NAME=\"nom_os\" VALUE=\"$nom_os\">");
	print("<INPUT TYPE=HIDDEN NAME=\"signature\" VALUE=\"$line[signature]\">");
	print("<INPUT TYPE=SUBMIT VALUE=\"Fixer les pilotes pour cette architecture et cet OS\">");
}
print("</FORM>\n");
print("</CENTER>\n");

DisconnectMySQL();

PiedPage();

?>
