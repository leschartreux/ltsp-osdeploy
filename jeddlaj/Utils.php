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

# Si $debug est a yes, tous les messages de debug issus des fonctions Debug et DebugTab s'affichent.
$debug = "no";
	
# Printe $texte si $debug = yes
function PrintDebug($texte){
	global $debug;
	if ($debug == "yes")
	{
		print($texte);
	}
}

# Prend en entr�e un nom de variable, i.e. sans le dollar devant, et 
# �crit une ligne de DEBUG avec nom et valeur de la variable.
# NE MARCHE PAS POUR tab[$i], ou count($checked), mais seulement pour $var
function Debug($nomvar) {
	global $debug;
	$str = "$"."GLOBALS['".$nomvar."'];";
	if ($debug == "yes")
	{
		eval("\$valeurvar = $str;");
		print("DEBUG : $nomvar = $valeurvar<BR>\n");
	}
}

# M�me id�e mais pour des variable de type $tab[$i], etc.
# Probl�me : preg_replace ne fait rien, inexplicablement... Donc la fonction
# ne marche pas...
function Debug2($str) {
	global $debug;
	print("str = $str");
	#preg_replace("/\$([a-z]*)/","\$GLOBALS['\1']",$str);
	print("str = $str");
	eval("\$valeurvar = $str;");
	print("DEBUG : $nomvar = $valeurvar<BR>\n");
}

# Prend en entr�e un nom de variable DE TABLEAU, sans le dollar devant, et 
# �crit une ligne de DEBUG avec nom et valeur de la variable.
# NE MARCHE PAS POUR tab[$i], ou count($checked), mais seulement pour $var
function DebugTab($nomvartab) {
	global $debug;
	$str = "$"."GLOBALS['".$nomvartab."'];";
	if ($debug == "yes")
	{
		eval("\$valeurvar = $str;");
		print("DEBUGTAB : $nomvartab<BR>\n");
		print_r($valeurvar);
		print("<BR>");
	}
}

?>
