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

$mode == "edition" ? entete("G�rard Milhaud & Fr�d�ric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : �dition logiciel ($action)") : entete("G�rard Milhaud & Fr�d�ric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Suppression logiciel ($action)");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);
$mode == "edition" ? print("<CENTER><H1>�dition de logiciels</H1></CENTER>\n") : print("<CENTER><H1>Suppression de logiciels</H1></CENTER>\n");

switch ($action)
{
	case "ChoixOS":
		EnteteFormulaire("POST","editer_ou_supprimer_logiciel.php?action=ChoixLogiciel&mode=$mode");
		$mode == "edition" ? $verbe = "�diter" : $verbe = "supprimer";
		print("<FONT COLOR=BLUE SIZE=+1>OS associ� au logiciel � $verbe :</I> </FONT> <SELECT NAME=nom_os>\n");
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
		# On veut, dans la table logiciels, seulement ceux qui correspondent � des logiciels, et non � des OS, donc on ne prend 
		# que ceux dont l'id appara�t dans la table packages (car un package est associ� � un "vrai logiciel", pas un OS...)
		$request="SELECT a.id_logiciel, nom_logiciel, version, icone FROM logiciels AS a, packages AS b WHERE a.id_logiciel=b.id_logiciel AND nom_os=\"$nom_os\" GROUP BY id_logiciel ORDER BY nom_logiciel,version";
		$result=mysql_query($request);
		$nb_logiciels = mysql_num_rows($result);
		$mode == "edition" ? $titre = "le logiciel � �diter" : $titre = "le(s) logiciel(s) � supprimer";
		print("<H2>S�lectionnez $titre :</H2>\n");
		if ($nb_logiciels > 0)
		{
			$mode == "edition" ? EnteteFormulaire("POST","editer_ou_supprimer_logiciel.php?action=EditLogiciel&mode=$mode") : EnteteFormulaire("POST","editer_ou_supprimer_logiciel.php?action=Validation&mode=$mode");
			EnteteTable("BORDER=2 CELLPADDING=2 CELLSPACING=1");
			$i = 1;
			while ($ligne = mysql_fetch_array($result))
			{
				print("<TR>\n");
				# On �vite l'affichage d'un champ vide pour la version si elle n'est pas sp�cifi�e...
				empty($ligne['version']) ? $laversion="non sp�cifi�e" : $laversion=$ligne['version'];
				print("<TD>\n<IMG ALIGN=CENTER WIDTH=\"$largeur_image_logiciel_et_package\" HEIGHT=\"$hauteur_image_logiciel_et_package\" SRC=\"ICONES/$ligne[icone]\">\n</TD><TD>\n<FONT COLOR=RED>$ligne[nom_logiciel]</FONT>, <I>version</I> <FONT COLOR=RED>$laversion</FONT>");
				if ($mode == "edition")
				{
					# Edition : un seul logiciel � la fois donc RADIO
					print("\n</TD>\n<TD>\n <INPUT TYPE=RADIO NAME=\"id_logiciel\" VALUE=\"$ligne[id_logiciel]\">\n </TD>\n");
				}
				else
				{
					# Suppression : eventuellement plusieurs logiciels � la fois donc CHECKBOX
					print("\n</TD>\n<TD>\n <INPUT TYPE=CHECKBOX NAME=\"checked[".$i++."]\" VALUE=\"$ligne[id_logiciel]\">\n </TD>\n");
				}
				print("</TR>\n");
			}
			mysql_free_result($result);
			FinTable();
			if ($mode == "suppression") 
			{
				print("<INPUT TYPE=HIDDEN NAME=\"nb_logiciels\" VALUE=\"$nb_logiciels\">\n");
			}
			print("<INPUT TYPE=HIDDEN NAME=\"nom_os\" VALUE=\"$nom_os\">\n");
			print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
			FinFormulaire();
		}
		else
		{
			print("<I>Votre base JeDDLaJ ne contient pour l'instant aucun logiciel pour le syst�me $nom_os... Il vous faut d'abord en ajouter au moins un avant de pouvoir l'�diter ou le supprimer. Pour cela, choisissez une machine sous $nom_os dans votre base JeDDLaJ et <A HREF=\"choix_machines_multiples.php?action=packages\">passez-l� en �tat \"Cr�ation de packages\"</A>. Puis laissez-vous guider pour cr�er un package associ� au logiciel que vous voulez voir figurer dans votre biblioth�que de logiciels JeDDLaJ pour $nom_os. Allez, tout cela n'est pas si grave : tout le monde, dans tous les domaines, commence par �tre d�butant...)</I>");
		}
		break;
	case "EditLogiciel":
		# On recupere les variables
		$id_logiciel = $_POST["id_logiciel"];
		$nom_os = $_POST["nom_os"];
		# toutes les variables ont ete recuperees
		if (!isset($id_logiciel))
		{
			print ("<P><I><FONT COLOR=RED>ATTENTION : Vous n'avez choisi aucun logiciel</FONT>. Utilisez le bouton <TT>BACK</TT> de votre navigateur pour faire une s�lection valide.</I></P>");
		}
		else
		{
	        	$request="SELECT id_logiciel, nom_logiciel, version, icone, description FROM logiciels WHERE id_logiciel=\"$id_logiciel\"";
			$result=mysql_query($request);
			$ligne = mysql_fetch_array($result);
			mysql_free_result($result);
			# On �vite l'affichage d'un champ vide pour la version si elle n'est pas sp�cifi�e...
			empty($ligne['version']) ? $laversion="non sp�cifi�e" : $laversion=$ligne['version'];
			print("<H2>Logiciel $ligne[nom_logiciel], version $laversion, pour $nom_os</H2>");
			EnteteFormulaire("POST","editer_ou_supprimer_logiciel.php?action=Validation&mode=$mode");
			print("<INPUT TYPE=HIDDEN NAME=id_logiciel VALUE=\"$ligne[id_logiciel]\">\n");
			print("<INPUT TYPE=HIDDEN NAME=nom_os VALUE=\"$nom_os\">\n");
			EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
			print("<TR><TD>\n<IMG ALIGN=CENTER WIDTH=\"$largeur_image_logiciel_et_package\" HEIGHT=\"$hauteur_image_logiciel_et_package\" SRC=\"ICONES/$ligne[icone]\"></TD></TR>\n");
			print("<TR><TD><I>Nom logiciel</I> </TD><TD>:</TD><TD><INPUT TYPE=TEXT NAME=nom_logiciel SIZE=50 VALUE=\"$ligne[nom_logiciel]\"></TD></TR>\n");
			print("<TR><TD><I>Version</I> </TD><TD>:</TD><TD><INPUT TYPE=TEXT NAME=version SIZE=50 VALUE=\"$ligne[version]\"></TD></TR>\n");
			print("<TR><TD><I>Icone</I> </TD><TD>:</TD><TD><INPUT TYPE=TEXT NAME=icone SIZE=50 VALUE=\"$ligne[icone]\"></TD></TR>\n");
			print("<TR><TD><I>Description</I></TD><TD>:</TD><TD><TEXTAREA ALIGN=BOTTOM COLS=100 ROWS=10 NAME=description>$ligne[description]</TEXTAREA></TD></TR>\n");
			FinTable();
			print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
			FinFormulaire();
		}
		break;
	case "Validation":
		# On recupere les variables
		$nom_os = $_POST["nom_os"];
		if ($mode == "edition")
		{
			$id_logiciel = $_POST["id_logiciel"];
			$nom_logiciel = $_POST["nom_logiciel"];
			$version = $_POST["version"];
			$icone = $_POST["icone"];
			$description = $_POST["description"];
		}
		if ($mode == "suppression")
		{
			$nb_logiciels = $_POST["nb_logiciels"];
			$checked = $_POST["checked"];
		}
		# toutes les variables ont ete recuperees
		# On attaque la base
		switch ($mode)
		{
			case "edition" :
				# On v�rifie via la cl� unique {nom_os, nom_logiciel, version} que les modifications effectu�es ne nous am�nent pas � dupliquer un logiciel d�j� existant...
				$request = "SELECT COUNT(*) AS total FROM logiciels WHERE nom_os=\"$nom_os\" AND nom_logiciel=\"$nom_logiciel\" AND version=\"$version\" AND id_logiciel<>\"$id_logiciel\"";
				$result = mysql_query($request);
				$line = mysql_fetch_array($result);
				$logiciel_meme_spec_deja_existant = ($line["total"] != 0);
				mysql_free_result($result);
				if ($logiciel_meme_spec_deja_existant)
				{
					# On �vite l'affichage d'un champ vide pour la version si elle n'est pas sp�cifi�e...
					empty($version) ? $laversion="non sp�cifi�e" : $laversion=$version;
					print("<P><I><FONT COLOR = RED>ATTENTION DANGER : </FONT>Le logiciel <FONT COLOR=RED>$nom_logiciel</FONT>, version <FONT COLOR=RED>$laversion</FONT>");
					print(" existe d�j� pour le syst�me $nom_os... Veuillez utiliser le bouton Back/Pr�c�dent de votre navigateur pour modifier votre entr�e.</I></P>\n");
				}
				else # OK, on peut attaquer la base et corriger
				{
					# On corrige dans la table logiciels
					mysql_query("UPDATE logiciels SET id_logiciel=\"$id_logiciel\", nom_logiciel=\"$nom_logiciel\", version=\"$version\", icone=\"$icone\", description=\"$description\" WHERE id_logiciel=\"$id_logiciel\"");
					# On �vite l'affichage d'un champ vide pour la version si elle n'est pas sp�cifi�e...
					empty($version) ? $laversion="non sp�cifi�e" : $laversion=$version;
					print("<P><I>Le logiciel <FONT COLOR=RED>$nom_logiciel</FONT>, version $laversion pour le syst�me $nom_os a �t� mis � jour paisiblement dans la table des logiciels.</I></P>\n");
					}
				break;
			case "suppression" :
				# Le nombre de logiciels choisis est non nul
				if (count($checked) > 0)
				{
					$nb_logiciels_concernes=0;
					$liste_id_logiciels_concernes="";
					for($i=1;$i<=$nb_logiciels;$i++)
					{
						if (isset($checked[$i]))
						{
							$nb_logiciels_concernes++;
							$id_logiciels_concernes[$nb_logiciels_concernes] = $checked[$i];
							$liste_id_logiciels_concernes .= $id_logiciels_concernes[$nb_logiciels_concernes]." ";
						}
					}
					### On s'occupe d'abord des logiciels...
					# On initialise la clause WHERE
					$clause_where_logiciels=" WHERE 1=0";
					for($i=1;$i<=$nb_logiciels_concernes;$i++)
					{
						$clause_where_logiciels .= " OR id_logiciel=\"$id_logiciels_concernes[$i]\"";
					}
					# Pour l'affichage on va r�cup�rer les noms des logiciels qu'on va supprimer
					$request = "SELECT id_logiciel, nom_logiciel FROM logiciels".$clause_where_logiciels;
					$result = mysql_query($request);
					$logiciels_detruits = "<UL>\n";
					while ($ligne = mysql_fetch_array($result))
					{
						$logiciels_detruits .= "<LI>$ligne[nom_logiciel] (<FONT SIZE=-1>id_logiciel = $ligne[id_logiciel])</FONT></LI>\n";
					}
					$logiciels_detruits .= "</UL>\n";
					mysql_free_result($result);
					# Les tables o� $id_logiciel appara�t
					$tables_concernees_logiciels = array("logiciels", "pis_est_associe_a");
					# La liste des tables s�par�es par des "," pour l'affichage
					$liste_tables_concernees_logiciels = implode(", ", $tables_concernees_logiciels);
					# On detruit les enregistrements des logiciels concernes dans les
					# tables concernees
					foreach($tables_concernees_logiciels as $table)
					{
						$request="DELETE FROM $table".$clause_where_logiciels;
						mysql_query($request);
					}
					# On s'occupe maintenant des packages associ�s aux logiciels...
					$request = "SELECT id_package, nom_package FROM packages".$clause_where_logiciels;
					$result = mysql_query($request);
					$packages_detruits = "<UL>\n";
					# On initialise la clause WHERE 2
					$clause_where_packages=" WHERE 1=0";
					while ($ligne = mysql_fetch_array($result))
					{
						$packages_detruits .= "<LI>$ligne[nom_package] (<FONT SIZE=-1>id_package = $ligne[id_package])</FONT></LI>\n";
						$clause_where_packages .= " OR id_package=\"$ligne[id_package]\"";
					}
					$packages_detruits .= "</UL>\n";
					mysql_free_result($result);
					# Les tables o� id_package appara�t 
					$tables_concernees_packages = array("packages", "package_est_installe_sur");
					# La liste des tables s�par�es par des "," pour l'affichage
					$liste_tables_concernees_packages = implode(", ", $tables_concernees_packages);
					# On detruit les enregistrements des packages concernes dans les
					# tables concernees
					foreach($tables_concernees_packages as $table)
					{
						$request="DELETE FROM $table".$clause_where_packages;
						mysql_query($request);
					}
					#######################
					print("<P>Les logiciels suivants ont �t� supprim�s de la base (tables <TT>$liste_tables_concernees_logiciels</TT>) :</P>".$logiciels_detruits);
					print("<P>Les packages suivants ont �t� supprim�s de la base (tables <TT>$liste_tables_concernees_packages</TT>) :</P>".$packages_detruits);
					print("<P><I><FONT COLOR = RED>MAIS ATTENTION :</FONT> rien n'a �t� fait ici pour supprimer le/les packages associ�(s) � ce logiciel sur le serveur REMBO ; il vous faudra y intervenir si vous souhaitez qu'ils soient d�truits AUSSI du point de vue de REMBO. <FONT SIZE=-1 COLOR=GREEN>Notez que leur pr�sence sous REMBO ne pose pas de probl�me sous JeDDLaJ : pour lui, ils ne sont plus r�f�renc�s dans la base, donc ils n'existent plus...</FONT></I></FONT></P>");
				}# end if(count($checked) > 0)
				else
				{
					print ("<P><I><FONT COLOR=RED>ATTENTION : Vous n'avez choisi aucun logiciel</FONT>... Il faut vous concentrer un peu et diminuer un peu les champignons sp�ciaux ramen�s d'Am�rique Latine... Utilisez le bouton <TT>BACK</TT> de votre navigateur pour faire une s�lection valide.</I></P>");
				}
		}
		break;
}

print("<BR><HR><P><CENTER><A HREF=accueil.php TARGET=\"_top\">Retour</A></CENTER></P>\n");

DisconnectMySQL();
PiedPage();
?>

