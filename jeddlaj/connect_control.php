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

print("<CENTER><H1>Connexion au serveur Rembo</H1></CENTER>\n");

if (isset($_GET["rep"])) $rep=$_GET["rep"]; else $rep="/";

print("<CENTER>\n");
switch($_GET["menu"]) {
	case "rien" :
		break;
	case "info" :
		print("<I>Vous vous trouvez actuellement dans le répertoire</I> $rep <I>du serveur Rembo</I>\n");
		break;
	case "namedir" :
		print("<FORM METHOD=GET>\n");
		print("Entrez le nom du répertoire : $rep<INPUT TYPE=TEXT SIZE=30 NAME=file>\n");
		print("&nbsp;<INPUT TYPE=SUBMIT VALUE='Créer le répertoire'>\n");
		print("<INPUT TYPE=hidden NAME=rep VALUE=$rep>\n");
		print("<INPUT TYPE=hidden NAME=menu VALUE=mkdir>\n");
		print("</FORM>\n");
		break;
	case "mkdir" :
		$file=$_GET["file"];
		$cmd="./mkdir.expect $netclnt_program $server $passwd $rep $file";
		exec($cmd);
		printf("<SCRIPT>parent.frames['connect'].location.href='connect.php?rep=%s'</SCRIPT>",$rep.urlencode($file)."/");
		break;
	case "rename" :
		$file=$_GET["file"];
		print("<FORM METHOD=GET>\n");
		print("Renommer <I>$file</I> en <INPUT TYPE=TEXT SIZE=30 NAME=newname VALUE=$file>\n");
		print("&nbsp;<INPUT TYPE=SUBMIT VALUE='Renommer'>\n");
		print("<INPUT TYPE=hidden NAME=rep VALUE=$rep>\n");
		print("<INPUT TYPE=hidden NAME=file VALUE=$file>\n");
		print("<INPUT TYPE=hidden NAME=menu VALUE=ren>\n");
		print("</FORM>\n");
		break;
	case "ren" :
		$file=$_GET["file"];
		$newname=$_GET["newname"];
		$cmd="./ren.expect $netclnt_program $server $passwd $rep $file $newname";
		exec($cmd);
		printf("<SCRIPT>parent.frames['connect'].location.href='connect.php?rep=%s'</SCRIPT>",$rep);
		break;
	case "delete" :
		$file=$_GET["file"];
		$command=$_GET["command"];
		if ($command=="deltree") print("Etes-vous sur de vouloir détruitre le répertoire <I>$file</I> ainsi que tous ses sous-répertoires ?\n");
		else print("Etes-vous sur de vouloir détruitre le fichier <I>$file</I> ?\n");
		print("&nbsp;<INPUT TYPE=button VALUE='OUI' onClick='javascript:location.href=\"connect_control.php?rep=$rep&file=".urlencode($file)."&menu=del&command=$command\"'>\n");
		print("&nbsp;<INPUT TYPE=button VALUE='NON' onClick='javascript:location.href=\"connect_control.php?menu=info&rep=$rep\"'>\n");
		break;
	case "del" :
		$file=$_GET["file"];
		$command=$_GET["command"];
		$cmd="./del.expect $netclnt_program $server $passwd $rep $file $command";
		exec($cmd);
		printf("<SCRIPT>parent.frames['connect'].location.href='connect.php?rep=%s'</SCRIPT>",$rep);
		break;
	case "upload" :
		print("<FORM NAME=FORM ENCTYPE='multipart/form-data' METHOD=POST ACTION='connect_control.php?rep=$rep&menu=put'>");
		print("Fichier à déposer : <INPUT TYPE='file' NAME='upfile' SIZE=50>\n");
		print("<INPUT TYPE=submit VALUE='Déposer'>\n"); 
		print("</FORM>\n");
		break;
	case "put" :
		$file="/tmp/".$_FILES["upfile"]["name"];
		move_uploaded_file($_FILES["upfile"]["tmp_name"],$file);
		$cmd="./put.expect $netclnt_program $server $passwd $rep $file";
		exec($cmd);
		unlink($file);
		printf("<SCRIPT>parent.frames['connect'].location.href='connect.php?rep=%s'</SCRIPT>",$rep);
}
print("</CENTER>\n");
print("<P><CENTER><A HREF=\"javascript:parent.location.href='accueil.php'\">Retour</A></CENTER></P>\n");

PiedPage();
?>
