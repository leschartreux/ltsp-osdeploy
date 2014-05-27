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
if (isset( $_POST['deja_passe_dans_choix_machines'])) {$deja_passe_dans_choix_machines = $_POST['deja_passe_dans_choix_machines'];}
if (isset( $_POST['nom_dns'])) { $nom_dns = $_POST['nom_dns']; }
if (isset( $_POST['nb_ordinateurs'])) { $nb_ordinateurs = $_POST['nb_ordinateurs']; }

if (isset( $_POST['action'])) {$action = $_POST['action']; }
if (!isset($action)) { $action = $_GET['action']; }

# Dans le cas de la creation de groupe, on a cette variable en arivant de choix_groupes_multiples
if (isset( $_POST['nb_groupes'])) {$nb_groupes = $_POST['nb_groupes']; }

# Dans le cas de la creation de groupe, on a ces variables une fois passe une fois dans choix_machines_multiples
if (isset( $_POST['nb_groupes_inclus'])) {$nb_groupes_inclus = $_POST['nb_groupes_inclus']; }
if (isset( $_POST['liste_groupes_inclus'])) {$liste_groupes_inclus = $_POST['liste_groupes_inclus']; }

if (isset( $_POST['checked'])) {$checked = $_POST['checked']; }
if (isset( $_POST['nom_groupe'])) {$nom_groupe = $_POST['nom_groupe']; }
# En cas de modif du nom, cas action=modification_de_groupe, on sait ainsi sur quel groupe on travaille...
if (isset( $_POST['ancien_nom_du_groupe'])) {$ancien_nom_du_groupe = $_POST['ancien_nom_du_groupe']; }
if (isset( $_POST['description_groupe'])) {$description_groupe = $_POST['description_groupe']; }
if (isset( $_POST['photo'])) {$photo = $_POST['photo']; }
# toutes les variables ont ete recuperees

include("UtilsHTML.php");
include("UtilsMySQL.php");
include("UtilsJeDDLaJ.php");
include("Utils.php");

####################
#  Modif Pyddlaj  #
###################
include("UtilsPyDDLaJ.php");


# Main()
entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Choix machines ($action)");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);
print("<CENTER><H1>Choix machines ($action)</H1></CENTER>\n");

# On a déjà effectué un choix de machines
if (isset($deja_passe_dans_choix_machines))
{

	# L'enjeu de ce if est le suivant. On a déjà fait un choix de machines. Il faut maintenant agir sur les machines
	# choisies en fonction de $action (suppression de machines, passage en état idb/packages, creation/suppression de groupe, etc.)


	# Le nombre de machines choisies est non nul... OU BIEN on est dans le cas de la creation/modification de groupe avec 0 machine isolée
	# mais des groupes inclus, ce qui est autorisé
	if(!isset($checked)) {$checked = array();}
	if (count($checked) > 0 or (count($checked) == 0 and ($action == "creation_de_groupe" or $action == "modification_de_groupe") and $nb_groupes_inclus != 0))
	{
		$nb_ordinateurs_concernes=0;
		$liste_ordinateurs_concernes="";
		# On ne fait la liste des ordinateurs concernés que s'il y en a (c'est-à-dire toujours SAUF dans le cas de 
		# la création de groupe avec groupe inclus ET sans ordinateurs isolés)
		if (count($checked) > 0)
		{
			for($i=1;$i<=$nb_ordinateurs;$i++)
			{
				if (isset($checked[$i]))
				{
					$nb_ordinateurs_concernes++;
					$ordinateurs_concernes[$nb_ordinateurs_concernes] = $checked[$i];
					$liste_ordinateurs_concernes .= $ordinateurs_concernes[$nb_ordinateurs_concernes]." ";
					# On verrouille les ordinateurs sur lesquels on travaille
					verrouille_pour_mon_ip($ordinateurs_concernes[$nb_ordinateurs_concernes]);
				}
			}
		}
		# On checke le contexte d'appel du script choix_machines_multiples
		switch ($action)
        	{
 			case "suppression":
				# On initialise la clause WHERE
				$clause_where=" WHERE 1=0";
				for($i=1;$i<=$nb_ordinateurs_concernes;$i++)
				{
					$clause_where .= " OR nom_dns=\"$ordinateurs_concernes[$i]\"";
				}
				# Les tables où nom_dns apparaît
				$tables_concernees = array("composant_est_installe_sur", "depannage", "idb_est_installe_sur", "package_est_installe_sur", "ord_appartient_a_gpe", "ordinateurs", "ordinateurs_en_consultation", "partitions", "stockages_de_masse");
				# On detruit les enregistrements des ordinateurs concernes dans le
				# tables concernees
				foreach($tables_concernees as $table)
				{
					$request="DELETE FROM $table".$clause_where;
					mysql_query($request);
	                                if (mysql_affected_rows() > 0)
					{
						printf ("<P><FONT SIZE=-1><I>Table <TT>$table</TT> : Destruction des %d enregistrements relatifs à <TT>$liste_ordinateurs_concernes</TT>.</I></P></FONT>\n", mysql_affected_rows());
					}
				}

                        	# Il y a aussi les tables postinstall_scripts et predeinstall_scripts qu'il faut UPDATER pour les scripts qui s'appliquaient aux machines à supprimer
                        	$tables_concernees = array("postinstall_scripts", "predeinstall_scripts");
				$scripts_updates = 0;
                        	foreach($tables_concernees as $table)
				{
					for($i=1;$i<=$nb_ordinateurs_concernes;$i++)
                        		{
						$request = "UPDATE $table SET applicable_a=\"rien_pour_l_instant\", valeur_application=\"\" WHERE applicable_a=\"nom_dns\" AND valeur_application=\"$ordinateurs_concernes[$i]\"";
						mysql_query($request);
						$scripts_updates += mysql_affected_rows();
					}
					if ($scripts_updates>0)
					{
						printf ("<P><FONT SIZE=-1><I>Table <TT>$table</TT> : Modification des %d enregistrements relatifs à <TT>$liste_ordinateurs_concernes</TT>.</I></P></FONT>\n", $scripts_updates);
					}
					$scripts_updates = 0;
				}
				break;

			case "packages":
				# 1. On met idb_active à NON sur les images de base marquées active sur toutes les machines choisies
				# On initialise la clause WHERE
				$clause_where=" WHERE 1=0";
				for($i=1;$i<=$nb_ordinateurs_concernes;$i++)
				{
					$clause_where .= " OR (nom_dns=\"$ordinateurs_concernes[$i]\" AND idb_active=\"oui\")";
				}
				mysql_query("UPDATE idb_est_installe_sur set idb_active=\"non\"".$clause_where);

				# 2. On passe les machines choisies en état package
				# On initialise la clause WHERE
				$clause_where=" WHERE 1=0";
				for($i=1;$i<=$nb_ordinateurs_concernes;$i++)
				{
					$clause_where .= " OR nom_dns=\"$ordinateurs_concernes[$i]\"";
				}
				mysql_query("UPDATE ordinateurs set etat_install=\"package\"".$clause_where);

				# 3. On déverrouille des qu'on a fini les actions
				for($i=1;$i<=$nb_ordinateurs_concernes;$i++)
				{
					deverrouille_pour_mon_ip($ordinateurs_concernes[$i]);
				}

				# 4. On donne un peu d'info.
				if ($nb_ordinateurs_concernes>1)
				{
					print ("<P><I>Les machines <FONT COLOR=RED>$liste_ordinateurs_concernes</FONT> sont prêtes pour y fabriquer des packages. Rebootez-les et laissez-vous guider...</I></P>\n");
				}
				else
				{
					print ("<P><I>La machine <FONT COLOR=RED>$liste_ordinateurs_concernes</FONT> est prête pour y fabriquer des packages. Rebootez-la et laissez-vous guider...</I></P>\n");
				}
				break;

			case "idbs":
				# 1. On passe les machines choisies en état idb
				# On initialise la clause WHERE
				$clause_where=" WHERE 1=0";
				for($i=1;$i<=$nb_ordinateurs_concernes;$i++)
				{
					$clause_where .= " OR nom_dns=\"$ordinateurs_concernes[$i]\"";
				}
				mysql_query("UPDATE ordinateurs set etat_install=\"idb\"".$clause_where);

				# 2. On déverrouille des qu'on a fini les actions
				for($i=1;$i<=$nb_ordinateurs_concernes;$i++)
				{
					deverrouille_pour_mon_ip($ordinateurs_concernes[$i]);
				}
				
				#####################
				#3 Modif pour pyddlaj
				#On efface le fichier de boot local pour forcer le boot sur l'OS dédié à pyddlaj
				####################
				for($i=1;$i<=$nb_ordinateurs_concernes;$i++)
				{
					supprime_boot_local($ordinateurs_concernes[$i]);
				}


				# 3. On donne un peu d'info.
				if ($nb_ordinateurs_concernes>1)
				{
					print ("<P><I>Les machines <FONT COLOR=RED>$liste_ordinateurs_concernes</FONT> sont prêtes pour la fabrication d'images de base. Rebootez-les et laissez-vous guider...</I></P>\n");
				}
				else
				{
					print ("<P><I>La machine <FONT COLOR=RED>$liste_ordinateurs_concernes</FONT> est prête pour la fabrication d'images de base. Rebootez-la et laissez-vous guider...</I></P>\n");
				}
				
				
				break;

		#	case "depannage":
		#		# 1. On passe les machines choisies en état depannage
		#		# On initialise la clause WHERE
		#		$clause_where=" WHERE 1=0";
		#		for($i=1;$i<=$nb_ordinateurs_concernes;$i++)
		#		{
		#			$clause_where .= " OR nom_dns=\"$ordinateurs_concernes[$i]\"";
		#		}
		#		mysql_query("UPDATE ordinateurs set etat_install=\"depannage\"".$clause_where);

		#		# 2. On déverrouille des qu'on a fini les actions
		#		for($i=1;$i<=$nb_ordinateurs_concernes;$i++)
		#		{
		#			deverrouille_pour_mon_ip($ordinateurs_concernes[$i]);
		#		}

		#		# 3. On donne un peu d'info.
		#		if ($nb_ordinateurs_concernes>1)
		#		{
		#			print ("<P><I>Les machines <FONT COLOR=RED>$liste_ordinateurs_concernes</FONT> sont désormais en état dépannage. Rebootez-les pour une partie debug sous l'interface Rembo client avec les droits administrateurs...</I></P>\n");
		#		}
		#		else
		#		{
		#			print ("<P><I>La machine <FONT COLOR=RED>$liste_ordinateurs_concernes</FONT> est désormais en état dépannage. Rebootez-la pour une partie debug sous l'interface Rembo client avec les droits administrateurs...</I></P>\n");
		#		}
		#		break;

 			case "modification_de_groupe":
				# Si pas de nom de groupe (i.e. l'ancien nom a été effacé de la case du formulaire, mais pas remplacé),
				# on ne fait rien et on exige le BACK du navigateur
				if ($nom_groupe == "")
				{
					print ("<P><I><FONT COLOR=RED>HONTE SUR VOUS : vous n'avez pas saisi de nouveau nom pour le groupe $ancien_nom_de_groupe. Une seule solution : le bouton BACK de votre navigateur pour corriger cette bévue...</I></P>\n");
					break;
				}
				else
				{
					# On commence par supprimer tous les ordinateurs que $ancien_nom_du_groupe contenait avant d'etre modifie
					# de tous les groupes incluant $ancien_nom_de_groupe (ensuite (plus bas a la fin de la partie creation de groupe utilisée
					# malicieusement en omettant le break entre les deux "case") on fera l'operation inverse i.e. ajouter 
					# a tous les groupes incluant $nom_groupe (=$ancien_nom_de_groupe si on ne l'a pas modifié) tous les ordinateurs
					# appartenant désormais à $nom_groupe)
					$gr_inc = groupes_incluant($nom_groupe);
					# On ne fait rien si pas de groupes incluant
					if (!empty($gr_inc))
					{
						print ("<H2> L'ouverture en DELETE mineur : on supprime habilement les machines contenues dans <TT>$ancien_nom_du_groupe</TT> avant sa modification de tous ses éventuels surgroupes : </H2>\n");
						foreach($gr_inc as $nom_groupe_incluant)
						{
							PrintDebug("On ponctionne $nom_groupe_incluant en lui supprimant toutes les machines de $ancien_nom_du_groupe <BR>");
							ponctionne_group($nom_groupe_incluant, $ancien_nom_du_groupe);
							print ("<P><I> &nbsp;&nbsp;&nbsp; - Surgroupe <TT>$nom_groupe_incluant</TT> : done.</I></P>\n");
						}
					}
					
					# Ensuite on détruit tout ce qui concerne le groupe $ancien_nom_du_groupe
					# pour ensuite créer le groupe $nom_groupe : pour ça on profitera du case "creation de groupe"
					# qui est déjà écrit juste après => SURTOUT PAS DE break; à LA FIN DU CASE "modification_de_groupe" !!!!!
					# Les tables où le champ nom_groupe apparaît
					$tables_concernees = array("groupes", "ord_appartient_a_gpe", "gpe_est_inclus_dans_gpe");
					# On detruit les enregistrements des groupes concernes dans les
					# tables concernees
					print("<H2> Destruction radicale des anciennes informations relatives à <TT>$ancien_nom_du_groupe</TT> </H2>\n");
					foreach($tables_concernees as $table)
					{
						$request="DELETE FROM $table WHERE nom_groupe=\"$ancien_nom_du_groupe\"";
						mysql_query($request);
						printf ("<P><I>Table <TT>$table</TT> : Destruction des %d enregistrements relatifs à <TT>$ancien_nom_du_groupe</TT> </I></P>\n", mysql_affected_rows());
					}
					# Si le nom du groupe a changé, on modifie les infos pour les groupes dans lequel est inclus ancien_nom_de_groupe
					# et pour les postinstall scripts qui s'appliquent sur $ancien_nom_du_groupe
					# Pour ces infos, on ne choisit pas de détruire puis recréer car au contraire des autres, des requêtes
					# supplémentaires seraient nécessaires pour stocker les infos avant de les détruire (pour celles qu'on 
					# détruit, on a déjà tout via les formulaires des pages précédentes...)
					if ($ancien_nom_du_groupe != $nom_groupe)
					{
						mysql_query("UPDATE gpe_est_inclus_dans_gpe SET nom_groupe_inclus=\"$nom_groupe\" WHERE nom_groupe_inclus=\"$ancien_nom_du_groupe\"");
						printf ("<P><I>Table <TT>gpe_est_inclus_dans_gpe</TT> : changement de nom de groupe (<TT>$ancien_nom_du_groupe</TT> --> <TT>$nom_groupe</TT>) dans %d enregistrements où <TT>$ancien_nom_du_groupe</TT> apparaît comme groupe inclus</I></P>\n", mysql_affected_rows());
						mysql_query("UPDATE postinstall_scripts SET valeur_application=\"$nom_groupe\" WHERE applicable_a=\"nom_groupe\" AND valeur_application=\"$ancien_nom_du_groupe\"");
						printf ("<P><I>Table <TT>postinstall_scripts</TT> : changement de nom de groupe (<TT>$ancien_nom_du_groupe</TT> --> <TT>$nom_groupe</TT>) dans %d enregistrements où <TT>$ancien_nom_du_groupe</TT> apparaît comme valeur d'application du script</I></P>\n", mysql_affected_rows());
					}
					print("<H2> Insertion élégante des nouvelles informations relatives à <TT>$nom_groupe</TT>. </H2>\n");
					# PAS DE break; pour faire la creation de groupe qu'on vient de détruire...
				}
				
 			case "creation_de_groupe":
				# Si pas de nom de groupe, on ne fait rien et on exige le BACK du navigateur
				if ($nom_groupe == "")
				{
					print ("<P><I><FONT COLOR=RED>HONTE SUR VOUS : vous n'avez pas saisi de nom pour le groupe à créer. Une seule solution : le bouton BACK de votre navigateur pour corriger cette bévue...</I></P>\n");
				}
				else
				# On a un nom de groupe, on va insérer dans la base
				{

					# 1. On insère le groupe dans la base

					if ($photo == "") {$photo = $default_group_photo;}
					if ($description_groupe == "") {$description_groupe = $default_group_description;}
					mysql_query("INSERT INTO groupes (nom_groupe, description_groupe, photo) VALUES (\"$nom_groupe\",\"$description_groupe\",\"$photo\")"); 
					print ("<P><I>Groupe <TT>$nom_groupe</TT> inséré d'une seule main dans la base. </I></P>\n");
					$inserted = 0;

					# FIN 1. On insère le groupe dans la base

					# 2. On insère les ordinateurs isoles dans la base

					# On commence par insérer les ordinateurs isolés, i.e. non issus d'un groupe inclus
					for($i=1;$i<=$nb_ordinateurs_concernes;$i++)
					{
						mysql_query("INSERT INTO ord_appartient_a_gpe (nom_dns,nom_groupe) VALUES (\"$ordinateurs_concernes[$i]\", \"$nom_groupe\")");
						$inserted++;
						# On deverrouille des qu'on a inséré
						deverrouille_pour_mon_ip($ordinateurs_concernes[$i]);
					}

					# FIN 2. On insère les ordinateurs isoles dans la base

					# 3. On insère les ordinateurs issus des groupes inclus

					# On insère ensuite les ordinateurs issus des groupes inclus
					# 3.1. On calcule quels sont ces ordinateurs
					# On initialise la clause WHERE
					# DEBUG
					#print("La liste des groupes inclus : $liste_groupes_inclus <BR>");
					#print("Le nombre de groupes inclus : $nb_groupes_inclus <BR>");
					# END DEBUG
					$groupes_inclus = explode(",", $liste_groupes_inclus);
					$clause_where=" WHERE 1=0";
					for($i=0;$i<$nb_groupes_inclus;$i++)
					{
						$clause_where .= " OR nom_groupe=\"$groupes_inclus[$i]\"";
					}
					$request = "SELECT DISTINCT nom_dns FROM ord_appartient_a_gpe".$clause_where;
					$result=mysql_query($request);
					# 3.2. On insère les ordinateurs
					while ($ligne = mysql_fetch_array($result)) 
					{
						mysql_query("INSERT INTO ord_appartient_a_gpe (nom_dns,nom_groupe) VALUES (\"$ligne[nom_dns]\", \"$nom_groupe\")");
						$inserted++;
						# On deverrouille des qu'on a inséré
						deverrouille_pour_mon_ip($ligne['nom_dns']);
						$liste_ordinateurs_concernes .= " $ligne[nom_dns]";
					}

					printf ("<P><I>Insertion les yeux bandés des enregistrements dans la table <TT>ord_appartient_a_groupe</TT> pour le groupe <TT>$nom_groupe</TT> et les ordinateurs <TT>$liste_ordinateurs_concernes</TT>. Nombre d'enregistrements ajoutés : <TT>%d</TT>. </I></P>\n", $inserted);

					# FIN 3. On insère les ordinateurs issus des groupes inclus

					# 4. On rentre les insertions de groupes dans la base

					# On s'attaque maintenant à la table gpe_est_inclus_dans_gpe
					$inserted = 0;
					for($i=0;$i<$nb_groupes_inclus;$i++)
					{
						mysql_query("INSERT INTO gpe_est_inclus_dans_gpe (nom_groupe_inclus,nom_groupe) VALUES (\"$groupes_inclus[$i]\", \"$nom_groupe\")");
						$inserted++;
					}
					print ("<H2> On gère tout en sifflotant \"Jeux Interdits\" les inclusions inter-groupes : </H2>\n");
					printf ("<P><I>Insertion souple des enregistrements dans la table <TT>gpe_est_inclus_dans_gpe</TT> pour le groupe <TT>$nom_groupe</TT> et les groupes inclus <TT>$liste_groupes_inclus</TT>. Nombre d'enregistrements ajoutés : <TT>%d</TT>. </I></P>\n", $inserted);

					# FIN 4. On rentre les insertions de groupes dans la base

					# 5. On insère les ordinateurs du groupe modifie dans tous les groupes incluant
					# (Rappel : on avait viré les ordinateurs de groupe en question avant sa modification
					# de tous les groupes incluants plus haut dans le case "modification_de_groupe"
					# *** ATTENTION *** : Ceci ne se fait que dans le cas de la modification de groupe
					# (qui passe par ici que parce qu'il n'y a pas de break entre les 2 "case")
					# En effet, un groupe nouvellement créé n'a pas de groupe incluant...

					if ($action == "modification_de_groupe")
					{
						$gr_inc = groupes_incluant($nom_groupe);
						# On ne fait rien si pas de groupes incluant
						if (!empty($gr_inc))
							{
							print ("<H2> Le Final en INSERT fortissimo : on ajoute en confiance les machines contenues dans <TT>$nom_groupe</TT> après sa modification dans tous ses éventuels surgroupes : </H2>\n");
							foreach($gr_inc as $nom_groupe_incluant)
							{
								feed_group($nom_groupe_incluant, $nom_groupe);
								print ("<P><I> &nbsp;&nbsp;&nbsp; - Surgroupe <TT>$nom_groupe_incluant</TT> : done.</I></P>\n");
							}
						}
					}

					# FIN 5. On insère les ordinateurs du groupe modifies dans tous les groupes incluant

				}


		}# end switch($action)
	}# end if (count($checked) > 0 or (count($checked) == 0 and ($action == "creation_de_groupe" or $action == "modification_de_groupe") and $nb_groupes_inclus != 0))
	else
	{
		# Dans le cas creation/modification de groupe, il peut certes éventuellement ne pas y avoir de machines isolées, mais seulement 
		# si des groupes inclus ont été choisis : ce qui ne peut être le cas dans ce else (voir le if si on doute...)
		if ($action == "creation_de_groupe" or $action == "modification_de_groupe")
		{
			print ("<P><I><FONT COLOR=RED>ATTENTION : Vous n'avez choisi aucun groupe inclus ET aucun ordinateur isolé. Le groupe vide n'est pas autorisé...</FONT>. Utilisez le bouton <TT>BACK</TT> de votre navigateur pour faire une sélection valide.</I></P>");
		}
		else
		{
			print ("<P><I><FONT COLOR=RED>ATTENTION : Vous n'avez choisi aucune machine</FONT>. Utilisez le bouton <TT>BACK</TT> de votre navigateur pour faire une sélection valide.</I></P>");
		}
	}
}# end if (isset($deja_passe_dans_choix_machines))
else
{

	# L'enjeu de ce else est 
		# A. Calculer les ordinateurs à afficher selon qu'on est en cas creation/modif groupe ou modif machine
		# B. Générer et afficher le formulaire adapté
	
	#On vire les verrouillages vieux de plus de $duree_verrouillage
	fait_peter_les_vieux_verrouillages();

	# A. Calcul de la requête qui va rendre le bon ensemble d'ordinateurs à afficher en fonction de $action (modif machine, creation groupe, modif groupe...)

	# Dans le cas creation de groupe ou modification, on ne présente que les ordinateurs qui ne font pas partie des groupes inclus, choisis
	# à la page précédente. Un petit calcul s'impose donc pour déterminer le bon ensemble d'ordinateurs à afficher...
	if ($action == "creation_de_groupe")
	{
		# Le nombre de groupes choisis est non nul
		if(!isset($checked)) {$checked = array();}
		if (count($checked) > 0)
		{
			$nb_groupes_inclus_coches=0;
			$liste_groupes_inclus_coches="";

			# 1. On calcule les groupes inclus directement (groupes_inclus_coches) i.e. ceux cochés dans le formulaire

			for($i=1;$i<=$nb_groupes;$i++)
			{
				if (isset($checked[$i]))
				{
					$groupes_inclus_coches[$nb_groupes_inclus_coches] = $checked[$i];
					$liste_groupes_inclus_coches .= $groupes_inclus_coches[$nb_groupes_inclus_coches].",";
					$nb_groupes_inclus_coches++;
				}
			}
			# DEBUG
#			print("Les groupes inclus coches :");
#			print_r($groupes_inclus_coches);
#			print("<BR>");
			# END DEBUG

			##################################################################################################################
			# On va calculer la liste des groupes inclus level 1 à partir des groupes cochés. Pour cela,
			# on va supprimer de la listes des groupes_coches ceux qui sont inclus dans d'autres groupes cochés, afin de 
			# ne garder que les inclusions de plus haut niveau, les sous-inclusions étant calculées ensuite si besoin est 
			# par la fonction groupes_inclus.

			# Algo 1 : simple mais pas optimal en complexité
			# Idée : Pour chaque groupe coché G, on le marque comme redondant s'il fait partie des groupes inclus de l'un quelconque des autres groupes cochés
			#$groupes_inclus_coches_redondant = array();
			#$nb_groupes_inclus_coches_redondant = 0;
			#$i = 0;
			#while ( $i < $nb_groupes_inclus_coches )
			#{
			#	$j=0;
			#	$non_redondant = 1;
			#	while ( $j < $nb_groupes_inclus_coches and $non_redondant )
			#	{
			#		if ( $j != $i )
			#		{
			#			if ( in_array($groupes_inclus_coches[$i], groupes_inclus($groupes_inclus_coches[$j])) )
			#			{
			#				$groupes_inclus_coches_redondant[$nb_groupes_inclus_coches_redondant] = $groupes_inclus_coches[$i];
			#				$nb_groupes_inclus_coches_redondant++;
			#				$non_redondant = 0;
			#			}
			#		}
			#		$j++;
			#	}
			#	$i++;
			#}
			# Fin algo 1

			# Algo 2 : plus compliqué, mais meilleur en complexité...
			# Idée : pour chaque groupe coché G, on marque comme redondant tous les autres groupes cochés qui font partie des groupes inclus de G
			$groupes_inclus_coches_redondant = array();
			$nb_groupes_inclus_coches_redondant = 0;
			$i = 0;
			while ( $i < $nb_groupes_inclus_coches  )
			{
				# Si le groupe courant (boucle en $i) est déjà dans les redondants, on passe au suivant
				if ( !in_array($groupes_inclus_coches[$i], $groupes_inclus_coches_redondant) )
				{
					# On stocke le résultat de la fonction groupe_inclus pour ne faire qu'un seul appel
					$groupes_inclus_du_groupe_courant = groupes_inclus($groupes_inclus_coches[$i]);
					$j=0;
					while ( $j < $nb_groupes_inclus_coches )
					{
						# Si $i == $j on ne fait rien bien sûr et on passe au suivant
						if ( $j != $i )
						{
							# Si le groupe courant (boucle en $j) est déjà dans les redondants, on ne fait rien et on passe au suivant
							if ( !in_array($groupes_inclus_coches[$j], $groupes_inclus_coches_redondant) )
							{
								# Si le groupe courant (boucle en $j) est dans les groupes inclus du groupe courant (boucle en $i)
								# alors on le met dans les redondants
								if ( in_array($groupes_inclus_coches[$j], $groupes_inclus_du_groupe_courant) ) 
								{
									$groupes_inclus_coches_redondant[$nb_groupes_inclus_coches_redondant] = $groupes_inclus_coches[$j];
									$nb_groupes_inclus_coches_redondant++;
								}
							}
						}
						$j++;
					}
				}
				$i++;
			}
			# Fin algo 2

			$groupes_inclus_level_1 = array_diff($groupes_inclus_coches, $groupes_inclus_coches_redondant);
#			DebugTab( $groupes_inclus_level_1);
			
			##################################################################################################################
			
		#	# 2. On calcule ensuite les groupes inclus transitivement (groupes_inclus_level_2), i.e. les groupes inclus dans les groupes cochés (= les level 1)

		#	$groupes_inclus_level_2 = array();
		#	foreach($groupes_inclus_level_1 as $nom_gpe)
		#	{
		#		# DEBUG
		#		print("FOREACH POUR $nom_gpe <BR>");
		#		# END DEBUG
		#		# On ne traite pas les groupes déjà présents dans $groupes_inclus_level_2 car leurs groupes inclus ont déjà été ajoutés...
		#		if (!in_array( $nom_gpe, $groupes_inclus_level_2))
		#		{
		#			# On ajoute array_unique qui dédoublonne car si on a C inclut B et C inclut A et B inclut A dans la table gpe_est_inclus_dans_gpe
		#			# alors les appels récursifs sur C ET sur B vont renvoyer A qu'on aura donc 2 fois...
		#			$groupes_inclus = array_unique(groupes_inclus($nom_gpe));
		#			# DEBUG
		#			print("FOREACH POUR $nom_gpe : Les groupes inclus de $nom_gpe");
		#			print_r($groupes_inclus);
		#			print("<BR>");
		#			# END DEBUG
		#			# Si $nom_gpe a des groupes inclus
		#			if ( ! empty($groupes_inclus) )
		#			{
		#				if ( ! empty($groupes_inclus_level_2) )
		#				{
		#					$groupes_inclus_level_2 = array_merge($groupes_inclus_level_2, $groupes_inclus );
		#				}
		#				else
		#				{
		#					$groupes_inclus_level_2 = $groupes_inclus;
		#				}
		#			}
		#			# DEBUG
		#			print("FOREACH POUR $nom_gpe : Les groupes inclus level 2 après le foreach pour $nom_gpe");
		#			print_r($groupes_inclus_level_2);
		#			print("<BR>");
		#			# END DEBUG
		#		}
		#	}

		#	# 3. On calcule tous les groupes inclus = union des "inclus direct/level 1" et "inclus transitifs/level 2"

		#	if ( ! empty($groupes_inclus_level_2) )
		#	{
		#		$tous_les_groupes_inclus = array_unique(array_merge($groupes_inclus_level_1, $groupes_inclus_level_2));
		#	}
		#	else
		#	{
		#		$tous_les_groupes_inclus = $groupes_inclus_level_1;
		#	}
		#	#DEBUG
		#	DebugTab("groupes_inclus_level_1");
		#	DebugTab("groupes_inclus_level_2");
		#	DebugTab("tous_les_groupes_inclus");
		#	#END DEBUG

			# 3. On calcule tous les groupes inclus = union des "inclus direct/level 1" et "inclus transitifs/level 2"
			$tous_les_groupes_inclus = $groupes_inclus_level_1;
		#	DebugTab("tous_les_groupes_inclus");

			# 4. On cree les variables $nb_groupes_inclus et $liste_groupes_inclus qui vont être passée plus bas en hidden POST dans le formulaire de choix des machines isolées
			# qui re-appelle choix_machines_multiples

			$nb_groupes_inclus = count($tous_les_groupes_inclus);
			# On cree $liste_groupes_inclus qui va etre passee dans le formulaire
			$liste_groupes_inclus = "";
			foreach($tous_les_groupes_inclus as $gpe)
			{
				$liste_groupes_inclus .= $gpe.",";
			}

			# 5. On cree la requete de choix des machines isolées possible pour ce groupe, i.e. toutes les machines MOINS celles des groupes inclus

			# On initialise la clause WHERE
			$clause_where=" WHERE 1=0";
			foreach($tous_les_groupes_inclus as $gpe)
			{
				$clause_where .= " OR nom_groupe=\"$gpe\"";
			}
			#################### Pour MySQL >= 4.1 i.e. sous-requêtes autorisées
			#$request="SELECT nom_dns from ordinateurs WHERE nom_dns NOT IN (SELECT DISTINCT nom_dns FROM ord_appartient_a_gpe".$clause_where);
			#################### Fin pour MySQL >= 4.1 
			#################### Pour MySQL toutes versions
			# La requete 1 pour choper tous les ordinateurs qu'on ne veut pas afficher i.e. ceux des groupes inclus
			$request1 = "SELECT DISTINCT nom_dns FROM ord_appartient_a_gpe".$clause_where;
			#DEBUG
			#print("request1 = $request1<BR>");
			#ENDDEBUG
			$result1=mysql_query($request1);
			# On initialise la clause WHERE de la requete finale qui nous donne le complémentaire de la requete 1
			# i.e. les ordinateurs qu'on veut afficher...
			$clause_where=" WHERE 1=1";
			while ($ligne = mysql_fetch_array($result1)) 
			{
				$clause_where .= " AND nom_dns<>\"$ligne[nom_dns]\"";
			}
			mysql_free_result($result1);
			$request = "SELECT nom_dns FROM ordinateurs".$clause_where;
			#################### Fin pour MySQL toutes versions
	
		}# end if(count($checked) > 0)
		else # Pas de groupe inclus, on peut donc choisir parmi tous les ordinateurs
		{
			$request="SELECT nom_dns FROM ordinateurs";
		}
	}
	elseif ($action == "modification_de_groupe")
	{
		# 1. On commence par le calcul des groupes inclus AVANT
		$groupes_inclus_avant = array();
		$request = "SELECT nom_groupe_inclus FROM gpe_est_inclus_dans_gpe WHERE nom_groupe=\"$nom_groupe\"";
		$result=mysql_query($request);
		while ($ligne = mysql_fetch_array($result)) 
		{
			$groupes_inclus_avant[] = $ligne['nom_groupe_inclus'];
			$groupes_inclus_avant = array_unique(array_merge($groupes_inclus_avant, groupes_inclus($ligne['nom_groupe_inclus'])));
		}
#		DebugTab("groupes_inclus_avant");
		mysql_free_result($result);
		# 2. On calcule les groupes cochés
		$groupes_inclus_coches = array();
		$nb_groupes_inclus_coches=0;
		$liste_groupes_inclus_coches="";
		if(!isset($checked)) {$checked = array();}
		for($i=1;$i<=$nb_groupes;$i++)
		{
			if (isset($checked[$i]))
			{
				$groupes_inclus_coches[$nb_groupes_inclus_coches] = $checked[$i];
				$liste_groupes_inclus_coches .= $groupes_inclus_coches[$nb_groupes_inclus_coches].",";
				$nb_groupes_inclus_coches++;
			}
		}
#		DebugTab("groupes_inclus_coches");
		############################################################################################################################################
		# 3. On va calculer la liste des groupes inclus level 1 à partir des groupes cochés. Pour cela,
		# on va supprimer de la listes des groupes_coches ceux qui sont inclus dans d'autres groupes cochés, afin de 
		# ne garder que les inclusions de plus haut niveau, les sous-inclusions étant calculées ensuite si besoin est 
		# par la fonction groupes_inclus.
		# Algo 2 : plus compliqué, mais meilleur en complexité...
		# Idée : pour chaque groupe coché G, on marque comme redondant tous les autres groupes cochés qui font partie des groupes inclus de G
		$groupes_inclus_coches_redondant = array();
		$nb_groupes_inclus_coches_redondant = 0;
		$i = 0;
		while ( $i < $nb_groupes_inclus_coches  )
		{
			# Si le groupe courant (boucle en $i) est déjà dans les redondants, on passe au suivant
			if ( !in_array($groupes_inclus_coches[$i], $groupes_inclus_coches_redondant) )
			{
				# On stocke le résultat de la fonction groupe_inclus pour ne faire qu'un seul appel
				$groupes_inclus_du_groupe_courant = groupes_inclus($groupes_inclus_coches[$i]);
				$j=0;
				while ( $j < $nb_groupes_inclus_coches )
				{
					# Si $i == $j on ne fait rien bien sûr et on passe au suivant
					if ( $j != $i )
					{
						# Si le groupe courant (boucle en $j) est déjà dans les redondants, on ne fait rien et on passe au suivant
						if ( !in_array($groupes_inclus_coches[$j], $groupes_inclus_coches_redondant) )
						{
							# Si le groupe courant (boucle en $j) est dans les groupes inclus du groupe courant (boucle en $i)
							# alors on le met dans les redondants
							if ( in_array($groupes_inclus_coches[$j], $groupes_inclus_du_groupe_courant) ) 
							{
								$groupes_inclus_coches_redondant[$nb_groupes_inclus_coches_redondant] = $groupes_inclus_coches[$j];
								$nb_groupes_inclus_coches_redondant++;
							}
						}
					}
					$j++;
				}
			}
			$i++;
		}
		# Fin algo 2

#		DebugTab("groupes_inclus_coches_redondant");
		$groupes_inclus_level_1 = array_diff($groupes_inclus_coches, $groupes_inclus_coches_redondant);
#		DebugTab("groupes_inclus_level_1");
		############################################################################################################################################
		# 4. On calcule les groupes après, dans chaque cas
		# 4.1 Cas vide avant et vide cochés, pas de groupes inclus
		if ( empty($groupes_inclus_avant) AND empty($groupes_inclus_level_1) )
		{
			$groupes_inclus_apres = array();
		}
		# 4.2 Cas vide avant, pas vide cochés : on va avoir des nouveaux groupes inclus...
		elseif (empty($groupes_inclus_avant))
		{
		#	print("On passe LA OU ON VEUT !!!<BR>");
		#	$groupes_inclus_en_plus = $groupes_inclus_level_1;
		#	# On calcule $groupes_inclus_en_plus_level_2, le tableau des groupe qui sont inclus dans les $groupes_inclus_en_plus
		#	$groupes_inclus_en_plus_level_2 = array();
		#	foreach($groupes_inclus_en_plus as $nom_groupe_en_plus)
		#	{
		#		# DEBUG
		#		print("Dans le foreach pour $nom_groupe_en_plus<BR>");
		#		# END DEBUG
		#		# On ne traite pas les groupes déjà présents dans $groupes_inclus_en_plus_level_2 car leurs groupes inclus ont déjà été ajoutés...
		#		if (!in_array( $nom_groupe_en_plus, $groupes_inclus_en_plus_level_2))
		#		{
		#			$groupes_inclus = array_unique(groupes_inclus($nom_groupe_en_plus));
		#			# DEBUG
		#			print("Les groupes inclus de $nom_groupe_en_plus");
		#			print_r($groupes_inclus);
		#			print("<BR>");
		#			# END DEBUG
		#			# Si $nom_groupe_en_plus a des groupes inclus
		#			if ( ! empty($groupes_inclus) )
		#			{
		#				if ( ! empty($groupes_inclus_en_plus_level_2) )
		#				{
		#					$groupes_inclus_en_plus_level_2 = array_merge($groupes_inclus_en_plus_level_2, $groupes_inclus );
		#				}
		#				else
		#				{
		#					$groupes_inclus_en_plus_level_2 = $groupes_inclus;
		#				}
		#				# DEBUG
		#				print("Les groupes inclus de $nom_groupe_en_plus");
		#				print_r($groupes_inclus);
		#				print("<BR>");
		#				# END DEBUG
		#			}
		#		}
		#	}
		#	# Les groupes après, dans ce cas :
		#	$groupes_inclus_apres = array_merge($groupes_inclus_en_plus, $groupes_inclus_en_plus_level_2);

			$groupes_inclus_apres = $groupes_inclus_level_1;
		
		}
		# 4.3 Cas vide coches, pas avant : plus de groupes inclus
		elseif (!isset($groupes_inclus_level_1))
		{
			$groupes_inclus_apres = array();
		}
		# 4.4 Cas pas vide avant, pas vide cochés, mais identiques : on ne fait rien
		elseif (count($groupes_inclus_avant) == count($groupes_inclus_level_1) AND count(array_diff($groupes_inclus_avant, $groupes_inclus_level_1)) == 0)
		{
			$groupes_inclus_apres = $groupes_inclus_avant;
		}
		# 4.5 Cas général : pas vide avant, pas vide cochés et différents
		else
		{

		##################################### THE OLD WAY, HYPRA COMPLEXE... #######################################
		#	$groupes_inclus_en_plus = array_diff($groupes_inclus_level_1, $groupes_inclus_avant);
		#	$groupes_inclus_en_moins = array_diff($groupes_inclus_avant, $groupes_inclus_level_1);
		#	DebugTab("groupes_inclus_en_plus");
		#	DebugTab("groupes_inclus_en_moins");
	#		if (!empty($groupes_inclus_en_plus))
	#		{
	#			# On calcule $groupes_inclus_en_plus_level_2, le tableau des groupe qui sont inclus dans les $groupes_inclus_en_plus
	#			$groupes_inclus_en_plus_level_2 = array();
	#			foreach($groupes_inclus_en_plus as $nom_groupe_en_plus)
	#			{
	#				# DEBUG
	#				print("Dans le foreach pour $nom_groupe_en_plus<BR>");
	#				# END DEBUG
	#				# On ne traite pas les groupes déjà présents dans $groupes_inclus_en_plus_level_2 car leurs groupes inclus ont déjà été ajoutés...
	#				if (!in_array( $nom_groupe_en_plus, $groupes_inclus_en_plus_level_2))
	#				{
	#					# Initialisation du tableau $groupes_inclus afin que les tests 'in_array' de la fonction groupes_inclus
	#					# et du while qui suit ne génère pas d'erreur lorsque groupes_inclus est vide et donc n'est pas considéré
	#					# comme un tableau par PHP
	#					$groupes_inclus = array_unique(groupes_inclus($nom_groupe_en_plus));
	#					# DEBUG
	#					print("Les groupes inclus de $nom_groupe_en_plus");
	#					print_r($groupes_inclus);
	#					print("<BR>");
	#					# END DEBUG
	#					# Si $nom_groupe_en_plus a des groupes inclus
	#					if ( ! empty($groupes_inclus) )
	#					{
	#						if ( ! empty($groupes_inclus_en_plus_level_2) )
	#						{
	#							$groupes_inclus_en_plus_level_2 = array_merge($groupes_inclus_en_plus_level_2, $groupes_inclus );
	#						}
	#						else
	#						{
	#							$groupes_inclus_en_plus_level_2 = $groupes_inclus;
	#						}
	#						# DEBUG
	#						print("Les groupes inclus de $nom_groupe_en_plus");
	#						print_r($groupes_inclus);
	#						print("<BR>");
	#						# END DEBUG
	#					}
	#				}
	#			}
	#		}
	#		if (!empty($groupes_inclus_en_moins))
	#		{
	#			# On calcule $groupes_inclus_en_moins_level_2, le tableau des groupe qui sont inclus dans les $groupes_inclus_en_moins
	#			$groupes_inclus_en_moins_level_2 = array();
	#			foreach($groupes_inclus_en_moins as $nom_groupe_en_moins)
	#			{
	#				# DEBUG
	#				print("Dans le foreach des groupes inclus en moins pour $nom_groupe_en_moins<BR>");
	#				# END DEBUG
	#				# On ne traite pas les groupes déjà présents dans $groupes_inclus_en_moins_level_2 car leurs groupes inclus ont déjà été ajoutés...
	#				if (!in_array( $nom_groupe_en_moins, $groupes_inclus_en_moins_level_2))
	#				{
	#					# Initialisation du tableau $groupes_inclus afin que les tests 'in_array' de la fonction groupes_inclus
	#					# et du while qui suit ne génère pas d'erreur lorsque groupes_inclus est vide et donc n'est pas considéré
	#					# comme un tableau par PHP
	#					$groupes_inclus = array_unique(groupes_inclus($nom_groupe_en_moins));
	#					# DEBUG
	#					print("Les groupes inclus en moins de $nom_groupe_en_moins");
	#					print_r($groupes_inclus);
	#					print("<BR>");
	#					# END DEBUG
	#					# Si $nom_groupe_en_moins a des groupes inclus
	#					if ( ! empty($groupes_inclus) )
	#					{
	#						if (! empty($groupes_inclus_en_moins_level_2) )
	#						{
	#							$groupes_inclus_en_moins_level_2 = array_merge($groupes_inclus_en_moins_level_2, $groupes_inclus );
	#						}
	#						else
	#						{
	#							$groupes_inclus_en_moins_level_2 = $groupes_inclus;
	#						}
	#						# DEBUG
	#						print("Les groupes inclus en moins de $nom_groupe_en_moins");
	#						print_r($groupes_inclus);
	#						print("<BR>");
	#						# END DEBUG
	#					}
	#				}
	#			}
	#		}
	#		if (empty($groupes_inclus_en_plus))
	#		{
	#			$tous_les_groupes_inclus_en_plus = array();
	#			$tous_les_groupes_inclus_en_moins = array_merge($groupes_inclus_en_moins, $groupes_inclus_en_moins_level_2);
	#		}
	#		if (empty($groupes_inclus_en_moins))
	#		{
	#			$tous_les_groupes_inclus_en_moins = array();
	#			$tous_les_groupes_inclus_en_plus = array_merge($groupes_inclus_en_plus, $groupes_inclus_en_plus_level_2);
	#		}
	#		if (!empty($groupes_inclus_en_plus) AND !empty($groupes_inclus_en_moins))
	#		{
	#			$tous_les_groupes_inclus_en_plus = array_merge($groupes_inclus_en_plus, $groupes_inclus_en_plus_level_2);
	#			$tous_les_groupes_inclus_en_moins = array_merge($groupes_inclus_en_moins, $groupes_inclus_en_moins_level_2);
	#		}
	#		DebugTab("tous_les_groupes_inclus_en_moins");
	#		# ICI on va supprimer les inclusions transitives des groupes inclus en moins, i.e. si on a A inclus dans B inclus dans C
	#		# et A inclus dans C
	#		# et qu'on modifie C en décochant A, on va détruire A inclus dans C, mais on aura encore A inclus dans C via la transitivité
	#		# par B. Il faut donc supprimer A inclus dans B...
	#		# Et si on avait C inclus dans D, il faut aussi couper le lien direct éventuel A inclus dans D
	#		foreach ($tous_les_groupes_inclus_en_moins as $g_en_moins)
	#		{
	#			coupe_lien_inclusion_transitif($g_en_moins, $nom_groupe);
	#			foreach(groupes_incluant($nom_groupe) as $g_inc)
	#			{
	#				supprime_inclusion($g_en_moins, $g_inc);
	#			}
	#		}
	#		# Fin suppression des inclusions transitives
	#		$groupes_inclus_apres = array_diff($groupes_inclus_avant, $tous_les_groupes_inclus_en_moins);
	#	DebugTab("tous_les_groupes_inclus_en_moins");
	#	DebugTab("groupes_inclus_avant");
	#	DebugTab("groupes_inclus_apres");
	#		$groupes_inclus_apres = array_merge($groupes_inclus_apres, $tous_les_groupes_inclus_en_plus);
	#	DebugTab("groupes_inclus_apres");
		################################# END OF THE OLD WAY, HYPRA COMPLEXE... ###################################

		################################# THE NEW WAY, SIMPLER... #################################
			$groupes_inclus_en_plus = array_diff($groupes_inclus_level_1, $groupes_inclus_avant);
			$groupes_inclus_en_moins = array_diff($groupes_inclus_avant, $groupes_inclus_level_1);
#			DebugTab("groupes_inclus_en_plus");
#			DebugTab("groupes_inclus_en_moins");
			$tous_les_groupes_inclus_en_moins = $groupes_inclus_en_moins;
			$tous_les_groupes_inclus_en_plus = $groupes_inclus_en_plus;
#			DebugTab("tous_les_groupes_inclus_en_plus");
#			DebugTab("tous_les_groupes_inclus_en_moins");
			$groupes_inclus_apres = array_diff($groupes_inclus_avant, $tous_les_groupes_inclus_en_moins);
			$groupes_inclus_apres = array_merge($groupes_inclus_apres, $tous_les_groupes_inclus_en_plus);
#			DebugTab("groupes_inclus_apres");
		############################## END OF THE NEW WAY, SIMPLER... #############################


		}


		$nb_groupes_inclus = count($groupes_inclus_apres);
		$liste_groupes_inclus = "";
		foreach($groupes_inclus_apres as $gpe)
		{
			$liste_groupes_inclus .= $gpe.",";
		}
		# On initialise la clause WHERE
		$clause_where=" WHERE 1=0";
		foreach($groupes_inclus_apres as $gpe)
		{
			$clause_where .= " OR nom_groupe=\"$gpe\"";
		}
		#################### Pour MySQL >= 4.1 i.e. sous-requêtes autorisées
		#$request="SELECT nom_dns from ordinateurs WHERE nom_dns NOT IN (SELECT DISTINCT nom_dns FROM ord_appartient_a_gpe".$clause_where);
		#################### Fin pour MySQL >= 4.1 
		#################### Pour MySQL toutes versions
		# La requete 1 pour choper tous les ordinateurs qu'on ne veut pas afficher i.e. ceux des groupes inclus
		$request1 = "SELECT DISTINCT nom_dns FROM ord_appartient_a_gpe".$clause_where;
		#DEBUG
		#print("request1 = $request1<BR>");
		#ENDDEBUG
		$result1=mysql_query($request1);
		# On initialise la clause WHERE de la requete finale qui nous donne le complémentaire de la requete 1
		# i.e. les ordinateurs qu'on veut afficher...
		$clause_where=" WHERE 1=1";
		while ($ligne = mysql_fetch_array($result1)) 
		{
			$clause_where .= " AND nom_dns<>\"$ligne[nom_dns]\"";
		}
		mysql_free_result($result1);
		$request = "SELECT nom_dns FROM ordinateurs".$clause_where;
		#################### Fin pour MySQL toutes versions
	}
	else # ni creation_de_groupe, ni modification_de_groupe...
	{
		$request="SELECT nom_dns FROM ordinateurs";
	}

	# FIN A. Calcul de la requête qui va rendre le bon ensemble d'ordinateurs à afficher en fonction de $action (modif machine, creation groupe, modif groupe...)

	# B. Génération du formulaire de choix des machines adapté selon $action (modif machine, creation groupe, modif groupe...)

	# On a calculé la requete d'affichage des ordinateurs que l'on veut pouvoir choisir dans tous les cas (creation de groupe, modif de groupe, de machine, etc.) 
	# et on l'a mis dans la variable $request : il est temps de la lancer et de générer le formulaire adapté selon les cas

	$result=mysql_query($request);

	# CREATION DU FORMULAIRE ADAPTE SUIVANT $action (creation groupe, modif groupe ou modif machine ou suppression machine)

	# Cas de la modification d'une machine
	if ($action == "modification")
	{
		EnteteFormulaire("POST","modifier_machine.php");
	}
	elseif ($action == "modification_machines_nombreuses")
	{
		EnteteFormulaire("POST","modifier_machines_nombreuses.php");
	}
	else # Cas modification de GROUPE, suppression de machines, etc...
	{
		EnteteFormulaire("POST","choix_machines_multiples.php");
	}
	print("<INPUT TYPE=HIDDEN NAME=action VALUE=\"$action\">\n");
	print("<INPUT TYPE=HIDDEN NAME=\"deja_passe_dans_choix_machines\" VALUE=1>\n");
	
	# Dans le cas de la creation/modification de groupe on passe le nom des groupes inclus dans le groupe en cours de creation
	# Test du !isset car il se peut que nb_groupes_inclus et liste_groupes_inclus soient non instanciées s'il n'y a pas de groupes inclus...
	# et dans ce cas le test du if isset $_POST du débuit de fichier est inopérant...
	if ($action == "creation_de_groupe" or $action == "modification_de_groupe")
	{
		if (!isset($nb_groupes_inclus)) {$nb_groupes_inclus = 0;}
		if (!isset($liste_groupes_inclus)) {$liste_groupes_inclus = "";}
		print("<INPUT TYPE=HIDDEN NAME=nb_groupes_inclus VALUE=\"$nb_groupes_inclus\">\n");
		print("<INPUT TYPE=HIDDEN NAME=liste_groupes_inclus VALUE=\"$liste_groupes_inclus\">\n");
	}

	# Dans le cas modification_de_groupe, on veut afficher cochés les ordinateurs isolés que comprenait le groupe
	# il faut donc les calculer
	if ($action == "modification_de_groupe")
	{
		# 1. Les ordinateurs du groupe
		$request1="SELECT nom_dns FROM ord_appartient_a_gpe WHERE nom_groupe=\"$nom_groupe\"";
		$result1=mysql_query($request1);
		$i=0;
		while($ligne = mysql_fetch_array($result1))
		{
			$ordinateurs_du_groupe[$i++] = $ligne['nom_dns'];
		}
		mysql_free_result($result1);
		# 2. Les ordinateurs des groupes inclus 
		# 2.1 Les groupes inclus (on ne peut pas ici utiliser la variable groupe_inclus issue du choix de 
		# la page précédente car le JeDDLaJeur
		# a peut-être modifié les groupes inclus. Il faut donc aller chercher les infos dans la base...
		$request2="SELECT nom_groupe_inclus FROM gpe_est_inclus_dans_gpe WHERE nom_groupe=\"$nom_groupe\"";
		$result2=mysql_query($request2);
		$i=0;
		while($ligne = mysql_fetch_array($result2))
		{
			$groupes_inclus_selon_la_DB[$i++] = $ligne['nom_groupe_inclus'];
		}
		mysql_free_result($result2);
		$nb_groupes_inclus_selon_la_DB = $i;
		# S'il y a des groupes inclus, on calcule...
		if ($nb_groupes_inclus_selon_la_DB > 0)
		{
			# 2.2 Les ordi de ces groupes inclus
			# On initialise la clause WHERE
			$clause_where=" WHERE 1=0";
			for($i=0;$i<$nb_groupes_inclus_selon_la_DB;$i++)
			{
				$clause_where .= " OR nom_groupe=\"$groupes_inclus_selon_la_DB[$i]\"";
			}
			$request3 = "SELECT DISTINCT nom_dns FROM ord_appartient_a_gpe".$clause_where;
			$result3=mysql_query($request3);
			$i=0;
			while($ligne = mysql_fetch_array($result3))
			{
				$ordinateurs_des_groupes_inclus[$i++] = $ligne['nom_dns'];
			}
			$nb_ordinateurs_des_groupes_inclus = $i;
			mysql_free_result($result3);
			# 3. Les ordinateurs isolés = (les ordinateurs du groupe - les ordinateurs des groupes inclus)
			$ordinateurs_isoles = array_diff($ordinateurs_du_groupe, $ordinateurs_des_groupes_inclus);
		}
		else # Pas de groupes inclus, donc tous les ordinateurs du groupe sont des ordinateurs isolés...
		{
			$ordinateurs_isoles = $ordinateurs_du_groupe;
		}
	}

	# Creation des entrées "ordinateurs" dans le formulaire

	EnteteTable("BORDER=2 CELLPADDING=2 CELLSPACING=1");
	$nb_ordinateurs = 0;
	while ($ligne = mysql_fetch_array($result)) {
		$nb_ordinateurs++;
		# On deverrouille nom_dns pour mon_ip
		deverrouille_pour_mon_ip($ligne['nom_dns']);
		print("<TR>\n");
		# Cas modif d'une machine : pour l'instant qu'une a la fois
		# donc RADIO
		if ($action == "modification")
		{
			list($est_verrouille, $ip_verrouillante) = est_verrouille_pour_une_autre_IP($ligne['nom_dns']);
			list($est_indisponible, $etat_install) = est_dans_un_etat_install_bloquant($ligne['nom_dns']);
			if ($est_verrouille)
			{
				print("<TD>\n$ligne[nom_dns]\n</TD>\n<TD>\n<IMG ALIGN=MIDDLE SRC=\"ICONES/ordi_lock.png\"> Cet ordinateur est en consultation depuis $ip_verrouillante\n </TD>\n");
			}
			elseif ($est_indisponible)
			{
				print("<TD>\n$ligne[nom_dns]\n</TD>\n<TD>\n<IMG ALIGN=MIDDLE SRC=\"ICONES/ordi_lock.png\"> Cet ordinateur est en état \"<I>$nom_long_etat[$etat_install]</I>\"\n </TD>\n");
			}
#			elseif (est_en_cours_d_install($ligne['nom_dns']))
#			{
#				print("<TD>\n$ligne[nom_dns]\n</TD>\n<TD>\n<IMG ALIGN=MIDDLE SRC=\"ICONES/ordi_lock.png\"> Cet ordinateur est en cours d'installation\n </TD>\n");
#			}
#			elseif (est_en_etat_package($ligne['nom_dns']))
#			{
#				print("<TD>\n$ligne[nom_dns]\n</TD>\n<TD>\n<IMG ALIGN=MIDDLE SRC=\"ICONES/ordi_lock.png\"> Cet ordinateur est en état <I>Création de packages</I>\n </TD>\n");
#			}
#			elseif (est_en_etat_idb($ligne['nom_dns']))
#			{
#				print("<TD>\n$ligne[nom_dns]\n</TD>\n<TD>\n<IMG ALIGN=MIDDLE SRC=\"ICONES/ordi_lock.png\"> Cet ordinateur est en état <I>Création d'images de base</I>\n </TD>\n");
#			}
#			elseif (est_en_etat_depannage($ligne['nom_dns']))
#			{
#				print("<TD>\n$ligne[nom_dns]\n</TD>\n<TD>\n<IMG ALIGN=MIDDLE SRC=\"ICONES/ordi_lock.png\"> Cet ordinateur est en état <I>depannage</I>\n </TD>\n");
#			}
			else # On peut travailler sur cet ordinateur
			{
				print("<TD>\n$ligne[nom_dns]\n</TD>\n<TD>\n <INPUT TYPE=RADIO NAME=\"nom_dns\" VALUE=\"$ligne[nom_dns]\">\n </TD>\n");
			}
		}
		# Cas creation/modification de groupe ou suppression ou packages ou idbs ou depannage : on peut choisir
		# plusieurs machines : donc CHECKBOX
		else
		{
			list($est_verrouille, $ip_verrouillante) = est_verrouille_pour_une_autre_IP($ligne['nom_dns']);
			list($est_indisponible, $etat_install) = est_dans_un_etat_install_bloquant($ligne['nom_dns']);
			if ($est_verrouille)
			{
				print("<TD>\n$ligne[nom_dns]\n</TD>\n<TD>\n<IMG ALIGN=MIDDLE SRC=\"ICONES/ordi_lock.png\"> Cet ordinateur est en consultation depuis $ip_verrouillante\n </TD>\n");
			}
			# ON empeche la selection des machines en etat install bloquant (depannage, idb, package, en_cours)
			# pour toute action sauf pour les manipulations de groupes... Exception à debattre...
			# elseif ($est_indisponible and $action != "creation_de_groupe" and $action != "modification_de_groupe")
			# Finalement, apres debat, on bloque aussi pour les manip. de groupes, pour des 
			# raisons de coherence...
			elseif ($est_indisponible)
			{
				print("<TD>\n$ligne[nom_dns]\n</TD>\n<TD>\n<IMG ALIGN=MIDDLE SRC=\"ICONES/ordi_lock.png\"> Cet ordinateur est en état \"<I>$nom_long_etat[$etat_install]</I>\"\n </TD>\n");
			}
#			elseif (est_en_cours_d_install($ligne['nom_dns']))
#			{
#				print("<TD>\n$ligne[nom_dns]\n</TD>\n<TD>\n<IMG ALIGN=MIDDLE SRC=\"ICONES/ordi_lock.png\"> Cet ordinateur est en cours d'installation\n </TD>\n");
#			}
#			# Dans le cas d'action = packages, on ne permet pas le choix des machines déjà en état packages
#			elseif ($action == "packages" and est_en_etat_package($ligne['nom_dns']))
#			{
#				print("<TD>\n$ligne[nom_dns]\n</TD>\n<TD>\n<IMG ALIGN=MIDDLE SRC=\"ICONES/ordi_lock.png\"> Cet ordinateur est déjà en état <I>création de packages</I>\n </TD>\n");
#			}
#			# Dans le cas d'action = packages, on ne permet pas le choix des machines en état idb
#			elseif ($action == "packages" and est_en_etat_idb($ligne['nom_dns']))
#			{
#				print("<TD>\n$ligne[nom_dns]\n</TD>\n<TD>\n<IMG ALIGN=MIDDLE SRC=\"ICONES/ordi_lock.png\"> Cet ordinateur est en état <I>création d'images de base</I>\n </TD>\n");
#			}
#			# Dans le cas d'action = packages, on ne permet pas le choix des machines en état depannage
#			elseif ($action == "packages" and est_en_etat_depannage($ligne['nom_dns']))
#			{
#				print("<TD>\n$ligne[nom_dns]\n</TD>\n<TD>\n<IMG ALIGN=MIDDLE SRC=\"ICONES/ordi_lock.png\"> Cet ordinateur est en état <I>depannage</I>\n </TD>\n");
#			}
#			# Dans le cas d'action = idbs, on ne permet pas le choix des machines déjà en état idb
#			elseif ($action == "idbs" and est_en_etat_idb($ligne['nom_dns']))
#			{
#				print("<TD>\n$ligne[nom_dns]\n</TD>\n<TD>\n<IMG ALIGN=MIDDLE SRC=\"ICONES/ordi_lock.png\"> Cet ordinateur est déjà en état <I>création d'images de base</I>\n </TD>\n");
#			}
#			# Dans le cas d'action = idbs, on ne permet pas le choix des machines en état packages
#			elseif ($action == "idbs" and est_en_etat_package($ligne['nom_dns']))
#			{
#				print("<TD>\n$ligne[nom_dns]\n</TD>\n<TD>\n<IMG ALIGN=MIDDLE SRC=\"ICONES/ordi_lock.png\"> Cet ordinateur est en état <I>création de packages</I>\n </TD>\n");
#			}
#			# Dans le cas d'action = idbs, on ne permet pas le choix des machines en état depannage
#			elseif ($action == "idbs" and est_en_etat_depannage($ligne['nom_dns']))
#			{
#				print("<TD>\n$ligne[nom_dns]\n</TD>\n<TD>\n<IMG ALIGN=MIDDLE SRC=\"ICONES/ordi_lock.png\"> Cet ordinateur est en état <I>depannage</I>\n </TD>\n");
#			}
#		#	# Dans le cas d'action = depannage, on ne permet pas le choix des machines déjà en état depannage
#		#	elseif ($action == "depannage" and est_en_etat_depannage($ligne[nom_dns]))
#		#	{
#		#		print("<TD>\n$ligne[nom_dns]\n</TD>\n<TD>\n<IMG ALIGN=MIDDLE SRC=\"ICONES/ordi_lock.png\"> Cet ordinateur est déjà en état <I>dépannage</I>\n </TD>\n");
#		#	}
#		#	# Dans le cas d'action = depannage, on ne permet pas le choix des machines en état packages
#		#	elseif ($action == "depannage" and est_en_etat_package($ligne[nom_dns]))
#		#	{
#		#		print("<TD>\n$ligne[nom_dns]\n</TD>\n<TD>\n<IMG ALIGN=MIDDLE SRC=\"ICONES/ordi_lock.png\"> Cet ordinateur est en état <I>création de packages</I>\n </TD>\n");
#		#	}
#		#	# Dans le cas d'action = depannage, on ne permet pas le choix des machines en état idb
#		#	elseif ($action == "depannage" and est_en_etat_idb($ligne[nom_dns]))
#		#	{
#		#		print("<TD>\n$ligne[nom_dns]\n</TD>\n<TD>\n<IMG ALIGN=MIDDLE SRC=\"ICONES/ordi_lock.png\"> Cet ordinateur est en état <I>création d'images de base</I>\n </TD>\n");
#		#	}
			else # On peut travailler sur cet ordinateur
			{
				# Si modification_de_groupe
				# On verifie que le tableau $ordinateurs_isoles est non vide (ca peut
				# arriver si on modifie un groupe qu'on vient de vider, non pas en lui
				# enlevant les machines via modifier_groupe car le groupe vide n'y est
				# pas tolere, mais en supprimant carrément les machines qu'il contient via
				# supprimer machine, par exemple pour un changement de PC dans une salle 
				# avec mise au rebut de tous les anciens...) pour eviter
				# une chiee de warning incommodants...
				if ($action=="modification_de_groupe" and !empty($ordinateurs_isoles))
				{
						in_array($ligne['nom_dns'], $ordinateurs_isoles) ? $coche = "CHECKED" : $coche = "";
				}
				else
				{
					$coche = "";
				}
				print("<TD>\n$ligne[nom_dns]\n</TD>\n<TD>\n <INPUT TYPE=CHECKBOX NAME=\"checked[$nb_ordinateurs]\" VALUE=\"$ligne[nom_dns]\" $coche>\n </TD>\n");
			}
		}
		print("</TR>\n");
	}
	print("<INPUT TYPE=HIDDEN NAME=\"nb_ordinateurs\" VALUE=$nb_ordinateurs>\n");
	mysql_free_result($result);
	FinTable();

	# FIN creation des entrées "ordinateurs" dans le formulaire
	
	# Création de la partie "groupe" du formulaire

	if ($action=="creation_de_groupe")
	{
		EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
		print("<TR><TD>Nom de ce nouveau groupe</TD><TD>: <INPUT TYPE=TEXT NAME=nom_groupe SIZE=50></TD></TR>");
		print("<TR><TD>Description </TD><TD>: <INPUT TYPE=TEXT NAME=description_groupe SIZE=50></TD></TR>");
		print("<TR><TD>Photo </TD><TD>: <INPUT TYPE=TEXT NAME=photo SIZE=50></TD></TR>");
		FinTable();
	}
	if ($action=="modification_de_groupe")
	{
		$request="SELECT * from groupes WHERE nom_groupe=\"$nom_groupe\"";
		$result=mysql_query($request);
		$ligne = mysql_fetch_array($result);
		EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
		print("<TR><TD>Nom groupe</TD><TD>: <INPUT TYPE=TEXT NAME=nom_groupe VALUE=\"$ligne[nom_groupe]\" SIZE=50></TD></TR>");
		print("<TR><TD>Description </TD><TD>: <INPUT TYPE=TEXT NAME=description_groupe VALUE=\"$ligne[description_groupe]\" SIZE=50></TD></TR>");
		print("<TR><TD>Photo </TD><TD>: <INPUT TYPE=TEXT NAME=photo VALUE=\"$ligne[photo]\" SIZE=50></TD></TR>");
		# Utile pour le traitement si par hasard il vient l'envie au JeDDLaJeur de changer le nom du groupe...
		print("<INPUT TYPE=HIDDEN NAME=\"ancien_nom_du_groupe\" VALUE=\"$nom_groupe\">\n");
		mysql_free_result($result);
		FinTable();
	}

	# FIN Création de la partie "groupe" du formulaire

	print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
	FinFormulaire();

	# FIN B. Génération du formulaire de choix des machines adapté selon $action (modif machine, creation groupe, modif groupe...)
}
print("<BR><HR><P><CENTER><A HREF=accueil.php TARGET=\"_top\">Retour</A></CENTER></P>\n");
DisconnectMySQL();
PiedPage();
//FIN Main()

?>
