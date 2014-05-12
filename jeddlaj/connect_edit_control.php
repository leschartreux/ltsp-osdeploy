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

function save() {
	global $file;
	$tmpfile=fopen("/tmp/$file","w");
	// si magic_quotes_gpc est à ON dans php.ini il faut retirer les / ajoutés par le POST
	if (get_magic_quotes_gpc()) 	
		fwrite($tmpfile,stripslashes($_POST["source"]));
	else
		fwrite($tmpfile,$_POST["source"]);
	fclose($tmpfile);
}

function putfile() {
	global $file,$rep,$netclnt_program,$server,$passwd;
	if (filesize("/tmp/$file")!=0) {
		$cmd="./put.expect $netclnt_program $server $passwd $rep /tmp/$file";
		exec($cmd);
		print("<I>Sauvegarde du fichier</I> $file <I>effectué dans</I> $rep\n");
	} else print("<I>Impossible de sauvegarder le fichier</I> $file <I>car il est vide !</I>\n");
	unlink("/tmp/$file");
}

print("<CENTER><H1>Connexion au serveur Rembo</H1></CENTER>\n");

$rep=$_POST["rep"];
$file=$_POST["file"];
$menu=$_POST["menu"];

print("<CENTER>\n");
switch($menu) {
	case "info" :
		print("<I>Edition du fichier</I> $file <I></I>\n");
		break;
	case "save_as" :
		save();
		print("<FORM NAME=form METHOD=POST TARGET=control ACTION='connect_edit_control.php'>\n");
		print("Entrez le nom du fichier : $rep<INPUT TYPE=TEXT SIZE=30 NAME=file>\n");
		print("&nbsp;<INPUT TYPE=submit VALUE='Enregistrer'>\n");
		print("<INPUT TYPE=hidden NAME=menu VALUE=move>\n");
		print("<INPUT TYPE=hidden NAME=rep VALUE=$rep>\n");
		print("<INPUT TYPE=hidden NAME=old_file VALUE=$file>\n");
	break;	
	case "save" :
		save();
		putfile();
		break;
	case "move" :
		if ($file!="") {
			rename("/tmp/".$_POST["old_file"],"/tmp/".$file);
			print("<script>parent.frames.connect.document.form.file.value=\"".urlencode($file)."\"</script>\n");
			putfile();
		} else print("<I>Le nom du fichier ne peut être vide !</I>\n");
		break;
}
print("</CENTER>\n");
print("<P><CENTER><A HREF=\"javascript:parent.location.href='accueil.php'\">Retour</A></CENTER></P>\n");

PiedPage();
?>
