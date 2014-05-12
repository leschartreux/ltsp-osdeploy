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

$mode == "edition" ? entete("G�rard Milhaud & Fr�d�ric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : �dition image de base ($action)") : entete("G�rard Milhaud & Fr�d�ric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Suppression image(s) de base ($action)");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);
$mode == "edition" ? print("<CENTER><H1>�dition images de base</H1></CENTER>\n") : print("<CENTER><H1>Suppression images de base</H1></CENTER>\n");

switch ($action)
{
	case "ChoixOS":
		EnteteFormulaire("POST","editer_ou_supprimer_image_de_base.php?action=ChoixLogiciel&mode=$mode");
		$mode == "edition" ? $verbe = "�diter" : $verbe = "supprimer";
		print("<FONT COLOR=BLUE SIZE=+1>Type d'OS associ� � l'image de base � $verbe :</I> </FONT> <SELECT NAME=nom_os>\n");
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
		# On veut, dans la table logiciels, seulement ceux qui correspondent � des os, et non � des logiciels, donc on ne prend 
		# que ceux dont l'id appara�t dans la table images_de_base (car une image de base est associ�e � un OS, pas � un logiciel...)
		$request="SELECT id_os, nom_logiciel, version, icone FROM logiciels AS a, images_de_base AS b WHERE id_logiciel=id_os AND nom_os=\"$nom_os\" GROUP BY id_logiciel ORDER BY nom_logiciel,version";
		$result=mysql_query($request);
		$mode == "edition" ? $verbe = "�diter" : $verbe = "supprimer";
		print("<H2>S�lectionnez la distribution associ� � l'image de base � $verbe :</H2>\n");
		EnteteFormulaire("POST","editer_ou_supprimer_image_de_base.php?action=ChoixIdb&mode=$mode");
		EnteteTable("BORDER=2 CELLPADDING=2 CELLSPACING=1");
		while ($ligne = mysql_fetch_array($result))
		{
			empty($ligne['version']) ? $version="non sp�cifi�e" : $version=$ligne['version'];
			print("<TR>\n");
			print("<TD>\n<IMG ALIGN=CENTER WIDTH=\"$largeur_image_distrib_et_idb\" HEIGHT=\"$hauteur_image_distrib_et_idb\" SRC=\"ICONES/$ligne[icone]\">\n</TD><TD>\n$ligne[nom_logiciel], version $version\n</TD>\n<TD>\n <INPUT TYPE=RADIO NAME=\"id_os\" VALUE=\"$ligne[id_os]\">\n </TD>\n");
			print("</TR>\n");
		}
		mysql_free_result($result);
		FinTable();
		print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
		FinFormulaire();
		break;
	case "ChoixIdb":
		# On recupere les variables
		$id_os = $_POST["id_os"];
		# toutes les variables ont ete recuperees
		if (!isset($id_os))
		{
			print ("<P><I><FONT COLOR=RED>ATTENTION : Vous n'avez choisi aucune distribution</FONT>. Utilisez le bouton <TT>BACK</TT> de votre navigateur pour faire une s�lection valide.</I></P>");
		}
		else
		{
	        	$request="SELECT id_logiciel, nom_logiciel, version, icone FROM logiciels WHERE id_logiciel=\"$id_os\"";
			$result=mysql_query($request);
			$ligne = mysql_fetch_array($result);
			mysql_free_result($result);
			$nom_logiciel = $ligne['nom_logiciel'];
			empty($ligne['version']) ? $version="non sp�cifi�e" : $version=$ligne['version'];
			print("<H2>Distribution</H2>");
			EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
			print("<TR><TD><IMG ALIGN=CENTER WIDTH=\"$largeur_image_distrib_et_idb\" HEIGHT=\"$hauteur_image_distrib_et_idb\" SRC=\"ICONES/$ligne[icone]\"></TD><TD><I>$ligne[nom_logiciel], version $ligne[version]</I></TD></TR>\n");
			FinTable();
			$request = "SELECT * FROM images_de_base WHERE id_os=\"$id_os\"";
			$result=mysql_query($request);
			$nb_idb = mysql_num_rows($result);
			# Un "s" � Images de base que s'il y en a plus d'une (ou 0, car � ce moment-l� on parle de la potentialit� d'existence
			# de plusieurs images de base...) : on ne badine pas avec l'orthographe...
			($nb_idb > 1 OR $nb_idb==0) ? $pas_de_faute = "Images de base associ�es" : $pas_de_faute = "Image de base associ�e";
			print("<H2>$pas_de_faute � cette distribution</H2>");
			if ($nb_idb > 0)
			{
				$mode == "edition" ? EnteteFormulaire("POST","editer_ou_supprimer_image_de_base.php?action=EditIdb&mode=$mode") : EnteteFormulaire("POST","editer_ou_supprimer_image_de_base.php?action=Validation&mode=$mode");
				EnteteTable("BORDER=2 CELLPADDING=2 CELLSPACING=1");
				$i = 1;
				while ($ligne = mysql_fetch_array($result))
				{
					print("<TR>\n");
					print("<TD>\n<FONT COLOR=RED>$ligne[nom_idb]</FONT>, <I>sp�cificit�</I> <FONT COLOR=RED>$ligne[specificite]</FONT>");
					if ($ligne['specificite'] != "aucune") {print("<I> de valeur</I> <FONT COLOR=RED>".$ligne['valeur_specificite']."</FONT>");}
					$ligne['repertoire'] == '' ? $repertoire_a_afficher = ", sans r�pertoire sp�cifi�" : $repertoire_a_afficher = ", r�pertoire <FONT COLOR=RED>".$ligne['repertoire']."</FONT>";
					print("$repertoire_a_afficher");
					if ($mode == "edition")
					{
						# Edition : une seule image de base � la fois donc RADIO
						print("\n</TD>\n<TD>\n <INPUT TYPE=RADIO NAME=\"id_idb\" VALUE=\"$ligne[id_idb]\">\n </TD>\n");
					}
					else
					{
						# Suppression : eventuellement plusieurs images de base � la fois donc CHECKBOX
						print("\n</TD>\n<TD>\n <INPUT TYPE=CHECKBOX NAME=\"checked[".$i++."]\" VALUE=\"$ligne[id_idb]\">\n </TD>\n");
					}
					print("</TR>\n");
				}
				mysql_free_result($result);
				FinTable();
				print("<INPUT TYPE=HIDDEN NAME=id_os VALUE=\"$id_os\">\n");
				if ($mode == "suppression") 
				{
					# nom_logiciel et version serviront a afficher le logiciel dans le bilan lors de la 
					# validation de la suppression ; on evite ainsi une requete dans la table
					# logiciels juste pour aller chercher le nom et la version. 
					# On aura besoin du nom si on n'a plus d'images de base associees a un 
					# logiciel, cas dans lequel on detruit aussi le logiciel, une information
					# qu'il est bon d'afficher...
					# Dans le cas edition, nom_logiciel est inutile puisqu'un requete est de 
					# toutes les facons necessaires pour aller chercher la version, l'icone, etc.
					print("<INPUT TYPE=HIDDEN NAME=nom_logiciel VALUE=\"$nom_logiciel\">\n");
					print("<INPUT TYPE=HIDDEN NAME=version VALUE=\"$version\">\n");
					print("<INPUT TYPE=HIDDEN NAME=nb_idb VALUE=\"$nb_idb\">\n");
				}
				print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
				FinFormulaire();
			}
			else
			{
				switch ($mode)
				{
					case "edition" :
						print("<I><FONT COLOR=RED>Pas d'images de base associ�es � la distribution <B>$nom_logiciel, version $version</B></FONT>... 3 choix s'offrent donc � vous :");
						print("<OL>");
						print("<LI>Utilisez le bouton <B>Pr�c�dent/Back</B> de votre navigateur pour <FONT COLOR=GREEN>choisir un autre logiciel</FONT>,");
						print("<LI>ou le bouton <B>valider</B> ci-dessous pour <FONT COLOR=GREEN>ajouter une image de base au logiciel <B>$nom_logiciel, version $version</B></FONT>,");
						print("<LI>ou encore le bouton <B>Retour</B> en bas de cette page pour <FONT COLOR=GREEN>faire une toute autre op�ration JeDDLaJique</FONT> (dans ce dernier cas, il semble utile de vous interroger sur votre motivation pour le travail aujourd'hui...)</I>");
						print("</OL>");
						EnteteFormulaire("POST","ajouter_image_de_base.php?action=AjoutIdb");
						print("<INPUT TYPE=HIDDEN NAME=id_os VALUE=\"$id_os\">\n");
						print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
						FinFormulaire();
						break;
					case "suppression" :
						print("<I><FONT COLOR=RED>Pas d'images de base associ�es � la distribution <B>$nom_logiciel, version $version</B></FONT>... 2 choix s'offrent donc � vous :");
						print("<OL>");
						print("<LI>Utilisez le bouton <B>Pr�c�dent/Back</B> de votre navigateur pour <FONT COLOR=GREEN>choisir un autre logiciel</FONT>,");
						print("<LI>ou encore le bouton <B>Retour</B> en bas de cette page pour <FONT COLOR=GREEN>faire une toute autre op�ration JeDDLaJique</FONT> (dans ce dernier cas, il semble utile de vous interroger sur votre motivation pour le travail aujourd'hui...)</I>");
						print("</OL>");
				}
			}
		}
		break;
	case "EditIdb":
		# On recupere les variables
		$id_idb = $_POST["id_idb"];
		$id_os = $_POST["id_os"];
		# toutes les variables ont ete recuperees
		if (!isset($id_idb))
		{
			print ("<P><I><FONT COLOR=RED>ATTENTION : Vous n'avez choisi aucune image de base</FONT>. Utilisez le bouton <TT>BACK</TT> de votre navigateur pour faire une s�lection valide.</I></P>");
		}
		else
		{
	        	$request1="SELECT id_logiciel, nom_logiciel, version, icone FROM logiciels WHERE id_logiciel=\"$id_os\"";
			$result1=mysql_query($request1);
			$ligne1 = mysql_fetch_array($result1);
			empty($ligne1['version']) ? $version="non sp�cifi�e" : $version=$ligne1['version'];
			mysql_free_result($result1);
			print("<H2>Distribution</H2>");
			EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
			print("<TR><TD><IMG ALIGN=CENTER WIDTH=\"$largeur_image_distrib_et_idb\" HEIGHT=\"$hauteur_image_distrib_et_idb\" SRC=\"ICONES/$ligne1[icone]\"></TD><TD><I>$ligne1[nom_logiciel], version $ligne1[version]</I></TD></TR>\n");
			FinTable();
			print("<H2>Image de base</H2>");
	        	$request2="SELECT * FROM images_de_base WHERE id_idb=\"$id_idb\"";
			$result2=mysql_query($request2);
			$ligne2 = mysql_fetch_array($result2);
			mysql_free_result($result2);
			EnteteFormulaire("POST","editer_ou_supprimer_image_de_base.php?action=Validation&mode=$mode");
			print("<INPUT TYPE=HIDDEN NAME=id_os VALUE=\"$ligne2[id_os]\">\n");
			print("<INPUT TYPE=HIDDEN NAME=nom_logiciel VALUE=\"$ligne1[nom_logiciel]\">\n");
			print("<INPUT TYPE=HIDDEN NAME=version VALUE=\"$ligne1[version]\">\n");
			print("<INPUT TYPE=HIDDEN NAME=id_idb VALUE=\"$ligne2[id_idb]\">\n");
			EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
			print("<TR><TD><I>Nom image de base</I> </TD><TD>: <INPUT TYPE=TEXT NAME=nom_idb SIZE=50 VALUE=\"$ligne2[nom_idb]\"></TD></TR>\n");
			print("<TR><TD><I>R�pertoire</I> </TD><TD>: <FONT SIZE=-1 COLOR=GREEN><I>$RemboIDBDir</I></FONT><INPUT TYPE=TEXT NAME=repertoire SIZE=50 VALUE=\"$ligne2[repertoire]\"></TD></TR>\n");
			print("<TR><TD><I>Sp�cificit�</I> </TD><TD>: <SELECT NAME=specificite>\n");
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
			print("<TR><TD><I>Valeur Sp�cificit�</I> </TD><TD>: <INPUT TYPE=TEXT NAME=valeur_specificite SIZE=50 VALUE=\"$ligne2[valeur_specificite]\"></TD></TR>\n");
			FinTable();
			print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
			FinFormulaire();
		}
		break;
	case "Validation":
		# On recupere les variables
		$id_os = $_POST["id_os"];
		$nom_logiciel = $_POST["nom_logiciel"];
		empty($_POST['version']) ? $version="non sp�cifi�e" : $version=$_POST['version'];
		if ($mode == "edition")
		{
			$id_idb = $_POST["id_idb"];
			$nom_idb = $_POST["nom_idb"];
			$repertoire = $_POST["repertoire"];
			# On ajoute un slash final au chemin s'il n'y est pas d�j�, sauf si r�pertoire est vide
			# sinon on aurait $RemboIDBDir/ (donc deux slashes finaux...)
			if (substr($repertoire,-1) != "/" and $repertoire != "") {$repertoire .= "/";}
			$specificite = $_POST["specificite"];
			$valeur_specificite = $_POST["valeur_specificite"];
		}
		if ($mode == "suppression")
		{
			$nb_idb = $_POST["nb_idb"];
			$checked = $_POST["checked"];
		}
		# toutes les variables ont ete recuperees
		# On attaque la base
		switch ($mode)
		{
			case "edition" :
				# On ne veut pas de fichiers de meme nom dans le meme repertoire en dehors du cas ou ce sont des entrees multiples 
				# pour la meme distribution, i.e. m�mes id_os ET specificites identiques ET valeurs specificite differentes. 
				# La requete suivante exprime les cas interdits ET les compte : on ne veut donc editer que si la requete renvoie 0 
				$request = "SELECT COUNT(*) AS total FROM images_de_base WHERE nom_idb=\"$nom_idb\" AND repertoire=\"$repertoire\" AND id_idb <> \"$id_idb\" AND (id_os<>\"$id_os\" OR specificite<>\"$specificite\" OR valeur_specificite=\"$valeur_specificite\")";
				# On ne veut pas, pour la meme distribution qu'il y ait plus d'une image non specifique OU qu'il y ait des images specifiques 
				# de type de specificite differentes OU (de meme type de specificite ET de meme valeur specificite).
				# La requete suivante exprime ces cas interdits ET les compte : on ne veut donc editer que si la requete renvoie 0 
				$request2 = "SELECT COUNT(*) AS total FROM images_de_base WHERE id_os=\"$id_os\" AND id_idb <> \"$id_idb\" AND (\"$specificite\"=\"aucune\" OR specificite=\"aucune\" OR specificite<>\"$specificite\" OR (specificite=\"$specificite\" AND valeur_specificite=\"$valeur_specificite\"))";

				$result = mysql_query($request);
				$result2 = mysql_query($request2);
				$line = mysql_fetch_array($result);
				$line2 = mysql_fetch_array($result2);
				$idb_incompatible_meme_rep_deja_existante = ($line["total"] != 0);
				$idb_incompatible_meme_distrib_deja_existante = ($line2["total"] != 0);
				mysql_free_result($result);
				mysql_free_result($result2);

				if ($repertoire=='') {$phrase_rep="dans le m�me r�pertoire";} else {$phrase_rep="dans le r�pertoire <FONT COLOR=RED>$repertoire</FONT>";}
				if ($idb_incompatible_meme_rep_deja_existante)
				{
					print("<P><I><FONT COLOR = RED>ATTENTION DANGER : </FONT>Une AUTRE image de base de nom <FONT COLOR=RED>$nom_idb</FONT> existe d�j� $phrase_rep... Or, ceci n'est possible que si les images de base sont attach�es � la m�me distribution, ont le m�me type de sp�cifit� mais une valeur de sp�cificit� diff�rente(*). Le cas pr�sent ne correspond pas � cette situation... Veuillez utiliser le bouton Back/Pr�c�dent de votre navigateur pour modifier votre entr�e.</I></P>\n");
					print("<P><FONT SIZE=-2>(*) Il s'agit alors d'entr�es multiples pour la m�me image de base, ce qui est souhaitable dans certains cas : <BR> - le m�me type de machine avec des composants identiques mais plac�s diff�remment sur le bus peut avoir deux signatures diff�rentes car l'algorithme de calcul de la signature Rembo d�pend de l'ordre de la d�tection des composants sur le bus ;<BR> - on peut vouloir restreindre une image de base � quelques machines ; on utilise alors la sp�cifit� nom_dns pour diff�rencier plusieurs entr�es de cette image de base.</FONT></P>\n");
				}
				elseif ($idb_incompatible_meme_distrib_deja_existante)
				{
					$phrase_valeur_specificite = ( ($specificite == "aucune" or empty($valeur_specificite)) ? "" : " de valeur <FONT COLOR=RED>".$valeur_specificite."</FONT>");
					print("<P><I><FONT COLOR = RED>ATTENTION DANGER : </FONT>l'image de base que vous souhaitez ajouter (<FONT COLOR = RED>$nom_idb</FONT>, sp�cificit� <FONT COLOR = RED>$specificite</FONT>".$phrase_valeur_specificite.") pr�sente des caract�ristiques incompatibles avec au moins une des images de base d�j� existantes associ�es � la m�me distribution (<FONT COLOR = RED>$nom_logiciel</FONT>, <FONT COLOR = RED>$version</FONT>). L'incompatibilit� provient d'une des causes suivantes(*) :<BR>\n<UL>\n<LI>Arrgh : l'image que vous ajoutez n'est pas sp�cifique ET il existe d�j� une image de base non sp�cifique pour cette distribution(*) !!! Reculez impies, fuyez devant les pr�ceptes JeDDLaJiques !!! ;</LI>\n<LI>Atroce malheur : l'image que vous ajoutez est d'un type de sp�cifit� diff�rent de celui des images d�j� existantes pour cette distribution !!! Inconscient !!! Vous risquez de r�veiller des forces mal�fiques dont vous ne pouvez appr�hender la puissance !!! ;</LI>\n<LI>Horreur insondable : l'image que vous ajoutez est du m�me type de sp�cifit� et de m�me valeur de sp�cificit� qu'une des images d�j� existantes pour cette distribution !!! Vous crachez � la face du Diable !!! Vous pissez face au vent au Cap Horn !!! Puisse la puissance de JeDDLaJ vous prot�ger contre tous les d�mons que vous d�fiez !!!</LI>\n</UL>\n");
					print("<P><FONT SIZE=-2>(*) Tous ces cas violent ce principe fondamental de JeDDLaJ : \"Pour une machine et une distribution donn�es, il ne doit y avoir qu'une image de base possible\". Ceci pour que JeDDLaJ puisse toujours op�rer le choix automatique de l'image de base lors des installations/r�installations d'une distribution donn�e sur une machine, ou un groupe quelconque de machines.</FONT></P>\n");
				}
				else # OK, on peut corriger dans la base
				{
					# On corrige dans la table images_de_base
					mysql_query("UPDATE images_de_base SET id_os=\"$id_os\", nom_idb=\"$nom_idb\", repertoire=\"$repertoire\", specificite=\"$specificite\", valeur_specificite=\"$valeur_specificite\" WHERE id_idb=\"$id_idb\"");
					print("<P><I>L'image de base <FONT COLOR=RED>$nom_idb</FONT> a �t� mise � jour souplement dans la table des images de base.</I></P>\n");
					print("<P><I><FONT COLOR = RED>MAIS ATTENTION :</FONT> prenez grand soin de r�percuter toute modification sur le serveur REMBO, en particulier les changements de nom d'image de base ou de r�pertoire, sous peine de perdre compl�tement la coh�rence de la base !!!</I></FONT></P>");
				}
				break;
			case "suppression" :
				# Le nombre d'images de base choisi est non nul
				if (count($checked) > 0)
				{
					$nb_idb_concernees=0;
					$liste_id_idb_concernees="";
					for($i=1;$i<=$nb_idb;$i++)
					{
						if (isset($checked[$i]))
						{
							$nb_idb_concernees++;
							$id_idb_concernees[$nb_idb_concernees] = $checked[$i];
							$liste_id_idb_concernees .= $id_idb_concernees[$nb_idb_concernees]." ";
						}
					}
							# On initialise la clause WHERE
							$clause_where=" WHERE 1=0";
							for($i=1;$i<=$nb_idb_concernees;$i++)
							{
								$clause_where .= " OR id_idb=\"$id_idb_concernees[$i]\"";
							}
							# Les tables o� $id_idb appara�t
							$tables_concernees = array("idb_est_installe_sur", "images_de_base");
							# La liste des tables s�par�es par des "," pour l'affichage
							$liste_tables_concernees = implode(", ", $tables_concernees);
							# Pour l'affichage on va r�cup�rer les noms des images de base qu'on va supprimer
							$request = "SELECT id_idb, nom_idb FROM images_de_base ".$clause_where;
							$result = mysql_query($request);
							$idb_detruites = "<UL>\n";
							while ($ligne = mysql_fetch_array($result))
							{
								$idb_detruites .= "<LI>$ligne[nom_idb] (<FONT SIZE=-1>id_idb = $ligne[id_idb])</FONT></LI>\n";
							}
							$idb_detruites .= "</UL>\n";
							mysql_free_result($result);
							# On detruit les enregistrements des idb concernees dans les
							# tables concernees
							foreach($tables_concernees as $table)
							{
								$request="DELETE FROM $table".$clause_where;
								mysql_query($request);
							}
							print("<P>Les images de base suivantes ont �t� supprim�es de la base (tables <TT>$liste_tables_concernees</TT>) :</P>".$idb_detruites);
							print("<P><I><FONT COLOR = RED>MAIS ATTENTION :</FONT> rien n'a �t� fait ici pour supprimer ces images de base sur le serveur REMBO ; il vous faudra y intervenir si vous souhaitez qu'elles soient d�truites AUSSI du point de vue de REMBO. <FONT SIZE=-1 COLOR=GREEN>Notez que leur pr�sence sous REMBO ne pose pas de probl�me sous JeDDLaJ : pour lui, elles ne sont plus r�f�renc�es dans la base, donc elles n'existent plus...</FONT></I></FONT></P>");
							# Si on a detruit toutes les images de base d'une distribution, on detruit aussi la distribution
							if (count($checked) == $nb_idb)
							{
								$request="DELETE FROM logiciels where id_logiciel=\"$id_os\"";
								mysql_query($request);
								print("<P><FONT COLOR = RED>DE PLUS</FONT>, comme vous avez choisi de supprimer toutes ses images de base associ�es, la distribution <FONT COLOR = RED>$nom_logiciel</FONT>, version <FONT COLOR = RED>$version</FONT> a �galement �t� supprim�e de la base.</P>");
							}
				}# end if(count($checked) > 0)
				else
				{
					print ("<P><I><FONT COLOR=RED>ATTENTION : Vous n'avez choisi aucune image de base</FONT>. Utilisez le bouton <TT>BACK</TT> de votre navigateur pour faire une s�lection valide.</I></P>");
				}
		}
		break;
}

print("<BR><HR><P><CENTER><A HREF=accueil.php TARGET=\"_top\">Retour</A></CENTER></P>\n");

DisconnectMySQL();
PiedPage();
?>

