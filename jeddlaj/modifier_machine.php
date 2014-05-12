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



# On recupere les variables
if(isset($_POST["nom_dns"])){$nom_dns = $_POST["nom_dns"];}
if (!isset($nom_dns)) { $nom_dns = $_GET["nom_dns"]; }
# toutes les variables ont ete recuperees

include("UtilsHTML.php");
include("UtilsMySQL.php");
include("UtilsJeDDLaJ.php");

# Main()
entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Machine $nom_dns");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<CENTER><H1>$nom_dns</H1></CENTER>\n");

if (est_verrouille_pour_mon_ip($nom_dns)) # Cas ou on revient depuis modifier_machine[123].php (on a déjà fait une modif sur la machine nom_dns)
{
	# On resette la duree de verrouillage 
	deverrouille_pour_mon_ip($nom_dns);
	verrouille_pour_mon_ip($nom_dns);
}
else # Cas où on arrive de choix_machines_multiples (on n'a pas encore fait de modif sur la machine nom_dns)
{
	# On verrouille la machine en cours de modif.
	verrouille_pour_mon_ip($nom_dns);
}

############ La renommade...

print("<H2>Nom DNS</H2>\n");

print("<BR><A HREF=\"modifier_machine_0.php?nom_dns=$nom_dns\">Renommer $nom_dns</A>\n");

############ Fin renommade...
############ Infos générales

# On attaque la tables Ordinateurs pour les infos générales

#$request = "SELECT A.nom_netbios, A.numero_serie, A.ram, A.adresse_mac, A.adresse_ip, A.affiliation_windows, A.nom_affiliation, B.num_disque, B.capacite FROM ordinateurs AS A, stockages_de_masse AS B where A.nom_dns=\"$nom_dns\" AND B.nom_dns=\"$nom_dns\" AND B.type=\"disque dur\"";
$request1 = "SELECT nom_netbios, numero_serie, ram, adresse_mac, adresse_ip, netmask, affiliation_windows,nom_affiliation,ou,poweroff,hres,vres,hfreq,vfreq,bpp,modeline FROM ordinateurs WHERE nom_dns=\"$nom_dns\"";
$request2 = "SELECT num_disque, capacite, connectique, linux_device FROM stockages_de_masse where nom_dns=\"$nom_dns\" AND type=\"disque dur\"";

$result1 = mysql_query($request1);
$result2 = mysql_query($request2);
print("<H2>Général</H2>\n");
AfficheResultatSelectEnTableauHTML($result1, "BORDER=1 CELLPADDING=2 CELLSPACING=1");
mysql_free_result($result1);
print("<BR>\n");
AfficheResultatSelectEnTableauHTML($result2, "BORDER=1 CELLPADDING=2 CELLSPACING=1");
mysql_free_result($result2);

print("<BR><A HREF=\"modifier_machine_1.php?nom_dns=$nom_dns\">Modifier</A>\n");

############ Fin Infos Générales

############ Infos de partitionnement

# On attaque la tables Partitions pour les infos partitions

# On determine d'abord le nombre de disques
$request="SELECT COUNT(*) AS total FROM stockages_de_masse WHERE nom_dns=\"$nom_dns\" AND type=\"disque dur\"";

$result = mysql_query($request);
$ligne = mysql_fetch_array($result);
$nb_disques = $ligne["total"];
mysql_free_result($result);

for($i=0;$i<$nb_disques;$i++)
{
	$request = "SELECT  num_disque, num_partition, taille_partition, type_partition, nom_partition, systeme, linux_device FROM partitions WHERE nom_dns=\"$nom_dns\" AND num_disque=\"$i\" ORDER BY num_partition";
	$result = mysql_query($request);
	
	print("<BR><H2>Partitionnement disque $i</H2>\n");
	AfficheResultatSelectEnTableauHTML($result, "BORDER=1 CELLPADDING=2 CELLSPACING=1");
	print("<BR><A HREF=\"modifier_machine_2.php?nom_dns=$nom_dns&num_disque=$i\">Modifier</A>\n");
	
	mysql_free_result($result);
}


############ FIN Infos de partitionnement

############ Infos groupe

# On attaque la tables ord_appartient_a_gpe pour les infos partitions

$request = "SELECT  nom_groupe FROM ord_appartient_a_gpe WHERE nom_dns=\"$nom_dns\"";
$result = mysql_query($request);

print("<BR><H2>Groupes</H2>\n");
AfficheResultatSelectEnTableauHTML($result, "BORDER=1 CELLPADDING=2 CELLSPACING=1");
print("<BR><A HREF=\"modifier_machine_3.php?nom_dns=$nom_dns\">Modifier</A>\n");

mysql_free_result($result);


############ FIN Infos de partitionnement

print("<BR><BR><HR><P><CENTER><A HREF=accueil.php>Retour</A></CENTER></P>\n");

DisconnectMySQL();


PiedPage();
//FIN Main()

?>
