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

# 2-3 initialisations
$duree_verrouillage = 500;
$mon_ip=getenv('REMOTE_ADDR');

# Les répertoires de stockage dans l'arborescence du serveur REMBO
$RemboIDBDir = "&lt;REMBO_DIR sur serveur REMBO&gt;/global/hdimages/";
$RemboPackagesDir = "&lt;REMBO_DIR sur serveur REMBO&gt;/global/incrementals/";
$RemboPostInstScriptsDir = "&lt;REMBO_DIR sur serveur REMBO&gt;/global/postinstall/";
$RemboPreDeinstScriptsDir = "&lt;REMBO_DIR sur serveur REMBO&gt;/global/predeinstall/";

# Pour les défauts...
$default_group_photo = "classroom.jpg";
$default_group_description = "Yet Another JeDDLaJ's Group";

# Pour un formatage standard des icones lors de l'affichage HTML (width et height balise IMG)
$largeur_image_distrib_et_idb = "100";
$hauteur_image_distrib_et_idb = "100";
$largeur_image_logiciel_et_package = "40";
$hauteur_image_logiciel_et_package = "40";

# Quelques listes
$oss = array("Windows95", "Windows98", "WindowsME", "WindowsNT", "Windows2000", "WindowsXP", "Windows2003","WindowsVista", "WindowsVista_x64", "Windows7", "Windows7_x64", "Windows2008", "Windows2008_x64", "Linux", "Linux_x64"); 
$resolutions = array("640x480", "720x576", "800x600", "1024x768", "1152x864", "1280x1024", "1440x900", "1600x900", "1600x1200", "1680x1050", "1920x1200", "2048x1536", "2560x1920");
$bpps = array(1, 2, 3, 4, 5, 6, 8, 12, 15, 16, 24, 32, 48);
$nom_long_etat=array("en_cours"=>"en cours d'installation", "modifie"=>"modifié", "package"=>"création de package", "idb"=>"création d'image de base", "depannage"=>"dépannage");

$tab_maj= array ("1.0"=>"modifdb_jeddlaj_v1.0_vers_v1.4IBE.sql","1.4IBE"=>"modifdb_jeddlaj_v1.4IBE_vers_v1.6IBE+.sql","1.6IBE+"=>"modifdb_jeddlaj_v1.6IBE+_vers_v1.8IBT+.sql", "1.8"=>"modifdb_jeddlaj_v1.8IBT+_vers_v1.8.1IBT+.sql");

######################### END CONFIG ZONE ###########################

# Quelques fonctions JeDDLaJiennes

# Renvoie un array(elem1, elem2).
# Si verrouille elem1 = 1, elem2 = ip_verrouillante
# Si pas verrouille elem1 = elem2 = 0
function est_verrouille_pour_une_autre_IP($nom_dns) {
	global $mon_ip;
	$request = "SELECT timestamp,ip_distante FROM ordinateurs_en_consultation WHERE nom_dns=\"$nom_dns\" AND NOW()-timestamp<=500 AND ip_distante<>\"$mon_ip\""; 
	$result= mysql_query($request);
	if (mysql_num_rows($result) > 0 ) # Si l'ordinateur est en consultation pour une autre IP et depuis moins de 5 minutes
	{
		$line = mysql_fetch_array($result);
		$ip_verrouillante = $line["ip_distante"];
		mysql_free_result($result);
		return array (1, $ip_verrouillante);
	}
	else # l'ordinateur n'est pas verrouillé
	{
		mysql_free_result($result);
		return array (0, 0);
	}
}


# Renvoie 1 si verrouille pour mon IP, 0 sinon
function est_verrouille_pour_mon_ip($nom_dns) {
	global $mon_ip;
	$request = "SELECT * FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\" AND NOW()-timestamp<=500"; 
	$result= mysql_query($request);
	if (mysql_num_rows($result) == 0 ) # l'ordinateur n'est plus verrouille pour moi
	{
		mysql_free_result($result);
		return 0;
	}
	else # l'ordinateur est verrouillé pour mon IP
	{
		mysql_free_result($result);
		return 1;
	}
}

# renvoie 1 si etat install = installe, 0 sinon
function est_installe($nom_dns) {
	$request = "select etat_install from ordinateurs where nom_dns=\"$nom_dns\" and etat_install=\"installe\""; 
	$result = mysql_query($request);
	if (mysql_num_rows($result) > 0 ) # si l'ordinateur est installe...
	{
		mysql_free_result($result);
		return 1;
	}
	else
	{
		mysql_free_result($result);
		return 0;
	}
}

# renvoie 1 si etat install = modifie, 0 sinon
function est_modifie($nom_dns) {
	$request = "select etat_install from ordinateurs where nom_dns=\"$nom_dns\" and etat_install=\"modifie\""; 
	$result = mysql_query($request);
	if (mysql_num_rows($result) > 0 ) # si l'ordinateur est modifie...
	{
		mysql_free_result($result);
		return 1;
	}
	else
	{
		mysql_free_result($result);
		return 0;
	}
}

# Renvoie un tableau (1, $etat_install) si la machine est dans un etat_install 
# qui la rend indisponible pour toute action hors creation/modification de 
# groupe (i.e. on peut la selectionner pour l'ajouter ou la supprimer d'un 
# groupe...), (0, 0) sinon
function est_dans_un_etat_install_bloquant($nom_dns) {
#	$request = "SELECT etat_install FROM ordinateurs WHERE nom_dns=\"$nom_dns\" AND (etat_install=\"en_cours\" OR etat_install=\"modifie\" OR etat_install=\"package\" OR etat_install=\"idb\" OR etat_install=\"depannage\")"; 
	$request = "SELECT etat_install FROM ordinateurs WHERE nom_dns=\"$nom_dns\" AND (etat_install=\"en_cours\" OR etat_install=\"package\" OR etat_install=\"idb\" OR etat_install=\"depannage\")"; 
	$result = mysql_query($request);
	$line = mysql_fetch_array($result);
	$etat_install = $line["etat_install"];
	if (mysql_num_rows($result) > 0 )
	{
		mysql_free_result($result);
		return array(1, $etat_install);
	}
	else
	{
		mysql_free_result($result);
		return array (0, 0);
	}
}

# renvoie 1 si etat install = en_cours, 0 sinon
function est_en_cours_d_install($nom_dns) {
	$request = "select etat_install from ordinateurs where nom_dns=\"$nom_dns\" and etat_install=\"en_cours\""; 
	$result = mysql_query($request);
	if (mysql_num_rows($result) > 0 ) # si l'ordinateur est en cours d'install...
	{
		mysql_free_result($result);
		return 1;
	}
	else
	{
		mysql_free_result($result);
		return 0;
	}
}

# Renvoie 1 si etat_install = package, 0 sinon
function est_en_etat_package($nom_dns) {
	$request = "SELECT etat_install FROM ordinateurs WHERE nom_dns=\"$nom_dns\" AND etat_install=\"package\""; 
	$result = mysql_query($request);
	if (mysql_num_rows($result) > 0 )
	{
		mysql_free_result($result);
		return 1;
	}
	else
	{
		mysql_free_result($result);
		return 0;
	}
}

# Renvoie 1 si etat_install = idb, 0 sinon
function est_en_etat_idb($nom_dns) {
	$request = "SELECT etat_install FROM ordinateurs WHERE nom_dns=\"$nom_dns\" AND etat_install=\"idb\""; 
	$result = mysql_query($request);
	if (mysql_num_rows($result) > 0 )
	{
		mysql_free_result($result);
		return 1;
	}
	else
	{
		mysql_free_result($result);
		return 0;
	}
}

# Renvoie 1 si etat_install = depannage, 0 sinon
function est_en_etat_depannage($nom_dns) {
	$request = "SELECT etat_install FROM ordinateurs WHERE nom_dns=\"$nom_dns\" AND etat_install=\"depannage\""; 
	$result = mysql_query($request);
	if (mysql_num_rows($result) > 0 )
	{
		mysql_free_result($result);
		return 1;
	}
	else
	{
		mysql_free_result($result);
		return 0;
	}
}

# Verouille la machine pour mon ip
function verrouille_pour_mon_ip($nom_dns) {
	global $mon_ip;
	$request="INSERT INTO ordinateurs_en_consultation (nom_dns,ip_distante,timestamp) VALUES(\"$nom_dns\",\"$mon_ip\",NOW())";
	mysql_query($request);
}

# Deverrouille la machine pour mon ip
function deverrouille_pour_mon_ip($nom_dns) {
	global $mon_ip;
	$request = "DELETE FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\" AND nom_dns=\"$nom_dns\"";
	mysql_query($request);
}

function fait_peter_les_vieux_verrouillages() {
	global $duree_verrouillage;
	$request = "DELETE FROM ordinateurs_en_consultation WHERE NOW()-timestamp>$duree_verrouillage";
	mysql_query($request);
}


# Une fonction pour vérifier que toutes les machines d'un groupe sont en état installé
# condition sine qua non pour pouvoir le proposer au choix de modification/suppression
function groupe_selectionnable_pour_suppression_ou_modification($nom_groupe)
{
	$request = "SELECT nom_dns FROM ord_appartient_a_gpe WHERE nom_groupe=\"$nom_groupe\"";
	$result=mysql_query($request);
	$selectionnable=1;
	while ($ligne = mysql_fetch_array($result))
	{
		#$selectionnable = est_installe($ligne['nom_dns']) OR est_modifie($ligne['nom_dns']);
		$selectionnable = est_modifie($ligne['nom_dns']) || est_installe($ligne['nom_dns']);
		if (!$selectionnable) {break;}
	}	
	return($selectionnable);
}


# Une fonction pour trouver les groupes inclus dans le groupe en argument
# Rend le tableau $groupes_inclus avec les groupes inclus dans $nom_groupe
function groupes_incluant($nom_groupe)
{
	$groupes_incluant = array();
	$request = "SELECT nom_groupe FROM gpe_est_inclus_dans_gpe WHERE nom_groupe_inclus=\"$nom_groupe\"";
	$result=mysql_query($request);
	if (mysql_affected_rows() == 0)
	{
		mysql_free_result($result);
		return($groupes_incluant);
	}
	else
	{
		while ($ligne = mysql_fetch_array($result)) 
		{
			# On ne met qu'une seule fois un groupe dans le tableau groupes_incluant...
			# ...et on ne relance évidemment la fonction que si le groupe n'est pas
			# déjà dans le tableau sinon ses groupes incluant ont déjà été trouvés et
			# insérés dans le tableau...
			if (!in_array($ligne['nom_groupe'], $groupes_incluant))
			{
				$groupes_incluant[] = $ligne['nom_groupe'];
				$groupes_incluant = array_merge($groupes_incluant, groupes_incluant($ligne['nom_groupe']));
			}
		}
		# Le dépilage...
		mysql_free_result($result);
		return($groupes_incluant);
	}
}

# Une fonction pour trouver les groupes inclus dans le groupe en argument
# Rend le tableau $groupes_inclus avec les groupes inclus dans $nom_groupe
function groupes_inclus($nom_groupe)
{
	$groupes_inclus = array();
	#print("<BR>");
	$request = "SELECT nom_groupe_inclus FROM gpe_est_inclus_dans_gpe WHERE nom_groupe=\"$nom_groupe\"";
	$result=mysql_query($request);
	if (mysql_affected_rows() == 0)
	{
		mysql_free_result($result);
		return($groupes_inclus);
	}
	else
	{
		while ($ligne = mysql_fetch_array($result)) 
		{
			# On ne met qu'une seule fois un groupe dans le tableau groupes_inclus...
			# ...et on ne relance évidemment la fonction que si le groupe n'est pas
			# déjà dans le tableau sinon ses groupes inclus ont déjà été trouvés et
			# insérés dans le tableau...
			# DEBUG
			#print("FONCTION GPES_INCLUS : <BR>");
			#print("FONCTION GPES_INCLUS : ligne[nom_groupe_inclus] = ".$ligne['nom_groupe_inclus']."<BR>\n");
			#print("FONCTION GPES_INCLUS : les groupe_inclus de $nom_groupe<BR>\n");
			#print_r($groupes_inclus);
			#print("<BR>FONCTION GPES_INCLUS : <BR>");
			# END DEBUG
			if (!in_array($ligne['nom_groupe_inclus'], $groupes_inclus))
			{
				$groupes_inclus[] = $ligne['nom_groupe_inclus'];
				$groupes_inclus = array_merge($groupes_inclus, groupes_inclus($ligne['nom_groupe_inclus']));
			}
		}
		# Le dépilage...
		# DEBUG
		#print("FONCTION GPES_INCLUS : les groupe_inclus de $nom_groupe après récursion\n");
		#print_r($groupes_inclus);
		#print("<BR>FONCTION GPES_INCLUS : <BR>");
		# END DEBUG
		mysql_free_result($result);
		return($groupes_inclus);
	}
}

# Une fonction pour trouver les groupes inclus direct (i.e. sans transitivité) dans le groupe en argument
# Rend le tableau $groupes_inclus_direct avec les groupes inclus dans $nom_groupe
function groupes_inclus_direct($nom_groupe)
{
	$groupes_inclus_direct = array();
	$request = "SELECT nom_groupe_inclus FROM gpe_est_inclus_dans_gpe WHERE nom_groupe=\"$nom_groupe\"";
	$result=mysql_query($request);
	if (mysql_affected_rows() == 0)
	{
		mysql_free_result($result);
	}
	else
	{
		while ($ligne = mysql_fetch_array($result)) 
		{
			$groupes_inclus_direct[] = $ligne['nom_groupe_inclus'];
		}
	}
	return($groupes_inclus_direct);
}

# Une fonction pour trouver les groupes incluant direct (i.e. sans transitivité) dans le groupe en argument
# Rend le tableau $groupes_incluant_direct avec les groupes incluant de $nom_groupe
function groupes_incluant_direct($nom_groupe)
{
	$groupes_incluant_direct = array();
	$request = "SELECT nom_groupe FROM gpe_est_inclus_dans_gpe WHERE nom_groupe_inclus=\"$nom_groupe\"";
	$result=mysql_query($request);
	if (mysql_affected_rows() == 0)
	{
		mysql_free_result($result);
	}
	else
	{
		while ($ligne = mysql_fetch_array($result)) 
		{
			print("Dans groupes_incluant_direct : nom groupe : ". $nom_groupe."<BR>");
			print("Dans groupes_incluant_direct :ligne['nom_groupe'] ". $ligne['nom_groupe']."<BR>");
			$groupes_incluant_direct[] = $ligne['nom_groupe'];
		}
	}
	return($groupes_incluant_direct);
}

# Supprime dans la table gpe_est_inclus_dans_gpe l'inclusion directe de $groupe_inclus dans $groupe_incluant, si elle existe.
function supprime_inclusion($groupe_inclus, $groupe_incluant)
{
	mysql_query("DELETE FROM gpe_est_inclus_dans_gpe WHERE nom_groupe = \"$groupe_incluant\" AND nom_groupe_inclus = \"$groupe_inclus\"");
}

# Une fonction qui va supprimer l'inclusion transitive entre groupe_incluant et $groupe_inclus
# i.e. si on a $groupe_inclus inclus dans B inclus dans C inclus dans $groupe_incluant
# on va supprimer $groupe_inclus inclus dans B : on n'aura plus alors l'inclusion de $groupe_inclus dans $groupe_incluant
function coupe_lien_inclusion_transitif($groupe_inclus, $groupe_incluant)
{
	foreach(groupes_incluant_direct($groupe_inclus) as $g_inc_direct)
	{
		Debug("g_inc_direct");
		if ($g_inc_direct == $groupe_incluant)
		{
			supprime_inclusion($groupe_inclus, $groupe_incluant);
		}
		else
		{
			if (in_array($groupe_incluant, groupes_incluant($g_inc_direct)))
			{
				supprime_inclusion($groupe_inclus, $g_inc_direct);
			}
		}
	}
}


# Une fonction qui va ajouter dans le groupe $groupe_a_nourrir les ordinateurs de $groupe_aliment
function feed_group($groupe_a_nourrir, $groupe_aliment)
{
	$request = "SELECT nom_dns FROM ord_appartient_a_gpe WHERE nom_groupe = \"$groupe_aliment\"";
	$result = mysql_query($request);
	while ($ligne = mysql_fetch_array($result)) 
	{
		$values[] = "(\"$ligne[nom_dns]\",\"$groupe_a_nourrir\")";
	}
	$values_string = implode(",", $values);
	mysql_query("INSERT IGNORE INTO ord_appartient_a_gpe (nom_dns, nom_groupe) VALUES $values_string");
	PrintDebug("INSERT IGNORE INTO ord_appartient_a_gpe (nom_dns, nom_groupe) VALUES $values_string");
}

# Une fonction qui va supprimer du groupe $groupe_a_reduire les ordinateurs de $groupe_a_enlever
function ponctionne_group($groupe_a_reduire, $groupe_a_enlever)
{
	$request = "SELECT nom_dns FROM ord_appartient_a_gpe WHERE nom_groupe = \"$groupe_a_enlever\"";
	$result = mysql_query($request);
	$clause_where_or = "( 0";
	while ($ligne = mysql_fetch_array($result)) 
	{
		$clause_where_or .= " OR nom_dns=\"".$ligne['nom_dns']."\"";
	}
	$clause_where_or .= " )";
	# DELETE avec l'option IGNORE n'existe qu'a partir de MySQL 4.1.1 qui
	# n'est même pas dans Debian Sarge, alors on enlève IGNORE pour la 
	# version 1.0 d'Octobre 2005
	#mysql_query("DELETE IGNORE FROM ord_appartient_a_gpe WHERE nom_groupe = \"$groupe_a_reduire\" AND $clause_where_or");
	mysql_query("DELETE FROM ord_appartient_a_gpe WHERE nom_groupe = \"$groupe_a_reduire\" AND $clause_where_or");
	PrintDebug("DELETE FROM ord_appartient_a_gpe WHERE nom_groupe = \"$groupe_a_reduire\" AND $clause_where_or");
}

# Une fonction qui renvoie la version du serveur MySQL utlisé
function mysql_version() {
	$version=split("[-_]",mysql_get_server_info());
	return $version[0];
}

# Traitement des erreurs de saisie dans un champ de formulaire
function erreur_saisie_formulaire($champ) 
{
	print ("<P><I><FONT COLOR=RED>ATTENTION : Erreur lors de la saisie du champ --&gt;$champ&lt;--.</FONT>. Utilisez le bouton <TT>BACK</TT> de votre navigateur pour corriger.</I></P>");
	exit(0);
}

# Traitement des erreurs de saisie dans un champ de formulaire avec message explicatif
function explication_erreur_saisie_formulaire($champ, $message) 
{
	print ("<P><I><FONT COLOR=RED>ATTENTION, erreur lors de la saisie du champ --&gt;$champ&lt;-- : $message</FONT></P><P>Utilisez le bouton <TT>BACK</TT> de votre navigateur pour corriger.</I></P>");
	exit(0);
}

# Importe le dump sql passé en paramètre dans une base MySQL
function importe_dump($dump)
{
    # varible pour stocker la requête courrante
    $query = '';

    # lit le fichier et renvoie le résultat dans un tableau
    $lines = file($dump);

    foreach ($lines as $line) {

        # si la ligne est un commentaire, on passe à la ligne suivante sans effectuer de traitement
        if (substr($line, 0, 2) == '--' || $line == '')
            continue;
 
        # On ajoute la ligne à la requête en court
        $query .= $line;

        # Si on detecte un ; à la fin de la ligne, la requête est entière
        if (substr(trim($line), -1, 1) == ';') {
		    # On execute la requete 
            mysql_query($query) || print mysql_error(); 

		    # On vide la variable qui va "accueillir" la requête suivante
            $query = '';
        }
    }
}

# Renvoie vrai si l OS est de type Linux
function EstUnLinux($nom_os) {
	return ($nom_os=="Linux" || $nom_os=="Linux_x64");
}

?>
