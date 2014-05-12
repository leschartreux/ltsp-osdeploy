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

include("../DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "../CSS/g.css", "JeDDLaJ : Modification d'un composant dans la base");

$types = array ('controleur disque','carte reseau','carte video','carte multimedia','carte memoire','pont pci','port de communication','peripherique systeme','peripherique entree','processeur','port serie','inconnu');
$types_modules = array ('kernel','xfree','kernel+xfree');

function affiche_liste($nom,$tab,$default) {
	print("<SELECT name=\"$nom\">");
	for ($i=0;$i<sizeof($tab);$i++) {
		print("<OPTION VALUE=\"$tab[$i]\"");
		if ($default==$tab[$i]) print(" SELECTED");
		print(">$tab[$i]</OPTION>\n");
	}
	print("</SELECT>\n");
}

print("<CENTER>\n");

$id_composant=$_GET["id_composant"];

if ($id_composant!="") {
	print("<FORM METHOD=GET NAME=\"form\" ACTION=\"modification_composant_frame2.php\" TARGET=\"selection\">\n");
	print("<TABLE>\n");
	if (isset($_GET["modification"])) {
		$nom=$_GET["nom"];
		if ($nom=="") $nom="inconnu";
		$type=$_GET["type"];
		$module_linux=$_GET["module_linux"];
		if ($module_linux=="") $module_linux="inconnu";
		$type_module=$_GET["type_module"];
		$parametres_module=$_GET["parametres_module"];
		$request="UPDATE composants SET nom=\"$nom\",type=\"$type\",module_linux=\"$module_linux\",type_module=\"$type_module\",parametres_module=\"$parametres_module\" WHERE id_composant=\"$id_composant\"";
		mysql_query($request);
		if (mysql_affected_rows()>0) print("<TR><TD colspan=3 align=center>Mise à jour réussie</TD></TR>\n");
		else print("<TR><TD colspan=3 align=center><FONT COLOR=\"red\">La mise à jour a echoué</FONT></TD></TR>\n");
		print("<TR><TD>&nbsp;</TD></TR>\n");
	} else print("<TR><TD>&nbsp;</TD></TR><TR><TD>&nbsp;</TD></TR>\n");
	$request = "SELECT * FROM composants AS a LEFT JOIN composant_est_installe_sur AS b ON a.id_composant=b.id_composant  WHERE a.id_composant=\"$id_composant\"";
	$result=mysql_query($request);
	$line=mysql_fetch_array($result);
	print("<TR><TD>Nom composant</TD><TD>:</TD><TD><INPUT TYPE=text NAME=nom SIZE=100 VALUE=\"$line[nom]\"></TD></TR>\n");
	print("<TR><TD>Type composant</TD><TD>:</TD><TD>");
	affiche_liste("type",$types,$line["type"]);
	print("</TD></TR>\n");
	print("<TR><TD>Module Linux</TD><TD>:</TD><TD><INPUT TYPE=text NAME=module_linux SIZE=30 VALUE=\"$line[module_linux]\"></TD></TR>\n");
	print("<TR><TD>Type module</TD><TD>:</TD><TD>");
	affiche_liste("type_module",$types_modules,$line["type_module"]);
	print("</TD></TR>\n");
	print("<TR><TD>Paramètres module</TD><TD>:</TD><TD><INPUT TYPE=text NAME=parametres_module SIZE=100 VALUE=\"$line[parametres_module]\"></TD></TR>\n");
	printf("<TR><TD>Composant utilisé</TD><TD>:</TD><TD>%s</TD></TR>\n",($line["nom_dns"]==""?"NON":"OUI"));
	print("</TABLE>\n");
	print("<INPUT TYPE=submit VALUE=VALIDER>\n");
	print("<INPUT TYPE=hidden name=modification>\n");
	print("<INPUT TYPE=hidden name=id_composant VALUE=\"$id_composant\">\n");
	print("</FORM>\n");
}

print("</CENTER>\n");
DisconnectMySQL();

PiedPage();

?>
