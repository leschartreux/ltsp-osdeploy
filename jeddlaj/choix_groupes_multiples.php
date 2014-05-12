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
if (isset( $_POST['deja_passe_dans_choix_groupes'])) {$deja_passe_dans_choix_groupes = $_POST['deja_passe_dans_choix_groupes'];}

if (isset( $_POST['action'])) {$action = $_POST['action']; }
if (!isset($action)) { $action = $_GET['action']; }

if (isset( $_POST['checked'])) {$checked = $_POST['checked']; }
if (isset( $_POST['nom_groupe'])) {$nom_groupe = $_POST['nom_groupe']; }
if (isset( $_POST['nb_groupes'])) {$nb_groupes = $_POST['nb_groupes']; }
# toutes les variables ont ete recuperees

include("UtilsHTML.php");
include("UtilsMySQL.php");
include("UtilsJeDDLaJ.php");
include("Utils.php");

# Main()
entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Choix groupes ($action)");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);
print("<CENTER><H1>Choix groupes ($action)</H1></CENTER>\n");

# On a déjà effectué un choix de groupes
if (isset($deja_passe_dans_choix_groupes))
{

	###################################################################################################################################################################
	# L'enjeu de ce if est, ayant choisi lors d'un premier passage dans choix_groupes multiples un (action = modif de groupe) 
	# ou plusieurs (action = suppression) groupes sur lesquels agir, de réaliser les actions voulues :
	#	- Dans le cas suppression, on va effectuer les actions de suppression dans la base.
	#	- Dans le cas modification, on va proposer un formulaire permettant de modifier les groupes inclus dans le groupes en cours de modification
	#	puis on va appeler choix_machines_multiples pour proposer un formulaire des machines isolées à intégrer dans le groupe en cours de 
	#	modification.
	###################################################################################################################################################################


	if ($action=="modification_de_groupe")
	{
		if (!isset($nom_groupe)) # Pas de groupe choisi : sanction... 
		{
			print ("<P><I><FONT COLOR=RED>ATTENTION : Vous n'avez choisi aucun groupe...</FONT>. Utilisez le bouton <TT>BACK</TT> de votre navigateur pour faire une sélection valide.</I></P>");
		}
		else # OK : on a un groupe, on continue...
		{
			print("<H2>Modification des groupes inclus dans le groupe $nom_groupe</H2>");
			# On commence par récupérer les groupes inclus dans $nom_groupe
			################################################################
			# Si on veut afficher cochés tous les groupes transitivement inclus
			#$groupes_inclus = array_unique(groupes_inclus($nom_groupe));
			################################################################
			##################################################################
			# On choisit de n'afficher cochés que les groupe directement inclus
			$groupes_inclus = groupes_inclus_direct($nom_groupe);
			##################################################################
		#	Debug("nom_groupe");
		#	foreach ($groupes_inclus as $gpe)
		#	{
		#		Debug("gpe");
		#	}
		#	DebugTab("groupes_inclus");
			empty($groupes_inclus) ? $il_y_a_des_groupes_inclus = 0 : $il_y_a_des_groupes_inclus = 1;
		#	Debug("il_y_a_des_groupes_inclus");
			# On va proposer tous les groupes (sauf tous les ordinateurs) avec les groupes inclus chéckés...
			# On ne propose pas le groupe tous les ordinateurs car il ne doit pas pouvoir être modifié,
			# ni le groupe nom_groupe car il ne peut pas s'inclure lui-même...
			$request="SELECT nom_groupe FROM groupes WHERE nom_groupe<>\"tous les ordinateurs\" and nom_groupe<>\"$nom_groupe\"";
			$result=mysql_query($request);
			EnteteFormulaire("POST","choix_machines_multiples.php");
			print("<INPUT TYPE=HIDDEN NAME=action VALUE=\"$action\">\n");
			print("<INPUT TYPE=HIDDEN NAME=nom_groupe VALUE=\"$nom_groupe\">\n");
			EnteteTable("BORDER=2 CELLPADDING=2 CELLSPACING=1");
			$groupes_incluant = groupes_incluant($nom_groupe);
			$nb_groupes = 0;
			while ($ligne = mysql_fetch_array($result)) {
					# On ne propose pas non plus les groupes incluant $nom_groupe pour éviter A inclut B et B inclut A...
					if (!in_array($ligne['nom_groupe'], $groupes_incluant))
					{
						$nb_groupes++;
						print("<TR>\n");
						# On coche les groupes inclus s'il y en a...
						$coche = "";
						if ($il_y_a_des_groupes_inclus)
						{
							if (in_array($ligne['nom_groupe'], $groupes_inclus)) { $coche = "CHECKED"; }
						}
						print("<TD>\n$ligne[nom_groupe]\n</TD>\n<TD>\n <INPUT TYPE=CHECKBOX NAME=\"checked[$nb_groupes]\" VALUE=\"$ligne[nom_groupe]\" $coche>\n </TD>\n");
						print("</TR>\n");
					}
			}
			print("<INPUT TYPE=HIDDEN NAME=\"nb_groupes\" VALUE=$nb_groupes>\n");
			mysql_free_result($result);
			FinTable();
			# Si on ne propose aucun groupe (cas tous sont incluant et aucun groupes inclus), la fenêtre est très vide
			# Une explication s'impose... 
			if ($nb_groupes == 0)
			{
				print("<P><I>Aucun groupe dans la base n'est candidat pour être inclus dans <TT>$nom_groupe</TT> (ne sont proposés ici que les groupes qui n'incluent pas <TT>$nom_groupe</TT>, i.e. les groupes disjoints et les groupes inclus). Cliquez <TT>Valider</TT> pour modifier les ordinateurs isolés constituant <TT>$nom_groupe</TT>...</I></P>");
			}
			print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
			FinFormulaire();
		}
	}# end if($action==modification_de_groupe)

	if ($action=="suppression")
	{
		# Le nombre de groupes choisis est non nul
		if (count($checked) > 0)
		{
			$nb_groupes_concernes=0;
			$liste_groupes_concernes="";
			for($i=1;$i<=$nb_groupes;$i++)
			{
				if (isset($checked[$i]))
				{
					$nb_groupes_concernes++;
					$groupes_concernes[$nb_groupes_concernes] = $checked[$i];
					$liste_groupes_concernes .= $groupes_concernes[$nb_groupes_concernes]." ";
				}
			}

			# Ici on va avoir un double traitement car le groupe a detruire peut exister dans la 
			# table gpe_est_inclus_dans_gpe comme groupe incluant (champ nom_groupe) ou comme
			# groupe inclus (champs nom_groupe_inclus)...
			# On initialise les clause WHERE
			$clause_where = $clause_where_groupe_inclus = " WHERE 1=0";
			for($i=1;$i<=$nb_groupes_concernes;$i++)
			{
				$clause_where .= " OR nom_groupe=\"$groupes_concernes[$i]\"";
				$clause_where_groupe_inclus .= " OR nom_groupe_inclus=\"$groupes_concernes[$i]\"";
			}
			# Les tables ou le champ nom_groupe apparait, hors relation d'inclusion
			$tables_concernees_hors_inclusion = array("groupes", "ord_appartient_a_gpe");
			# Les tables ou nom_groupe et nom_groupe_inclus apparaissent, i.e. relation d'inclusion
			$tables_concernees_inclusion_groupes = array("gpe_est_inclus_dans_gpe");
			# On detruit les enregistrements des groupes concernes dans les
			# tables concernees hors relation d'inclusion (groupe dans champ nom_groupe)
			foreach ($tables_concernees_hors_inclusion as $table)
			{
				$request="DELETE FROM $table".$clause_where;
				mysql_query($request);
				printf ("<P><I>Table <TT>$table</TT> : Destruction des %d enregistrements relatifs à <TT>$liste_groupes_concernes</TT>. </I></P>\n", mysql_affected_rows());
			}
			# On detruit les enregistrements des groupes concernes dans les
			# tables concernees dans la relation d'inclusion 
			# (groupe dans champ nom_groupe OU nom_groupe_inclus)
			foreach ($tables_concernees_inclusion_groupes as $table)
			{
				# On detruit les lignes ou le groupe apparait dans le champ nom_groupe i.e.
				# la ou il est un groupe INCLUANT
				$request="DELETE FROM $table".$clause_where;
				mysql_query($request);
				printf ("<P><I>Table <TT>$table</TT> : Destruction des %d enregistrements relatifs à <TT>$liste_groupes_concernes</TT> en tant que groupes incluants. </I></P>\n", mysql_affected_rows());
				# On detruit maintenant les lignes ou le groupe apparait dans le champ nom_groupe_inclus
				# i.e. la ou il est un groupe INCLUS
				$request="DELETE FROM $table".$clause_where_groupe_inclus;
				mysql_query($request);
				printf ("<P><I>Table <TT>$table</TT> : Destruction des %d enregistrements relatifs à <TT>$liste_groupes_concernes</TT> en tant que groupes inclus. </I></P>\n", mysql_affected_rows());
			}
			# Il y a aussi la table postinstall_scripts qu'il faut UPDATER pour les scripts qui s'appliquaient aux groupes à supprimer
			$postinst_scripts_updates = 0;
			for($i=1;$i<=$nb_groupes_concernes;$i++)
			{
				$request = "UPDATE postinstall_scripts SET applicable_a=\"rien_pour_l_instant\", valeur_application=\"\" WHERE applicable_a=\"nom_groupe\" AND valeur_application=\"$groupes_concernes[$i]\"";
				mysql_query($request);
				$postinst_scripts_updates += mysql_affected_rows();
			}
			if ($postinst_scripts_updates>0)
			{
				printf ("<P><I>Table <TT>postinstall_scripts</TT> : Modification des %d enregistrements relatifs à <TT>$liste_groupes_concernes</TT>. </I></P>\n", $postinst_scripts_updates);
			}
		}# end if(count($checked) > 0)
		else # on n'a pas de groupe : sanction...
		{
			print ("<P><I><FONT COLOR=RED>ATTENTION : Vous n'avez choisi aucun groupe...</FONT>. Utilisez le bouton <TT>BACK</TT> de votre navigateur pour faire une sélection valide.</I></P>");
		}
	}# end if($action==suppression)
}# end if(isset(deja_passe...
else # Choix du groupe pas encore effectué...
{

	#########################################################################################################################################
	# L'enjeu de ce else qui correspond au premier passage dans choix_groupes_multiples est de générer et afficher le formulaire 
	# adapté selon $action (creation/modif/suppression groupe ou modififcation_machines_nombreuses)
	# 	Pour la modification et la suppression de groupe, on choisit par ce formulaire le (modification) ou les (suppression)
	# groupes sur lesquels on va agir. On renvoie ensuite sur choix_groupes_multiples avec la variable deja_passe_dans_choix_groupes
	# passée par POST avec la valeur 1. C'est là que seront effectuées efectivement les suppressions dans la base dans le cas suppression.
	# Dans le cas modification, c'est là qu'on proposera un formulaire de choix des groupes à inclure dans le groupe en cours de modification
	# en prenant soin de précocher les groupes inclus AVANT modification...
	# 	Pour la création, ce formulaire est utilisé pour choisir les groupes à inclure dans le nouveau groupe en cours de création.
	# On renvoie ensuite sur choix_machines_multiples afin que l'on choisisse les machines isolées du nouveau groupe.
	#	Pour la modification_machines_nombreuses, le formulaire sert à selectionner un ou plusieurs groupes desquels on modifiera toutes
	# les machines. On envoie vers modifier_machines_nombreuses.php, ou on fera le travail.
	#########################################################################################################################################

	
	# On ne propose pas le groupe tous les ordinateurs car il ne doit pas pouvoir être modifié...	
	$request="SELECT nom_groupe FROM groupes WHERE nom_groupe<>\"tous les ordinateurs\"";
	$result=mysql_query($request);
	# Pour le cas de la création de groupe, on donne un peu plus d'infos car l'apparition de la liste des groupes à ce moment-là
	# peut surprendre...
	if ($action=="creation_de_groupe")
	{
		print("<H2>Sélection des groupes à inclure dans le nouveau groupe</H2>");
		# Cette remarque n'est bienvenue que s'il y a des groupes dans la base...
		if (mysql_affected_rows() != 0)
		{
			print("<I><FONT SIZE=-1>Si vous ne souhaitez inclure que des machines isolées, ne sélectionnez rien et choisissez OK</FONT></I><BR><BR>");
		}
	}
	switch ($action)
	{
		case "modification_de_groupe":
		case "suppression":
			EnteteFormulaire("POST","choix_groupes_multiples.php");
		case "creation_de_groupe":
			EnteteFormulaire("POST","choix_machines_multiples.php");
		case "modification_machines_nombreuses":
			EnteteFormulaire("POST","modifier_machines_nombreuses.php");
	}
	print("<INPUT TYPE=HIDDEN NAME=action VALUE=\"$action\">\n");
	print("<INPUT TYPE=HIDDEN NAME=\"deja_passe_dans_choix_groupes\" VALUE=1>\n");
	
	EnteteTable("BORDER=2 CELLPADDING=2 CELLSPACING=1");
	$nb_groupes = 0;
	while ($ligne = mysql_fetch_array($result)) {
		$nb_groupes++;
		print("<TR>\n");
		# On n'affiche pas les groupes qui comportent au moins une machine en état différent de "installé" ou "modifie"
		$selectionnable = (groupe_selectionnable_pour_suppression_ou_modification($ligne['nom_groupe']));
		if (!$selectionnable)
		{
			$request="SELECT A.nom_dns, etat_install FROM ordinateurs AS A, ord_appartient_a_gpe as B WHERE A.nom_dns=B.nom_dns AND nom_groupe='$ligne[nom_groupe]' AND etat_install&lt;&gt;'installe' AND etat_install&lt;&gt;'modifie'";
			print("<TD>\n$ligne[nom_groupe]\n</TD>\n<TD>\n <IMG ALIGN=MIDDLE SRC=\"ICONES/ordi_lock.png\"><A HREF=\"Interro.php?request=$request\">L'état de certaines machines interdit la sélection de ce groupe\n</A> </TD>\n</TD>\n");
		}
		else
		{
			# Cas modif d'un groupe : pour l'instant qu'un a la fois
			# donc RADIO
			if ($action == "modification_de_groupe")
			{
				if ($selectionnable)
				{
					print("<TD>\n$ligne[nom_groupe]\n</TD>\n<TD>\n <INPUT TYPE=RADIO NAME=\"nom_groupe\" VALUE=\"$ligne[nom_groupe]\">\n </TD>\n");
				}
				else
				{
					print("<TD>\n$ligne[nom_groupe]\n</TD>\n<TD>\n <IMG ALIGN=MIDDLE SRC=\"ICONES/ordi_lock.png\"> Ce groupe contient au moins une machine en état <I>en_cours, modifié, idb, package</I>\n </TD>\n</TD>\n");
				}
			}
			# Cas supppression ou creation de groupe ou modification_machines_nombreuses : on peut choisir
			# plusieurs groupes : donc CHECKBOX
			else
			{
				print("<TD>\n$ligne[nom_groupe]\n</TD>\n<TD>\n <INPUT TYPE=CHECKBOX NAME=\"checked[$nb_groupes]\" VALUE=\"$ligne[nom_groupe]\">\n </TD>\n");
			}
			print("</TR>\n");
		}
	}
	print("<INPUT TYPE=HIDDEN NAME=\"nb_groupes\" VALUE=$nb_groupes>\n");
	mysql_free_result($result);
	FinTable();
	if ($nb_groupes == 0)
	{
		print("<P><I>Pour l'instant, <FONT COLOR=RED>aucun groupe n'est défini dans votre base JeDDLaJ</FONT>...</I></P>");
		switch ($action)
		{
			case "modification_de_groupe":
				print("<P><I>Donc, rien à modifier ici pour l'instant...</I></P>");
				break;
			case "suppression":
				print("<P><I>Donc, rien à supprimer ici pour l'instant...</I></P>");
				break;
			case "creation_de_groupe":
				print("<P><I>Donc, aucun groupe à inclure pour l'instant... Cliquez <TT>Valider</TT> pour choisir les ordinateurs isolés constituant votre premier groupe.</I></P>");
			case "modification_machines_nombreuses":
				print("<P><I>Donc, impossible d'utiliser la methode de sélection par groupe pour choisir les machines à modifier...</I></P>");
				break;
		}
	}
	print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
	FinFormulaire();
}
print("<BR><HR><P><CENTER><A HREF=accueil.php TARGET=\"_top\">Retour</A></CENTER></P>\n");
DisconnectMySQL();
PiedPage();
//FIN Main()

?>
