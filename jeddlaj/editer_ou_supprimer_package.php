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
include("UtilsMySQL.php");
include("UtilsJeDDLaJ.php");
include("Utils.php");

# On recupere les variables
if (isset( $_POST['action'])) {$action = $_POST['action']; }
if (!isset($action)) { $action = $_GET['action']; }
if (isset( $_POST['mode'])) {$mode = $_POST['mode']; }
if (!isset($mode)) { $mode = $_GET['mode']; }
# toutes les variables ont ete recuperees

$mode == "edition" ? entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Édition package ($action)") : entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Suppression package ($action)");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);
$mode == "edition" ? print("<CENTER><H1>Édition de packages</H1></CENTER>\n") : print("<CENTER><H1>Suppression de packages</H1></CENTER>\n");

switch ($action)
{
	case "ChoixOS":
		EnteteFormulaire("POST","editer_ou_supprimer_package.php?action=ChoixLogiciel&mode=$mode");
		$mode == "edition" ? $verbe = "éditer" : $verbe = "supprimer";
		print("<FONT COLOR=BLUE SIZE=+1>OS associé au package à $verbe :</I> </FONT> <SELECT NAME=nom_os>\n");
		foreach ($GLOBALS['oss'] as $os)
		{
		    print("<OPTION VALUE=\"$os\">$os</OPTION>\n");
		}
		print("</SELECT>\n");
		print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
		FinFormulaire();
		break;
	case "ChoixLogiciel":
		# On recupere les variables
		$nom_os = $_POST["nom_os"];
		# toutes les variables ont ete recuperees
		# On veut, dans la table logiciels, seulement ceux qui correspondent à des logiciels, et non à des OS, donc on ne prend 
		# que ceux dont l'id apparaît dans la table packages (car un package est associé à un "vrai logiciel", pas un OS...)
		$request="SELECT a.id_logiciel, nom_logiciel, version, icone FROM logiciels AS a, packages AS b WHERE a.id_logiciel=b.id_logiciel AND nom_os=\"$nom_os\" GROUP BY id_logiciel ORDER BY nom_logiciel,version";
		$result=mysql_query($request);
		$mode == "edition" ? $verbe = "éditer" : $verbe = "supprimer";
		print("<H2>Sélectionnez le logiciel associé au package à $verbe :</H2>\n");
		EnteteFormulaire("POST","editer_ou_supprimer_package.php?action=ChoixPack&mode=$mode");
		print("<INPUT TYPE=HIDDEN NAME=nom_os VALUE=\"$nom_os\">\n");
		EnteteTable("BORDER=2 CELLPADDING=2 CELLSPACING=1");
		while ($ligne = mysql_fetch_array($result))
		{
			empty($ligne['version']) ? $version="non spécifiée" : $version=$ligne['version'];
			print("<TR>\n");
			print("<TD>\n<IMG ALIGN=CENTER WIDTH=\"$largeur_image_logiciel_et_package\" HEIGHT=\"$hauteur_image_logiciel_et_package\" SRC=\"ICONES/$ligne[icone]\">\n</TD><TD>\n$ligne[nom_logiciel], version $version\n</TD>\n<TD>\n <INPUT TYPE=RADIO NAME=\"id_logiciel\" VALUE=\"$ligne[id_logiciel]\">\n </TD>\n");
			print("</TR>\n");
		}
		mysql_free_result($result);
		FinTable();
		print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
		FinFormulaire();
		break;
	case "ChoixPack":
		# On recupere les variables
		$id_logiciel = $_POST["id_logiciel"];
		$nom_os = $_POST["nom_os"];
		# toutes les variables ont ete recuperees
		if (!isset($id_logiciel))
		{
			print ("<P><I><FONT COLOR=RED>ATTENTION : Vous n'avez choisi aucun logiciel</FONT>. Utilisez le bouton <TT>BACK</TT> de votre navigateur pour faire une sélection valide.</I></P>");
		}
		else
		{
	        	$request="SELECT nom_logiciel, version, icone FROM logiciels WHERE id_logiciel=\"$id_logiciel\"";
			$result=mysql_query($request);
			$ligne = mysql_fetch_array($result);
			mysql_free_result($result);
			$nom_logiciel = $ligne['nom_logiciel'];
			empty($ligne['version']) ? $version="non spécifiée" : $version=$ligne['version'];
			print("<H2>Logiciel</H2>");
			EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
			print("<TR><TD><IMG ALIGN=CENTER WIDTH=\"$largeur_image_logiciel_et_package\" HEIGHT=\"$hauteur_image_logiciel_et_package\" SRC=\"ICONES/$ligne[icone]\"></TD><TD><I>$nom_logiciel, version $version</I></TD></TR>\n");
			FinTable();
	        	$request="SELECT * FROM packages WHERE id_logiciel=\"$id_logiciel\" ORDER BY nom_package";
			$result=mysql_query($request);
			$nb_packages = mysql_num_rows($result);
			# Un "s" à Package que s'il y en a plus d'un (ou 0, car à ce moment-là on parle de la potentialité d'existence
			# de plusieurs packages...) : on ne badine pas avec l'orthographe...
			($nb_packages > 1 OR $nb_packages==0) ? $pas_de_faute = "Packages associés" : $pas_de_faute = "Package associé";
			print("<H2>$pas_de_faute à cette distribution</H2>");
			if ($nb_packages > 0)
			{
				$mode == "edition" ? EnteteFormulaire("POST","editer_ou_supprimer_package.php?action=EditPack&mode=$mode") : EnteteFormulaire("POST","editer_ou_supprimer_package.php?action=Validation&mode=$mode");
				print("<INPUT TYPE=HIDDEN NAME=nom_os VALUE=\"$nom_os\">\n");
				EnteteTable("BORDER=2 CELLPADDING=2 CELLSPACING=1");
				$i = 1;
				while ($ligne = mysql_fetch_array($result))
				{
					print("<TR>\n");
					print("<TD>\n<FONT COLOR=RED>$ligne[nom_package]</FONT>, <I>spécificité</I> <FONT COLOR=RED>$ligne[specificite]</FONT>");
					if ($ligne['specificite'] != "aucune") {print("<I> de valeur</I> <FONT COLOR=RED>".$ligne['valeur_specificite']."</FONT>");}
					$ligne['repertoire'] == '' ? $repertoire_a_afficher = ", sans répertoire spécifié" : $repertoire_a_afficher = ", répertoire <FONT COLOR=RED>".$ligne['repertoire']."</FONT>";
					print("$repertoire_a_afficher");
					if ($mode == "edition")
					{
						# Edition : un seul package à la fois donc RADIO
						print("\n</TD>\n<TD>\n <INPUT TYPE=RADIO NAME=\"id_package\" VALUE=\"$ligne[id_package]\">\n </TD>\n");
					}
					else
					{
						# Suppression : eventuellement plusieurs packages à la fois donc CHECKBOX
						print("\n</TD>\n<TD>\n <INPUT TYPE=CHECKBOX NAME=\"checked[".$i++."]\" VALUE=\"$ligne[id_package]\">\n </TD>\n");
					}
					print("</TR>\n");
				}
				mysql_free_result($result);
				FinTable();
				print("<INPUT TYPE=HIDDEN NAME=id_logiciel VALUE=\"$id_logiciel\">\n");
				if ($mode == "suppression") 
				{
					# nom_logiciel et version serviront a afficher le logiciel dans le bilan lors de la 
					# validation de la suppression ; on evite ainsi une requete dans la table
					# logiciels juste pour aller chercher le nom et la version. 
					# On aura besoin du nom si on n'a plus de packages associes a un 
					# logiciel, cas dans lequel on detruit aussi le logiciel, une information
					# qu'il est bon d'afficher...
					# Dans le cas edition, nom_logiciel est inutile puisqu'un requete est de 
					# toutes les facons necessaires pour aller chercher la version, l'icone, etc.
					print("<INPUT TYPE=HIDDEN NAME=nom_logiciel VALUE=\"$nom_logiciel\">\n");
					print("<INPUT TYPE=HIDDEN NAME=version VALUE=\"$version\">\n");
					print("<INPUT TYPE=HIDDEN NAME=nb_packages VALUE=\"$nb_packages\">\n");
				}
				print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
				FinFormulaire();
			}
			else
			{
				switch ($mode)
				{
					case "edition" :
						print("<I><FONT COLOR=RED>Pas de packages associé au logiciel <B>$nom_logiciel, version $version</B></FONT>... 3 choix s'offrent donc à vous :");
						print("<OL>");
						print("<LI>Utilisez le bouton <B>Précédent/Back</B> de votre navigateur pour <FONT COLOR=GREEN>choisir un autre logiciel</FONT>,");
						print("<LI>ou le bouton <B>valider</B> ci-dessous pour <FONT COLOR=GREEN>ajouter un package au logiciel <B>$nom_logiciel, version $version</B></FONT>,");
						print("<LI>ou encore le bouton <B>Retour</B> en bas de cette page pour <FONT COLOR=GREEN>faire une toute autre opération JeDDLaJique</FONT> (dans ce dernier cas, il semble utile de vous interroger sur votre motivation pour le travail aujourd'hui...)</I>");
						print("</OL>");
						EnteteFormulaire("POST","ajouter_package.php?action=NewPackageOldSoftAddPack");
						print("<INPUT TYPE=HIDDEN NAME=id_logiciel VALUE=\"$id_logiciel\">\n");
						print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
						FinFormulaire();
						break;
					case "suppression" :
						print("<I><FONT COLOR=RED>Pas de packages associé au logiciel <B>$nom_logiciel, version $version</B></FONT>... 2 choix s'offrent donc à vous :");
						print("<OL>");
						print("<LI>Utilisez le bouton <B>Précédent/Back</B> de votre navigateur pour <FONT COLOR=GREEN>choisir un autre logiciel</FONT>,");
						print("<LI>ou encore le bouton <B>Retour</B> en bas de cette page pour <FONT COLOR=GREEN>faire une toute autre opération JeDDLaJique</FONT> (dans ce dernier cas, il semble utile de vous interroger sur votre motivation pour le travail aujourd'hui...)</I>");
						print("</OL>");
				}
			}
		}
		break;
	case "EditPack":
		# On recupere les variables
		$nom_os = $_POST["nom_os"];
		$id_package = $_POST["id_package"];
		$id_logiciel = $_POST["id_logiciel"];
		# toutes les variables ont ete recuperees
		if (!isset($id_package))
		{
			print ("<P><I><FONT COLOR=RED>ATTENTION : Vous n'avez choisi aucun package</FONT>. Utilisez le bouton <TT>BACK</TT> de votre navigateur pour faire une sélection valide.</I></P>");
		}
		else
		{
	        	$request1="SELECT id_logiciel, nom_logiciel, version, icone FROM logiciels WHERE id_logiciel=\"$id_logiciel\"";
			$result1=mysql_query($request1);
			$ligne1 = mysql_fetch_array($result1);
			empty($ligne1['version']) ? $version="non spécifiée" : $version=$ligne1['version'];
			mysql_free_result($result1);
			print("<H2>Logiciel</H2>");
			EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
			print("<TR><TD><IMG ALIGN=CENTER WIDTH=\"$largeur_image_logiciel_et_package\" HEIGHT=\"$hauteur_image_logiciel_et_package\" SRC=\"ICONES/$ligne1[icone]\"></TD><TD><I>$ligne1[nom_logiciel], version $version</I></TD></TR>\n");
			FinTable();
			print("<H2>Package</H2>");
	        	$request2="SELECT * FROM packages WHERE id_package=\"$id_package\"";
			$result2=mysql_query($request2);
			$ligne2 = mysql_fetch_array($result2);
			mysql_free_result($result2);
			EnteteFormulaire("POST","editer_ou_supprimer_package.php?action=Validation&mode=$mode");
			print("<INPUT TYPE=HIDDEN NAME=id_logiciel VALUE=\"$ligne2[id_logiciel]\">\n");
			print("<INPUT TYPE=HIDDEN NAME=nom_logiciel VALUE=\"$ligne1[nom_logiciel]\">\n");
			print("<INPUT TYPE=HIDDEN NAME=version VALUE=\"$ligne1[version]\">\n");
			print("<INPUT TYPE=HIDDEN NAME=id_package VALUE=\"$ligne2[id_package]\">\n");
			print("<INPUT TYPE=HIDDEN NAME=nom_os VALUE=\"$nom_os\">\n");
			EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
			print("<TR><TD><I>Nom package</I> </TD><TD>: <INPUT TYPE=TEXT NAME=nom_package SIZE=50 VALUE=\"$ligne2[nom_package]\"></TD></TR>\n");
			print("<TR><TD><I>Répertoire</I> </TD><TD>: <FONT SIZE=-1 COLOR=GREEN><I>$RemboPackagesDir</I></FONT><INPUT TYPE=TEXT NAME=repertoire SIZE=50 VALUE=\"$ligne2[repertoire]\"></TD></TR>\n");
			print("<TR><TD><I>Spécificité</I> </TD><TD>: <SELECT NAME=specificite>\n");
			$ligne2['specificite'] == "aucune" ? $selected_aucune = "SELECTED" : $selected_aucune = "";
			$ligne2['specificite'] == "nom_dns" ? $selected_nom_dns = "SELECTED" : $selected_nom_dns = "";
			$ligne2['specificite'] == "signature" ? $selected_signature = "SELECTED" : $selected_signature = "";
			$ligne2['specificite'] == "id_composant" ? $selected_id_composant = "SELECTED" : $selected_id_composant = "";
			print("<OPTION $selected_aucune VALUE=\"aucune\">aucune</OPTION>\n");
			print("<OPTION $selected_nom_dns VALUE=\"nom_dns\">nom_dns</OPTION>\n");
			print("<OPTION $selected_signature VALUE=\"signature\">signature</OPTION>\n");
			print("<OPTION $selected_id_composant VALUE=\"id_composant\">id_composant</OPTION>\n");
			print("</SELECT>\n");
			print("</TD></TR>\n");
			print("<TR><TD><I>Valeur Spécificité</I> </TD><TD>: <INPUT TYPE=TEXT NAME=valeur_specificite SIZE=50 VALUE=\"$ligne2[valeur_specificite]\"></TD></TR>\n");
			FinTable();
			print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
			FinFormulaire();
		}
		break;
	case "Validation":
		# On recupere les variables
		$id_logiciel = $_POST["id_logiciel"];
		$nom_logiciel = $_POST["nom_logiciel"];
		empty($_POST['version']) ? $version="non spécifiée" : $version=$_POST['version'];
		if ($mode == "edition")
		{
			$id_package = $_POST["id_package"];
			$nom_os = $_POST["nom_os"];
			$nom_package = $_POST["nom_package"];
			$repertoire = $_POST["repertoire"];
			# On ajoute un slash final au chemin s'il n'y est pas déjà, sauf si répertoire est vide 
			# sinon on aurait $RemboPackagesDir/ (donc deux slashes finaux...)
			if (substr($repertoire,-1) != "/" and $repertoire != "") {$repertoire .= "/";}
			$specificite = $_POST["specificite"];
			$valeur_specificite = $_POST["valeur_specificite"];
		}
		if ($mode == "suppression")
		{
			$nb_packages = $_POST["nb_packages"];
			$checked = $_POST["checked"];
		}
		# toutes les variables ont ete recuperees
		# On attaque la base
		switch ($mode)
		{
			case "edition" :
				# On ne veut pas de fichiers de meme nom dans le meme repertoire en dehors du cas ou ce sont des entrees multiples 
				# pour le meme logiciel, i.e. mêmes id_logiciel ET specificites identiques ET valeurs specificite differentes. 
				# La requete suivante exprime les cas interdits ET les compte : on ne veut donc editer que si la requete renvoie 0 
				$request = "SELECT COUNT(*) AS total FROM packages WHERE nom_package=\"$nom_package\" AND repertoire=\"$repertoire\" AND id_package <> \"$id_package\" AND (id_logiciel<>\"$id_logiciel\" OR specificite<>\"$specificite\" OR valeur_specificite=\"$valeur_specificite\")";
				# On ne veut pas, pour le meme logiciel, qu'il y ait plus d'un package non specifique OU qu'il y ait des packages specifiques 
				# de type de specificite differentes OU (de meme type de specificite ET de meme valeur specificite).
				# La requete suivante exprime ces cas interdits ET les compte : on ne veut donc editer que si la requete renvoie 0 
				$request2 = "SELECT COUNT(*) AS total FROM packages WHERE id_logiciel=\"$id_logiciel\" AND id_package <> \"$id_package\" AND (\"$specificite\"=\"aucune\" OR specificite=\"aucune\" OR specificite<>\"$specificite\" OR (specificite=\"$specificite\" AND valeur_specificite=\"$valeur_specificite\"))";

				$result = mysql_query($request);
				$result2 = mysql_query($request2);
				$line = mysql_fetch_array($result);
				$line2 = mysql_fetch_array($result2);
				$package_incompatible_meme_rep_deja_existant = ($line["total"] != 0);
				$package_incompatible_meme_distrib_deja_existant = ($line2["total"] != 0);
				mysql_free_result($result);
				mysql_free_result($result2);

				if ($repertoire=='') {$phrase_rep="dans le même répertoire";} else {$phrase_rep="dans le répertoire <FONT COLOR=RED>$repertoire</FONT>";}
				if ($package_incompatible_meme_rep_deja_existant)
				{
					print("<P><I><FONT COLOR = RED>ATTENTION DANGER : </FONT>Un AUTRE package de nom <FONT COLOR=RED>$nom_package</FONT> existe déjà $phrase_rep... Or, ceci n'est possible que si les packages sont attachées au même logiciel, ont le même type de spécifité mais une valeur de spécificité différente(*). Le cas présent ne correspond pas à cette situation... Veuillez utiliser le bouton Back/Précédent de votre navigateur pour modifier votre entrée.</I></P>\n");
					print("<P><FONT SIZE=-2>(*) Il s'agit alors d'entrées multiples pour le même package, ce qui est souhaitable dans certains cas : <BR> - le même type de machine avec des composants identiques mais placés différemment sur le bus peut avoir deux signatures différentes car l'algorithme de calcul de la signature Rembo dépend de l'ordre de la détection des composants sur le bus ;<BR> - un même package de spécificité id_composant peut parfois convenir pour plusieurs composants différents : par exemple le package d'un driver vidéo.</FONT></P>\n");
				}
				elseif ($package_incompatible_meme_distrib_deja_existant)
				{
					$phrase_valeur_specificite = ( ($specificite == "aucune" or empty($valeur_specificite)) ? "" : " de valeur <FONT COLOR=RED>".$valeur_specificite."</FONT>");
					print("<P><I><FONT COLOR = RED>ATTENTION DANGER : </FONT>le package que vous souhaitez ajouter (<FONT COLOR = RED>$nom_package</FONT>, spécificité <FONT COLOR = RED>$specificite</FONT>".$phrase_valeur_specificite.") présente des caractéristiques incompatibles avec au moins un des packages déjà existants associés au même logiciel (<FONT COLOR = RED>$nom_logiciel</FONT>, <FONT COLOR = RED>$version</FONT>). L'incompatibilité provient d'une des causes suivantes(*) :<BR>\n<UL>\n<LI>Arrgh : le package que vous ajoutez n'est pas spécifique ET il existe déjà un package non spécifique pour cette distribution(*) !!! Reculez impies, fuyez devant les préceptes JeDDLaJiques !!! ;</LI>\n<LI>Atroce malheur : le package que vous ajoutez est d'un type de spécifité différent de celui des packages déjà existants pour cette distribution !!! Inconscient !!! Vous risquez de réveiller des forces maléfiques dont vous ne pouvez appréhender la puissance !!! ;</LI>\n<LI>Horreur insondable : le package que vous ajoutez est du même type de spécifité et de même valeur de spécificité qu'un des packages déjà existants pour ce logiciel !!! Vous crachez à la face du Diable !!! Vous pissez face au vent au Cap Horn !!! Puisse la puissance de JeDDLaJ vous protéger contre tous les démons que vous défiez !!!</LI>\n</UL>\n");
					print("<P><FONT SIZE=-2>(*) Tous ces cas violent ce principe fondamental de JeDDLaJ : \"Pour une machine et un logiciel donnés, il ne doit y avoir qu'un package possible\". Ceci pour que JeDDLaJ puisse toujours opérer le choix automatique du package lors des installations/réinstallations d'un logiciel donné sur une machine, ou un groupe quelconque de machines.</FONT></P>\n");
				}
				else # OK, on peut corriger dans la base
				{
					# On corrige dans la table packages
					mysql_query("UPDATE packages SET id_logiciel=\"$id_logiciel\", nom_package=\"$nom_package\", repertoire=\"$repertoire\", specificite=\"$specificite\", valeur_specificite=\"$valeur_specificite\" WHERE id_package=\"$id_package\"");
					print("<P><I>Le package <FONT COLOR=RED>$nom_package</FONT> a été mis à jour souplement dans la table des packages.</I></P>\n");
					print("<P><I><FONT COLOR = RED>MAIS ATTENTION :</FONT> prenez grand soin de répercuter toute modification sur le serveur REMBO, en particulier les changements de nom de package ou de répertoire, sous peine de perdre complètement la cohérence de la base !!!</I></FONT></P>");
				}
				break;
			case "suppression" :
				# Le nombre de packages choisis est non nul
				if (count($checked) > 0)
				{
					$nb_packages_concernes=0;
					$liste_id_packages_concernes="";
					for($i=1;$i<=$nb_packages;$i++)
					{
						if (isset($checked[$i]))
						{
							$nb_packages_concernes++;
							$id_packages_concernes[$nb_packages_concernes] = $checked[$i];
							$liste_id_packages_concernes .= $id_packages_concernes[$nb_packages_concernes]." ";
						}
					}
					# On initialise la clause WHERE
					$clause_where=" WHERE 1=0";
					for($i=1;$i<=$nb_packages_concernes;$i++)
					{
						$clause_where .= " OR id_package=\"$id_packages_concernes[$i]\"";
					}
					# Les tables où $id_package apparaît
					$tables_concernees = array("packages", "package_est_installe_sur");
					# La liste des tables séparées par des "," pour l'affichage
					$liste_tables_concernees = implode(", ", $tables_concernees);
					# Pour l'affichage on va récupérer les noms des package qu'on va supprimer
					$request = "SELECT id_package, nom_package FROM packages".$clause_where;
					$result = mysql_query($request);
					$packages_detruits = "<UL>\n";
					while ($ligne = mysql_fetch_array($result))
					{
						$packages_detruits .= "<LI>$ligne[nom_package] (<FONT SIZE=-1>id_package = $ligne[id_package])</FONT></LI>\n";
					}
					$packages_detruits .= "</UL>\n";
					mysql_free_result($result);
					# On detruit les enregistrements des packages concernes dans les
					# tables concernees
					foreach($tables_concernees as $table)
					{
						$request="DELETE FROM $table".$clause_where;
						mysql_query($request);
					}
					print("<P>Les packages suivants ont été supprimés de la base (tables <TT>$liste_tables_concernees</TT>) :</P>".$packages_detruits);
					print("<P><I><FONT COLOR = RED>MAIS ATTENTION :</FONT> rien n'a été fait ici pour supprimer ces packages sur le serveur REMBO ; il vous faudra y intervenir si vous souhaitez qu'ils soient détruits AUSSI du point de vue de REMBO. <FONT SIZE=-1 COLOR=GREEN>Notez que leur présence sous REMBO ne pose pas de problème sous JeDDLaJ : pour lui, ils ne sont plus référencés dans la base, donc ils n'existent plus...</FONT></I></FONT></P>");
					# Si on a detruit tous les package d'un logiciel, on detruit aussi le logiciel
					if (count($checked) == $nb_packages)
					{
						$request="DELETE FROM logiciels where id_logiciel=\"$id_logiciel\"";
						mysql_query($request);
						print("<P><FONT COLOR = RED>DE PLUS</FONT>, comme vous avez choisi de supprimer tous ses packages associés, le logiciel <FONT COLOR = RED>$nom_logiciel</FONT>, version <FONT COLOR = RED>$version</FONT> a également été supprimé de la base (table <tt>logiciels</TT>).</P>");
					}
				}# end if(count($checked) > 0)
				else
				{
					print ("<P><I><FONT COLOR=RED>ATTENTION : Vous n'avez choisi aucun package</FONT>. Utilisez le bouton <TT>BACK</TT> de votre navigateur pour faire une sélection valide.</I></P>");
				}
		}
		break;
}

print("<BR><HR><P><CENTER><A HREF=accueil.php TARGET=\"_top\">Retour</A></CENTER></P>\n");

DisconnectMySQL();
PiedPage();
?>

