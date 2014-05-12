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
include("ExpectDefs.php");
entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Connexion au serveur Rembo");

$server=$GLOBALS['rembo_server'];
$passwd=$GLOBALS['rembo_passwd'];

$rep=$_GET["rep"];
$file=$_GET["file"];
$new=$_GET["new"];

print("<CENTER><FORM NAME=form METHOD=POST TARGET=control ACTION=connect_edit_control.php>\n");
print("<INPUT TYPE=hidden name=rep value=\"$rep\">\n");
print("<INPUT TYPE=hidden name=file value=\"".urlencode($file)."\">\n");
print("<INPUT TYPE=hidden name=menu value=info>\n");
print("<TABLE WIDTH=\"40%\">\n");
print("<TR><TD align=left><INPUT TYPE=button VALUE='Enregistrer' onClick='document.form.menu.value=\"save\";document.form.submit();'></TD>\n");
print("<TD align=left><INPUT TYPE=button VALUE='Enregistrer sous' onClick='document.form.menu.value=\"save_as\";document.form.submit();'></TD>\n");
print("<TD align=right><INPUT TYPE=button VALUE='Fermer' onClick='javascript:location.href=\"connect.php?rep=$rep\"'></TD></TR>\n");
print("</TABLE>\n");
if ($new=="false") {
	$cmd="./get.expect $netclnt_program $server $passwd $rep $file";
	exec($cmd);
	$ligne=file("/tmp/".$file);
	print("<TEXTAREA NAME=source WRAP=OFF COLS=80 ROWS=15>\n");
	for ($i=0;$i<count($ligne);$i++) print($ligne[$i]);
	print("</TEXTAREA>\n");
	unlink("/tmp/".$file);
} else print("<TEXTAREA NAME=source WRAP=OFF COLS=80 ROWS=15></TEXTAREA>\n");
print("</FORM></CENTER>\n");

print("<SCRIPT>document.form.submit();</SCRIPT>");

?>
