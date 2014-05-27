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


######################### CONFIG ZONE ###########################

$rep_pyddlaj = "/usr/share/pyddlaj";
$tftp_root = "/srv/tftp/ltsp/i386-osdeploy";

######################### END CONFIG ZONE ###########################

# Quelques fonctions Pyddlaj

#Supprime le fichier de boot local pour forcer le boot sur l'OS dédié
function supprime_boot_local($nom_dns) {
	//global $mon_ip;
	global $tftp_root;
	$request = "SELECT adresse_mac FROM ordinateurs WHERE nom_dns=\"$nom_dns\""; 
	$result= mysql_query($request);
	if (mysql_num_rows($result) > 0 ) # L'ordianteur a été trouvé on va supprimer le fichier concerné
	{
		$line = mysql_fetch_array($result);
		$mac = $line["adresse_mac"];
		mysql_free_result($result);
		#genere le fichier de conf PXE à supprimer (01-Adresse mac)
		$rep_pxe = $tftp_root . "/pxelinux.cfg/";
		$macfile = "01-" . str_replace(":","-",$mac);
		
		if ( file_exists ( $rep_pxe . $macfile))
		{
			if ( unlink($rep_pxe . $macfile) )
				print "<P>Le fichier de boot local $rep_pxe $macfile a été supprimé</P>";
			else
				print "<P>Impossible de supprimer le fichier $rep_pxe $macfile</P>";
		}
		else
			print "<P>Ce poste boote déjà sur Pyddlaj</P>";
		
	}
	else # l'ordinateur n'est pas verrouillé
	{
		mysql_free_result($result);
	}
}


?>
