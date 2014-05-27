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

$etat = $_POST["etat"];
$id_logiciel = $_POST["id_logiciel"];
$id_os = $_POST["id_os"];

include("UtilsHTML.php");
include("UtilsMySQL.php");

entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Configuration Logicielle - Etape 3");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<CENTER><H1>Configuration Logicielle - Etape 3</H1></CENTER>\n");

$mon_ip=getenv('REMOTE_ADDR');

$request="SELECT * FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\" AND NOW()-timestamp<=500";
$result=mysql_query($request);
$ordi=Array();
for ($i=0;$i<mysql_num_rows($result);$i++) {
  $ordi[$i]=mysql_fetch_array($result);
	}
mysql_free_result($result);

if (count($ordi) == 0) {
  print("<CENTER>La sélection a expiré.<BR><CENTER>\n");
}
else {
  for ($i=0;$i<count($id_logiciel);$i++) {
  	switch($etat[$i]) {
  		case "comme_actuel" :
			  # Tout ce qui était a_supprimer reste installé
  			$request="UPDATE package_est_installe_sur AS a, packages AS b, logiciels AS c,ordinateurs_en_consultation AS d SET etat_package=\"installe\" WHERE c.id_logiciel=\"".$id_logiciel[$i]."\" AND c.id_logiciel=b.id_logiciel AND b.id_package=a.id_package AND etat_package=\"a_supprimer\" AND ip_distante=\"$mon_ip\" AND a.nom_dns=d.nom_dns AND a.num_disque=d.num_disque AND a.num_partition=d.num_partition"; 
  			mysql_query($request);
				# Tout ce qui était a_jouter disparait
  			$request="DELETE package_est_installe_sur FROM package_est_installe_sur, packages AS b, logiciels AS c,ordinateurs_en_consultation AS d WHERE c.id_logiciel=\"".$id_logiciel[$i]."\" AND c.id_logiciel=b.id_logiciel AND b.id_package= package_est_installe_sur.id_package AND etat_package=\"a_ajouter\" AND ip_distante=\"$mon_ip\" AND  package_est_installe_sur.nom_dns=d.nom_dns AND package_est_installe_sur.num_disque=d.num_disque AND package_est_installe_sur.num_partition=d.num_partition"; 
				mysql_query($request);
  			break;
  		case "supprimer_partout" :
			  # Tout ce qui était installé devient à a_supprimer
  			$request="UPDATE package_est_installe_sur AS a, packages AS b, logiciels AS c,ordinateurs_en_consultation AS d SET etat_package=\"a_supprimer\" WHERE c.id_logiciel=\"".$id_logiciel[$i]."\" AND c.id_logiciel=b.id_logiciel AND b.id_package=a.id_package AND etat_package=\"installe\" AND ip_distante=\"$mon_ip\" AND a.nom_dns=d.nom_dns AND a.num_disque=d.num_disque AND a.num_partition=d.num_partition"; 
  			mysql_query($request);
				# Tout ce qui était a_ajouter disparait
  			$request="DELETE package_est_installe_sur FROM package_est_installe_sur, packages AS b, logiciels AS c,ordinateurs_en_consultation AS d WHERE c.id_logiciel=\"".$id_logiciel[$i]."\" AND c.id_logiciel=b.id_logiciel AND b.id_package=package_est_installe_sur.id_package AND etat_package=\"a_ajouter\" AND ip_distante=\"$mon_ip\" AND package_est_installe_sur.nom_dns=d.nom_dns AND package_est_installe_sur.num_disque=d.num_disque AND package_est_installe_sur.num_partition=d.num_partition"; 
				mysql_query($request);
  			break;
  		case "installer_partout" :
				# Tout ce qui était à a_supprimer reste installé
  			$request="UPDATE package_est_installe_sur AS a, packages AS b, logiciels AS c,ordinateurs_en_consultation AS d SET etat_package=\"installe\" WHERE c.id_logiciel=\"".$id_logiciel[$i]."\" AND c.id_logiciel=b.id_logiciel AND b.id_package=a.id_package AND etat_package=\"a_supprimer\" AND ip_distante=\"$mon_ip\" AND a.nom_dns=d.nom_dns AND a.num_disque=d.num_disque AND a.num_partition=d.num_partition"; 
  			mysql_query($request);
  			# Ici on fait on se sert de l'unicité des entrées et de IGNORE pour ne pas interrompre les insertions, parce que MySQL c'est naze : 
				# il faudrait une sous-requête pour faire nom_dns NOT IN ordinateurs_déjà_présents
  	 		$request="INSERT IGNORE INTO package_est_installe_sur (nom_dns,etat_package,id_package,num_disque,num_partition) SELECT c.nom_dns,\"a_ajouter\",id_package,num_disque,num_partition FROM logiciels AS a,packages AS b, ordinateurs AS c, composant_est_installe_sur AS d, ordinateurs_en_consultation AS e WHERE ip_distante=\"$mon_ip\" AND c.nom_dns=d.nom_dns AND d.nom_dns=e.nom_dns AND a.id_logiciel=\"".$id_logiciel[$i]."\" AND a.id_logiciel=b.id_logiciel AND  (specificite=\"aucune\" OR ( specificite=\"nom_dns\" AND valeur_specificite=c.nom_dns) OR ( specificite=\"signature\" AND valeur_specificite=c.signature) OR valeur_specificite=d.id_composant) GROUP BY nom_dns";
  			mysql_query($request);
  			break;
  	}
  }

  for ($i=0;$i<count($ordi);$i++) {
		# Etat de l'OS : instancié ? ( Sachant que d'après la page1 il ne peut y avoir un autre os )
  	$request="SELECT etat_idb FROM idb_est_installe_sur  WHERE nom_dns=\"".$ordi[$i]["nom_dns"]."\" AND num_disque=\"".$ordi[$i]["num_disque"]."\" AND num_partition=\"".$ordi[$i]["num_partition"]."\"";
   	$result = mysql_query($request);
		# Etat non instancié : on positionne l'OS en état a_ajouter
		if ( mysql_num_rows($result)==0 ) {
 	 		$request="INSERT INTO idb_est_installe_sur (nom_dns,etat_idb,id_idb,num_disque,num_partition) SELECT nom_dns,\"a_ajouter\",id_idb,\"".$ordi[$i]["num_disque"]."\",\"".$ordi[$i]["num_partition"]."\" FROM images_de_base , ordinateurs WHERE id_os=\"$id_os\" AND nom_dns=\"".$ordi[$i]["nom_dns"]."\" AND (specificite=\"aucune\" OR ( specificite=\"nom_dns\" AND valeur_specificite=nom_dns ) OR ( specificite=\"signature\" AND valeur_specificite=signature))";
  		mysql_query($request);
		} else {
    	$line = mysql_fetch_array($result);
    	$etat_idb=$line["etat_idb"] ;
    	mysql_free_result($result);
			if ( $etat_idb!="a_ajouter" && $etat_idb!="a_synchroniser" ) {
				# On compte le nombre de logiciels à supprimer sur l'ordinateur pour la partition donnée
  			$request="SELECT COUNT(*) AS total FROM package_est_installe_sur WHERE nom_dns=\"".$ordi[$i]["nom_dns"]."\" AND num_disque=\"".$ordi[$i]["num_disque"]."\" AND num_partition=\"".$ordi[$i]["num_partition"]."\" AND etat_package=\"a_supprimer\"";
    		$result = mysql_query($request);
    		$line = mysql_fetch_array($result);
    		$nb_a_supprimer = $line["total"] ;
    		mysql_free_result($result);
				# On compte le nombre de logiciels à ajouter sur l'ordinateur la partition donnée
  			$request="SELECT COUNT(*) AS total FROM package_est_installe_sur WHERE nom_dns=\"".$ordi[$i]["nom_dns"]."\" AND num_disque=\"".$ordi[$i]["num_disque"]."\" AND num_partition=\"".$ordi[$i]["num_partition"]."\" AND etat_package=\"a_ajouter\"";
    		$result = mysql_query($request);
    		$line = mysql_fetch_array($result);
    		$nb_a_ajouter = $line["total"] ;
    		mysql_free_result($result);
				# Si a_supprimer+a_ajouter > 0 c'est qu'il y a eu une modification de faite sur l'ordinateur
  			if ( $nb_a_supprimer+$nb_a_ajouter > 0 && $etat_idb=="installe") {
  		  	$request="UPDATE idb_est_installe_sur SET etat_idb=\"modif_softs\" WHERE nom_dns=\"".$ordi[$i]["nom_dns"]."\" AND num_disque=\"".$ordi[$i]["num_disque"]."\" AND num_partition=\"".$ordi[$i]["num_partition"]."\"";
  		  	mysql_query($request);
				# Sinon, il n'y a pas eu de changements ( ou annulation des changements précédents )
  			} else if ( $nb_a_supprimer+$nb_a_ajouter==0 && $etat_idb=="modif_softs" ) { 
  		  	$request="UPDATE idb_est_installe_sur SET etat_idb=\"installe\" WHERE nom_dns=\"".$ordi[$i]["nom_dns"]."\" AND num_disque=\"".$ordi[$i]["num_disque"]."\" AND num_partition=\"".$ordi[$i]["num_partition"]."\"";
  		  	mysql_query($request);
				}
			}
		}
		# Y a t il une modification sur l'ordinateur ?
		$request="SELECT COUNT(*) AS total FROM idb_est_installe_sur WHERE etat_idb!=\"installe\" AND nom_dns=\"".$ordi[$i]["nom_dns"]."\"";
		$result=mysql_query($request);
    $line=mysql_fetch_array($result);
		# Pas de modification
    if ($line["total"]==0) {
  		$request2="UPDATE ordinateurs SET etat_install=\"installe\" WHERE nom_dns=\"".$ordi[$i]["nom_dns"]."\" AND etat_install!=\"installe\"";
  		mysql_query($request2);
		}
		# Modification : ordinateur en etat modifie
		else {
			$request2="UPDATE ordinateurs SET etat_install=\"modifie\" WHERE nom_dns=\"".$ordi[$i]["nom_dns"]."\" AND etat_install!=\"modifie\"";
			mysql_query($request2);
		}
    mysql_free_result($result);
  }
  print ("<P><I>Mofications des configurations logicielles insérées dans la base. <FONT COLOR=RED>ATTENTION :</FONT> Elles ne seront effectives sur les ordinateurs concernés qu'après leur reboot...</I></P>\n");
}  
print("<BR><HR><P><CENTER><A HREF=accueil.php>Retour</A></CENTER></P>\n");

$request="DELETE FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\""; 
mysql_query($request);

DisconnectMySQL();

PiedPage();

?>
