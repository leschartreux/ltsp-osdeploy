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


	
function SelectDb($db) {
	mysql_select_db($db);
}

function ConnectMySQL($host, $user, $pwd) {
	mysql_connect($host, $user, $pwd) or die ("Connexion au serveur MySQL impossible\n");
}

function DisconnectMySQL() {
	mysql_close();
}

function pretty_display($result, $extra){
	echo "<TABLE $extra>\n";
	echo "<TR>\n";
	$a = 0;
	$LigneTitre = "";
	for ($i=0;$i<mysql_num_fields($result);$i++){
		if (mysql_field_name($result,$i) == "N_Inventaire"){
			$a++;
			$IndexDuN_Inventaire = $i;
		}
		if (mysql_field_name($result,$i) == "Entite"){
			$a++;
		}
		if (mysql_field_name($result,$i) != "N_Inventaire"){
			$LigneTitre .= "<TH> ".mysql_field_name($result,$i)."</TH>";
		}	
	}
	if ($a == 2) {
		$LigneTitre = "<TH> Numero Inventaire </TH>".$LigneTitre;
	}
	echo $LigneTitre;
	echo "</TR>";
	for ($i=0;$i<mysql_num_rows($result);$i++){
		echo "<TR>\n";
		$line = mysql_fetch_row($result);
		if ($a == 2){
			$Champs = mysql_fetch_field($result,0);
			echo "<TD>".substr($Champs->table, 0, 1)."-$line[1]-$line[0]</TD>\n";
		}
		for ($j=0;$j<mysql_num_fields($result);$j++){
			if ($j != $IndexDuN_Inventaire){
				echo "<TD>$line[$j]</TD>";
			}
		}
		echo "</TR>\n";
	}
	echo "</TABLE>\n";
}

function EcritLigneTitre($result){
        echo "<TR>\n";
        $LigneTitre = "";
        for ($i=0;$i<mysql_num_fields($result);$i++)
	{
        	$LigneTitre .= "<TH> ".mysql_field_name($result,$i)."</TH>";
        }
        echo $LigneTitre;
        echo "</TR>";
}

function AfficheResultatSelectEnTableauHTML($result, $extra){
	// On ecrit les noms de champs en haut du tableau
        echo "<TABLE $extra>\n";
	EcritLigneTitre($result);
        echo "<TR>\n";
        for ($i=0;$i<mysql_num_rows($result);$i++){
                echo "<TR>\n";
                $line = mysql_fetch_row($result);
                for ($j=0;$j<mysql_num_fields($result);$j++){
			echo "<TD>$line[$j]</TD>";
                }
                echo "</TR>\n";
        }
	echo "</TABLE>\n";
}

?>
