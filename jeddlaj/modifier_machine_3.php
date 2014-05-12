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
if (isset($_GET["nom_dns"])){$nom_dns = $_GET["nom_dns"];}
if (isset($_POST["checked"])){$checked = $_POST["checked"];}
if (isset($_POST["nb_groupes"])){$nb_groupes = $_POST["nb_groupes"];}
if (isset($_POST["nom_groupe"])){$nom_groupe = $_POST["nom_groupe"];}
if (isset($_POST["modif_base"])){$modif_base = $_POST["modif_base"];}

# toutes les variables ont ete recuperees


include("Utils.php");
include("UtilsHTML.php");
include("UtilsMySQL.php");
include("UtilsJeDDLaJ.php");


# Main()
entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Machine $nom_dns, groupes");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);
print("<CENTER><H1>$nom_dns : groupes</H1></CENTER>\n");

if (est_verrouille_pour_mon_ip($nom_dns))
{
	# On resette la duree de verrouillage 
	deverrouille_pour_mon_ip($nom_dns);
	verrouille_pour_mon_ip($nom_dns);
}
else 
{
	print("La sélection a expiré. Retournez à l'<A HREF=accueil.php>accueil</A> pour relancer une procédure de modification machine.<BR>\n");
	$expirade = 1;
}


if (!isset($expirade))
{
	# On a deja fait des modifs dans le formulaire des groupes, 
	# il est temps de modifier effectivement la base
	if (isset($modif_base))
	{
		if (est_verrouille_pour_mon_ip($nom_dns))
		{
			# 1. On récupère les groupes concernés (i.e. cochés)
			$groupes_concernes = array();
			$nb_groupes_concernes=0;
			$liste_groupes_concernes="";
			for($i=1;$i<=$nb_groupes;$i++)
			{
				if (isset($checked[$i]))
				{
					$nb_groupes_concernes++;
					$groupes_concernes[$nb_groupes_concernes] = $checked[$i];
					$liste_groupes_concernes .= "&lt;".$groupes_concernes[$nb_groupes_concernes]."&gt; ";
				}
			}
			# 2. On récupère les groupes d'avant : groupes_avant = ...
			$request="SELECT nom_groupe FROM ord_appartient_a_gpe WHERE nom_dns=\"$nom_dns\" AND nom_groupe<>\"tous les ordinateurs\"";
			$result=mysql_query($request);
			$nb_groupes_avant=0;
			while ($ligne=mysql_fetch_array($result))
			{
				$nb_groupes_avant++;
				$groupes_avant[$nb_groupes_avant] = $ligne['nom_groupe'];
			}
			mysql_free_result($result);
			#DebugTab("groupes_concernes");
			#DebugTab("groupes_avant");

			# 3. On crée groupes_en_moins = (groupes avant - groupes concernes) 
			# (voir http://www.nexen.net/docs/php/annotee/function.array-diff.php)
			# On declare $groupes_en_moins comme
			# un array() car, comme ils peut etre vide (si pas
			# de groupes en moins),
			# auquel cas le calcul de array_diff ne l'instancie
			# pas en tant que tableau et donc lorsqu'on va les
			# decrire a l'etape 4., on aura un warning malseant
			# indiquant que le type d'argument pour le foreach est
			# incorrect (il faut un tableau pour le foreach).

			$groupes_en_moins=array();
			if (count($groupes_concernes) > 0)
			{
				if (count($groupes_avant) > 0)
				{
					$groupes_en_moins = array_diff($groupes_avant, $groupes_concernes);
				}
			}
			else # aucun groupe n'est cliqué
			{
				if (count($groupes_avant) > 0) # pas de groupe cliqués alors que la machine appartenait à des groupes avant : ils ont donc tous été supprimés...
				{
					$groupes_en_moins = $groupes_avant;
				}
			}

			# 4. On boucle sur les groupes en moins pour calculer le tableau auto_suppressed_groups
			# i.e. les groupes a supprimer recursivement ( il s'agit des groupes incluant les groupes en moins )
			$auto_suppressed_groups = array();
			#DebugTab("groupes_en_moins");
			foreach($groupes_en_moins as $nom_groupe)
			{
			    $auto_suppressed_groups = array_merge( $auto_suppressed_groups, groupes_incluant($nom_groupe) );
			}
			#DebugTab("auto_suppressed_groups");
			$auto_suppressed_groups = array_unique($auto_suppressed_groups);
			#DebugTab("auto_suppressed_groups");

			# 5. On calcule les groupes_apres, i.e. les groupes auxquels la machine
			# appartiendra finalement. Pour ca, la methode (issue d'une longue 
			# reflexion...) est de 
				# --> a. partir des groupes coches (i.e. concernes) et leur
				# enlever tous les groupes incluant des groupes en moins (si la machine 
				# etait AVANT dans A C et D avec A inclus dans C inclus dans D ET qu'on 
				# a decoche A, C et D restent coches mais doivent etre supprimes en tant
				# que groupes incluant du groupe supprime). Ces groupes ont ete calcule
				# ci-dessus : ce sont les auto_suppressed_groups
				# --> b. ajouter ensuite aux groupes restant (coches - auto_suppressed ) tous 
				# leurs groupes incluant (si la machine etait AVANT dans A,B C et D 
				# avec A inclus dans C inclus dans D ET B inclus dans C inclus dans D ET 
				# qu'on a decoche A, C et D ont ete supprimes en a. (auto_suppressed_groups)
				# Il ne reste donc que B apres a. Mais du coup, C et D doivent etre repris car 
				# ils sont aussi incluant de B...)

			# Si des groupes ont ete coches, on lance le calcul des groupes_apres 
			if (count($groupes_concernes) > 0)
			{
				if (count($auto_suppressed_groups) > 0)
				{
					$groupes_apres = array_diff($groupes_concernes, $auto_suppressed_groups);
				}
				else
				{
					$groupes_apres = $groupes_concernes;
				}
				foreach($groupes_apres as $nom_groupe)
				{
					$groupes_apres = array_unique(array_merge($groupes_apres, groupes_incluant($nom_groupe)));
				}
			}
			else # Groupes apres est evidemment vide si aucun groupe n'a ete coche...
			{
				$groupes_apres = array();
			}

			# 6. On detruit les anciennes appartenances a des groupes
			mysql_query("DELETE FROM ord_appartient_a_gpe where nom_dns=\"$nom_dns\" AND nom_groupe<>\"tous les ordinateurs\"");
			printf ("<P><I>Anciennes informations d'appartenance de $nom_dns à des groupes detruites. Nombre d'enregistrements détruits : %d. </I></P>\n", mysql_affected_rows());
		
			# 7. Si groupes_apres est NON VIDE, on insère...
			if (count($groupes_apres) > 0)
			{
				$inserted = 0;
				$liste_groupes_apres = "";
				foreach($groupes_apres as $nom_groupe)
				{
					mysql_query("INSERT INTO ord_appartient_a_gpe (nom_dns,nom_groupe) VALUES (\"$nom_dns\",\"$nom_groupe\")");
					$liste_groupes_apres .= " $nom_groupe";
					$inserted++;
				}
				printf ("<P><I>Insertion des nouveaux enregistrements dans la table ord_appartient_a_groupe pour l'ordinateur <TT>$nom_dns</TT> et les groupes <TT>$liste_groupes_apres</TT>. Nombre d'enregistrements ajoutés : %d. </I></P>\n", $inserted);
			} # end if (count($groupes_apres) > 0)
			else # Seul l'inamovible groupe tous les ordinateurs reste pour cet ordinateur
			{
				# Cas ou des groupes restaient coches MAIS ils ont ete supprime en tant que 
				# groupes incluant de groupes qui ont ete decoches (auto_suppressed_groups)...
				if (count($groupes_concernes) > 0)
				{
					print("<P><I><TT>$nom_dns</TT> n'appartient plus qu'au groupe obligatoire <TT>tous les ordinateurs</TT>. En effet, tous les groupes qui restaient cochés ont été automatiquement supprimés en tant que groupes incluant de groupes qui ont été décochés...");
				}
				# Cas ou aucun groupe ne reste coche...
				else
				{
					print("<P><I>Aucun groupe n'a été coché, <TT>$nom_dns</TT> appartient donc seulement au groupe obligatoire <TT>tous les ordinateurs</TT>.");
				}
			}
		}
		else
		{
			print("La sélection a expiré. Retournez à l'<A HREF=accueil.php>accueil</A> pour relancer une procédure de modification machine.<BR>\n");
		}
	}
	# On arrive a peine de modifier_machine : on va donc demander 
	# de choisir les groupes auxquels on souhaite appartenir
	else
	{
		# On commence par récupérer les groupes de l'ordinateur sauf tous les ordinateurs qui sera traité de façon spéciale
		# car on ne doit pas pouvoir supprimer l'appartenance d'un ordinateur à ce groupe
		$request="SELECT nom_groupe FROM ord_appartient_a_gpe WHERE nom_dns=\"$nom_dns\" AND nom_groupe<>\"tous les ordinateurs\"";
		$result=mysql_query($request);
		$nb_groupes=0;
		$nb_groupes_de_l_ordinateur=0;
		while ($ligne=mysql_fetch_array($result))
		{
			$nb_groupes_de_l_ordinateur++;
			$groupes_de_l_ordinateur[$nb_groupes_de_l_ordinateur] = $ligne['nom_groupe'];
		}
		mysql_free_result($result);

		EnteteFormulaire("POST","modifier_machine_3.php?nom_dns=$nom_dns");
		EnteteTable("BORDER=1 CELLPADDING=2 CELLSPACING=1");
		echo("<INPUT TYPE=HIDDEN NAME=modif_base VALUE=1>\n");
		$request="SELECT nom_groupe FROM groupes WHERE nom_groupe<>\"tous les ordinateurs\"";
		$result=mysql_query($request);
		$nb_groupes=0;
		while ($ligne = mysql_fetch_array($result)) 
		{
			$nb_groupes++;
			# On regarde si l'ordinateur est dans le groupe courant afin de préchecker la case concernée...
			$dans_ce_groupe=0;
			$i=1;
			while (!$dans_ce_groupe and $i<=$nb_groupes_de_l_ordinateur)
			{
				$dans_ce_groupe = ($ligne["nom_groupe"] == $groupes_de_l_ordinateur[$i]);
				$i++;
			}
			$dans_ce_groupe ? $checked = " CHECKED " : $checked = "";
			print("<TR><TD>\n$ligne[nom_groupe]\n</TD>\n<TD>\n <INPUT TYPE=CHECKBOX NAME=\"checked[$nb_groupes]\" VALUE=\"$ligne[nom_groupe]\"".$checked.">\n </TD></TR>\n");
		}
		# On checke d'office le groupe "tous les ordinateurs" et on empêche qu'il soit déchecké...
		print("<TR><TD>\ntous les ordinateurs\n</TD>\n<TD>\n <INPUT DISABLED CHECKED TYPE=CHECKBOX NAME=\"tous les ordinateurs\" VALUE=\"tous les ordinateurs\">\n </TD></TR>\n");
		mysql_free_result($result);
		print("<INPUT TYPE=HIDDEN NAME=\"nb_groupes\" VALUE=$nb_groupes>\n");
		FinTable();
		print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
		FinFormulaire();
	}
}

print("<BR><HR><P><CENTER><A HREF=modifier_machine.php?nom_dns=$nom_dns>Retour</A></CENTER>\n");
DisconnectMySQL();


PiedPage();
//FIN Main()

?>
