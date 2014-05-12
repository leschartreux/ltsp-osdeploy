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



include("UtilsHTML.php");
include("ExpectDefs.php");
entete("G�rard Milhaud & Fr�d�ric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Connexion au serveur Rembo");

$server=$GLOBALS['rembo_server'];
$passwd=$GLOBALS['rembo_passwd'];
$rep=$_GET["rep"];

function is_editable($file) {
	return preg_match("/\.(shtml|html|log|rbc|txt|ini|conf|inf|bat|csv)$/",$file) || $file=="autoload";
}

print("<CENTER>\n");
print("<FORM>\n");
print("<TABLE WIDTH=\"50%\">\n");
print("<TR><TD align=left><INPUT TYPE=button VALUE='Nouveau r�pertoire' onClick='javascript:parent.frames[\"control\"].location.href=\"connect_control.php?menu=namedir&rep=$rep\"'></TD>\n");
print("<TD align=center><INPUT TYPE=button VALUE='Nouveau fichier' onClick='javascript:location.href=\"connect_edit.php?rep=$rep&file=sans_titre.txt&new=true\"'></TD>\n");
print("<TD align=right><INPUT TYPE=button VALUE='D�poser un fichier' onClick='javascript:parent.frames[\"control\"].location.href=\"connect_control.php?menu=upload&rep=$rep\"'></TD></TR>\n");
print("</TABLE>\n");

$cmd="./list.expect $netclnt_program $server $passwd $rep";
$ret=shell_exec($cmd);
$lignes=split("\r",$ret);
print("<TABLE>\n");
if ($rep!="/") {
	$lastslash=strrpos(substr($rep,0,strlen($rep)-1),"/");
	printf("<TR><TD><A HREF='connect.php?rep=%s'>&lt;..&gt;</A>",substr($rep,0,$lastslash+1));
}
for ($i=0;$i<count($lignes)-1;$i++) {
	$cel=preg_split("/[\s]+/",$lignes[$i]);
	if ($cel[2]=="dir") break;
}
for ($i++;$i<count($lignes)-1;$i++) {
	print("<TR><TD>");
	$cel=preg_split("/[\s]+/",$lignes[$i]);
	if ($cel[3]=="<DIR>") {
		printf("<A HREF='connect.php?rep=%s'>&lt;$cel[1]&gt;</A></TD><TD>$cel[2]</TD><TD></TD><TD>$cel[4]</TD><TD>$cel[5]",$rep.$cel[1]."/");
		print("</TD><TD><INPUT TYPE=button VALUE=Del onClick='javascript:parent.frames[\"control\"].location.href=\"connect_control.php?rep=$rep&file=".urlencode($cel[1])."&menu=delete&command=deltree\"'>");
	} else {
		if (is_editable($cel[1])) printf("<A HREF='connect_edit.php?rep=%s&file=%s&new=false'>$cel[1]</A></TD><TD>$cel[2]</TD><TD>$cel[3]</TD><TD>$cel[4]</TD><TD>$cel[5]",$rep,urlencode($cel[1]));
		else print("$cel[1]</TD><TD>$cel[2]</TD><TD>$cel[3]</TD><TD>$cel[4]</TD><TD>$cel[5]");
		print("</TD><TD><INPUT TYPE=button VALUE=Del onClick='javascript:parent.frames[\"control\"].location.href=\"connect_control.php?rep=$rep&file=".urlencode($cel[1])."&menu=delete&command=rm\"'>");
	}
	print("</TD><TD><INPUT TYPE=button VALUE=Ren onClick='javascript:parent.frames[\"control\"].location.href=\"connect_control.php?rep=$rep&file=".urlencode($cel[1])."&menu=rename\"'></TD>");
	if (is_editable($cel[1]))
		print("</TD><TD><INPUT TYPE=button VALUE=Get onClick='javascript:parent.frames[\"control\"].location.href=\"connect_get.php?rep=$rep&file=".urlencode($cel[1])."\"'></TD>");
	print("<TR>\n");
}
print("</TABLE>\n");
print("</FORM>\n");
print("</CENTER>\n");

print("<SCRIPT>parent.frames['control'].location.href='connect_control.php?menu=info&rep=$rep'</SCRIPT>");

PiedPage();

?>
