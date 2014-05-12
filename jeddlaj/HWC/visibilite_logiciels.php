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
include("../UtilsJeDDLaJ.php");

entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "../CSS/g.css", "JeDDLaJ : Visibilité des logiciels");
include ("../DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<CENTER><H1>Visibilité des logiciels</H1></CENTER>\n");
print("<P><CENTER><A HREF=\"javascript:parent.location.href='accueil.php'\">Retour</A></CENTER></P>\n");
print("<CENTER>\n");

if (isset($_POST["validation"])) {
	$visible=$_POST["visible"];
	$invisible=$_POST["invisible"];
	for ($i=0;$i<count($visible);$i++) {
		$request="UPDATE logiciels SET visible=\"oui\" WHERE id_logiciel=\"$visible[$i]\"";
		mysql_query($request);
	}
	for ($i=0;$i<count($invisible);$i++) {
		$request="UPDATE logiciels SET visible=\"non\" WHERE id_logiciel=\"$invisible[$i]\"";
		mysql_query($request);
	}
} 
$request="SELECT DISTINCT a.id_logiciel,nom_logiciel,version,nom_os,visible, if(c.id_package IS NULL,0,count(nom_dns)) AS total FROM logiciels AS a, packages AS b LEFT JOIN package_est_installe_sur AS c ON b.id_package=c.id_package WHERE a.id_logiciel=b.id_logiciel GROUP BY a.id_logiciel ORDER BY nom_os,nom_logiciel,version";
$result=mysql_query($request);
if (mysql_num_rows($result)>0) {
	print("<FORM METHOD=POST NAME=\"form\" ACTION=\"visibilite_logiciels.php\">\n");
	print("<TABLE>");
	print("<TR><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Logiciels</b></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Version</b></TD><TD ALIGN=\"center\"  BGCOLOR=\"#CC00AA\"><b>OS</b></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>#Installations</b></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Visible</b></TD></TR>\n");
	for ($i=0;$i<mysql_num_rows($result);$i++) {
		$line=mysql_fetch_array($result);
		$color=$i%2?"#00AA00":"#FFFFFF";
		print("<TR><TD BGCOLOR=$color>$line[nom_logiciel]</TD><TD BGCOLOR=$color>$line[version]</TD><TD BGCOLOR=$color>$line[nom_os]</TD><TD BGCOLOR=$color ALIGN=right>$line[total]</TD>");
		if ($line["visible"]=="oui") print("<TD BGCOLOR=$color><INPUT TYPE=checkbox CHECKED DISABLED>oui<INPUT TYPE=checkbox NAME=\"invisible[]\" VALUE=\"$line[id_logiciel]\">non</TD>\n");
		else print("<TD BGCOLOR=$color><INPUT TYPE=checkbox NAME=\"visible[]\" VALUE=\"$line[id_logiciel]\">oui<INPUT TYPE=checkbox CHECKED DISABLED>non</TD>\n");
	}
	mysql_free_result($result);
	print("</TABLE><BR>");
} else print("Aucun logiciel sélectionnable<BR>\n");
print("<INPUT TYPE=hidden NAME=validation>\n");
print("<INPUT TYPE=submit VALUE=VALIDER>\n");
print("</CENTER>\n");

DisconnectMySQL();

PiedPage();

?>
