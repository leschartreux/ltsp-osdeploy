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
# JeDDLaJ est un distribution libre : vous pouvez le redistribuer ou le modifier
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



include("../UtilsHTML.php");
include("../UtilsMySQL.php");
include("../UtilsJeDDLaJ.php");

entete("G�rard Milhaud & Fr�d�ric Bloise : La.Firme@esil.univ-mrs.fr", "../CSS/g.css", "JeDDLaJ : Visibilit� des distributions");
include ("../DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<CENTER><H1>Visibilit� des distributions</H1></CENTER>\n");
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
$request="SELECT DISTINCT a.id_logiciel,nom_logiciel,version,nom_os,visible, if(c.id_idb IS NULL,0,count(nom_dns)) AS total FROM logiciels AS a, images_de_base AS b LEFT JOIN idb_est_installe_sur AS c ON b.id_idb=c.id_idb WHERE a.id_logiciel=b.id_os GROUP BY a.id_logiciel HAVING total=0 ORDER BY nom_os,nom_logiciel,version";
$result=mysql_query($request);
if (mysql_num_rows($result)>0) {
	print("<FORM METHOD=POST NAME=\"form\" ACTION=\"visibilite_distributions.php\">\n");
	print("<TABLE>");
	print("<TR><TD ALIGN=\"center\"  BGCOLOR=\"#CC00AA\"><b>Distributions</b></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Version</b></TD><TD ALIGN=\"center\"  BGCOLOR=\"#CC00AA\"><b>OS</b></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Visible</b></TD></TR>\n");
	for ($i=0;$i<mysql_num_rows($result);$i++) {
		$line=mysql_fetch_array($result);
		$color=$i%2?"#00AA00":"#FFFFFF";
		print("<TR><TD BGCOLOR=$color>$line[nom_logiciel]</TD><TD BGCOLOR=$color>$line[version]</TD><TD BGCOLOR=$color>$line[nom_os]</TD>");
		if ($line["visible"]=="oui") print("<TD BGCOLOR=$color><INPUT TYPE=checkbox CHECKED DISABLED\">oui<INPUT TYPE=checkbox NAME=\"invisible[]\" VALUE=\"$line[id_logiciel]\">non</TD>");
		else print("<TD BGCOLOR=$color><INPUT TYPE=checkbox NAME=\"visible[]\" VALUE=\"$line[id_logiciel]\">oui<INPUT TYPE=checkbox CHECKED DISABLED\">non</TD>");
	}
	mysql_free_result($result);
	print("</TABLE><BR>");
} else print("Aucun distribution s�lectionnable<BR>\n");
print("<INPUT TYPE=hidden NAME=validation>\n");
print("<INPUT TYPE=submit VALUE=VALIDER>\n");
print("</CENTER>\n");

DisconnectMySQL();

PiedPage();

?>
