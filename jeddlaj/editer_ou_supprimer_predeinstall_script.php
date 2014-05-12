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

$mode == "edition" ? entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Édition predeinstall script ($action)") : entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Suppression predeinstall script ($action)");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);
$mode == "edition" ? print("<CENTER><H1>Édition de predeinstall scripts</H1></CENTER>\n") : print("<CENTER><H1>Suppression de predeinstall scripts</H1></CENTER>\n");

switch ($action)
{
	case "ChoixPreDeinstallScript":
	        $request="SELECT * FROM predeinstall_scripts";
		$result=mysql_query($request);
		$nb_scripts = mysql_num_rows($result);
		print("<H2>Choix predeinstall script</H2>");
		$mode == "edition" ? EnteteFormulaire("POST","editer_ou_supprimer_predeinstall_script.php?action=EditScript&mode=$mode") : EnteteFormulaire("POST","editer_ou_supprimer_predeinstall_script.php?action=Validation&mode=$mode");
		EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
		$i = 1;
		while ($ligne = mysql_fetch_array($result))
		{
			if ($ligne["applicable_a"] == "nom_dns")
			{ 
				$info = "à la machine</I><TT> <FONT COLOR=RED>$ligne[valeur_application]</FONT></TT>";
			}
			elseif ($ligne["applicable_a"] == "nom_groupe")
			{
				$info = "au groupe</I><TT> <FONT COLOR=RED>$ligne[valeur_application]</FONT></TT>";	
			}
			else #applicable = rien_pour_l_instant (cas d'un script qui s'appliquait à une machine ou un groupe qui ont été supprimés
			{
				$info = "à rien pour l'instant</I>";	
			}
			print("<TR><TD><IMG ALIGN=CENTER SRC=ICONES/greenball.png> <TT><FONT COLOR=RED>$ligne[nom_script]</FONT></TT>,<I> dans le répertoire</I> <TT><FONT COLOR=RED>$ligne[repertoire]</FONT></TT>,<I> applicable $info</TD>\n");
			if ($mode == "edition")
			{
				# Edition : un seul predeinstall script à la fois donc RADIO
				print("\n</TD>\n<TD>\n <INPUT TYPE=RADIO NAME=\"id_script\" VALUE=\"$ligne[id_script]\">\n </TD>\n");
			}
			else
			{
				# Suppression : eventuellement plusieurs predeinstall script à la fois donc CHECKBOX
				print("\n</TD>\n<TD>\n <INPUT TYPE=CHECKBOX NAME=\"checked_pdis[".$i++."]\" VALUE=\"$ligne[id_script]\">\n </TD>\n");
			}
			print("</TR>\n");
		}
		mysql_free_result($result);
		FinTable();
		print("<INPUT TYPE=HIDDEN NAME=nb_scripts VALUE=\"$nb_scripts\">\n");
		print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
		FinFormulaire();
		break;
	case "EditScript":
		# On recupere les variables
		$id_script = $_POST["id_script"];
		# toutes les variables ont ete recuperees
		if (!isset($id_script))
		{
			print ("<P><I><FONT COLOR=RED>ATTENTION : Vous n'avez choisi aucun predeinstall_script</FONT>. Utilisez le bouton <TT>BACK</TT> de votre navigateur pour faire une sélection valide.</I></P>");
		}
		else
		{
	        	$request="SELECT * FROM predeinstall_scripts WHERE id_script=\"$id_script\"";
			$result=mysql_query($request);
			$ligne = mysql_fetch_array($result);
			mysql_free_result($result);
			$nom_script = $ligne["nom_script"];
			$repertoire = $ligne["repertoire"];
			$applicable_a = $ligne["applicable_a"];
			$valeur_application = $ligne["valeur_application"];
			print("<H2>Predeinstall script à éditer</H2>");
			print("<FORM NAME=\"edit_predeinst\" METHOD=\"POST\" ACTION=\"editer_ou_supprimer_predeinstall_script.php?action=Validation&mode=$mode\">");
			EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
			print("<TR><TD><I>Nom predeinstall script</I> </TD><TD>: <INPUT TYPE=TEXT NAME=nom_script SIZE=50 VALUE=\"$nom_script\"></TD></TR>\n");
			print("<TR><TD><I>Répertoire</I> </TD><TD>: <FONT SIZE=-1 COLOR=GREEN><I>$RemboPreDeinstScriptsDir</I></FONT><INPUT TYPE=TEXT NAME=repertoire SIZE=50 VALUE=\"$repertoire\"></TD></TR>\n");
			print("<TR>\n<TD><I>Applicable à : </I></TD></TR>\n");
			print("<TR><TD><I>&nbsp;&nbsp;&nbsp; <IMG ALIGN=CENTER SRC=ICONES/purpleball.png> un groupe  </I></TD>\n");
			print("<TD>: <SELECT name=\"nom_groupe\" onChange=\"javascript:document.edit_predeinst.nom_dns.selectedIndex=0\">\n");
			if ($applicable_a == "nom_dns")
			{
				print("<OPTION value=\"\"></OPTION>\n");
				$request = "SELECT nom_groupe FROM groupes";
				$result = mysql_query($request);
				for ($i=0;$i<mysql_num_rows($result);$i++) 
				{
					$line = mysql_fetch_array($result);
					print("<OPTION value=\"".$line["nom_groupe"]."\">".$line["nom_groupe"]."</OPTION>\n");
				}
				mysql_free_result($result);
				print("</SELECT></TD></TR>\n");
				print("<TR><I><TD>&nbsp;&nbsp;&nbsp; <IMG SRC=ICONES/purpleball.png> <FONT COLOR=RED>OU</FONT> à un ordinateur spécifique </I></TD>\n");
				print("<TD>: <SELECT name=\"nom_dns\" onChange=\"javascript:document.edit_predeinst.nom_groupe.selectedIndex=0\">\n");
				print("<OPTION value=\"\"></OPTION>\n");
				$request = "SELECT nom_dns FROM ordinateurs";
				$result = mysql_query($request);
				for ($i=0;$i<mysql_num_rows($result);$i++) 
				{
					$line = mysql_fetch_array($result);
					if ($valeur_application == $line["nom_dns"])
					{
						print("<OPTION SELECTED value=\"".$line["nom_dns"]."\">".$line["nom_dns"]."</OPTION>\n");
						continue;
					}
					print("<OPTION value=\"".$line["nom_dns"]."\">".$line["nom_dns"]."</OPTION>\n");
				}
			}
			else #$applicable_a = "nom_groupe"
			{
				print("<OPTION value=\"\"></OPTION>\n");
				$request = "SELECT nom_groupe FROM groupes";
				$result = mysql_query($request);
				for ($i=0;$i<mysql_num_rows($result);$i++) 
				{
					$line = mysql_fetch_array($result);
				 	if ($valeur_application == $line["nom_groupe"])
					{
						print("<OPTION SELECTED value=\"".$line["nom_groupe"]."\">".$line["nom_groupe"]."</OPTION>\n");
						continue;
					}
					print("<OPTION value=\"".$line["nom_groupe"]."\">".$line["nom_groupe"]."</OPTION>\n");
				}
				mysql_free_result($result);
				print("</SELECT></TD></TR>\n");
				print("<TR><I><TD>&nbsp;&nbsp;&nbsp; <IMG SRC=ICONES/purpleball.png> <FONT COLOR=RED>OU</FONT> à un ordinateur spécifique </I></TD>\n");
				print("<TD>: <SELECT name=\"nom_dns\" onChange=\"javascript:document.edit_predeinst.nom_groupe.selectedIndex=0\">\n");
				print("<OPTION value=\"\"></OPTION>\n");
				$request = "SELECT nom_dns FROM ordinateurs";
				$result = mysql_query($request);
				for ($i=0;$i<mysql_num_rows($result);$i++) 
				{
					$line = mysql_fetch_array($result);
					print("<OPTION value=\"".$line["nom_dns"]."\">".$line["nom_dns"]."</OPTION>\n");
				}
			}
			FinTable();
			print("<H2>Logiciels associés à ce predeinstall script</H2>");
			# On recupère les logiciels associés à ce script
			$request="SELECT id_logiciel from pdis_est_associe_a where id_script=\"$id_script\"";
			$result=mysql_query($request);
			$i = 1;
			while ($ligne = mysql_fetch_array($result))
			{
				$id_logiciels_associes[$i++] = $ligne["id_logiciel"];
			}
			mysql_free_result($result);
			# On affiche les logiciels
			$request="SELECT * FROM logiciels ORDER BY nom_logiciel,version";
			$result=mysql_query($request);
			$nb_logiciels = mysql_num_rows($result);
			EnteteTable("BORDER=2 CELLPADDING=2 CELLSPACING=1");
			$i = 1;
			while ($ligne = mysql_fetch_array($result))
			{
				print("<TR>\n");
				# On coche les logiciels auxquels le script est associé
				(array_search($ligne["id_logiciel"], $id_logiciels_associes)) ? $checked = "CHECKED" : $checked = "";
				print("<TD>\n<IMG ALIGN=CENTER WIDTH=\"$largeur_image_logiciel_et_package\" HEIGHT=\"$hauteur_image_logiciel_et_package\" SRC=\"ICONES/$ligne[icone]\">\n</TD><TD>\n<TT><FONT COLOR=RED>$ligne[nom_logiciel]</FONT></TT>, version <TT><FONT COLOR=GREEN>$ligne[version]</FONT></TT>, (<TT><FONT COLOR=RED>$ligne[nom_os]</FONT></TT>)\n</TD>\n<TD>\n <INPUT TYPE=CHECKBOX  NAME=\"checked_log[".$i++."]\" VALUE=\"$ligne[id_logiciel]\" $checked>\n </TD>\n");
				print("</TR>\n");
			}
			mysql_free_result($result);
			FinTable();
			print("<INPUT TYPE=HIDDEN NAME=nb_logiciels VALUE=\"$nb_logiciels\">\n");
			print("<INPUT TYPE=HIDDEN NAME=id_script VALUE=\"$id_script\">\n");
			print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
			FinFormulaire();
		}
		break;
	case "Validation":
		# On recupere les variables
		if ($mode == "edition")
		{
			$id_script = $_POST["id_script"];
			$nom_script = $_POST["nom_script"];
			$repertoire = $_POST["repertoire"];
			$nom_groupe = $_POST["nom_groupe"];
			$nom_dns = $_POST["nom_dns"];
			# On ajoute un slash final au chemin s'il n'y est pas déjà, sauf si répertoire est vide sinon on aurait $RemboPreDeinstScriptsDir/ (donc deux slashes finaux...)
			if (substr($repertoire,-1) != "/" and $repertoire != "") {$repertoire .= "/";}
			$nb_logiciels = $_POST["nb_logiciels"];
			$checked_log = $_POST["checked_log"];
		}
		if ($mode == "suppression")
		{
			$nb_scripts = $_POST["nb_scripts"];
			$checked_pdis = $_POST["checked_pdis"];
		}
		# toutes les variables ont ete recuperees
		# On attaque la base
		switch ($mode)
		{
			case "edition" :
				# On énumère les cas pour lesquels on de doit pas ajouter
		
				# Le nombre de logiciels choisis est nul
				if (count($checked_log) == 0)
				{
					print ("<P><I><FONT COLOR=RED>ATTENTION : Vous n'avez choisi aucun logiciel</FONT>. Utilisez le bouton <TT>BACK</TT> de votre navigateur pour faire une sélection valide.</I></P>");
					break;
				}
				# Le nom du predeinstall script n'est pas précisé
				if ($nom_script == "")
				{
					print("<P><I><FONT COLOR = RED>ATTENTION DANGER : </FONT>Vous n'avez pas donné de nom au predeinstall script que vous souhaitez ajouter... Veuillez utiliser le bouton Back/Précédent de votre navigateur pour modifier votre entrée.</I></P>\n");
					break;
				}
				# Pas de valeur d'application... : on interdit l'ajout
				if ($nom_groupe == "" and $nom_dns == "")
				{
					print("<P><I><FONT COLOR = RED>ATTENTION DANGER : </FONT>Vous n'avez pas spécifié à quoi s'applique ce predeinstall script... Veuillez utiliser le bouton Back/Précédent de votre navigateur pour modifier votre entrée.</I></P>\n");
					break;
				}
		
				# Reste des cas nécessitant des requêtes
				# On vérifie qu'on n'insère pas un predeinstall script déjà existant i.e. de même nom dans le même répertoire mais avec un id_script
				# qui n'est pas celui du script courant...
				$request = "SELECT COUNT(*) AS total FROM predeinstall_scripts WHERE nom_script=\"$nom_script\" AND repertoire=\"$repertoire\" AND id_script<>\"$id_script\"";
				$result = mysql_query($request);
				$line = mysql_fetch_array($result);
				$predeinstscript_meme_rep_deja_existant = ($line["total"] != 0);
				mysql_free_result($result);
				if ($predeinstscript_meme_rep_deja_existant)
				{
					print("<P><I><FONT COLOR = RED>ATTENTION DANGER : </FONT>Un predeinstall script, qui n'est pas celui que vous éditez, de nom <FONT COLOR=RED>$nom_script</FONT> existe déjà dans le répertoire $RemboPreDeinstScriptsDir<FONT COLOR=RED>$repertoire</FONT> ... Veuillez utiliser le bouton Back/Précédent de votre navigateur pour modifier votre entrée.</I></P>\n");
					break;
				}

		 		# OK, on peut attaquer la base
				# 1. On corrige dans la table predeinstall_scripts
				# 1.1 On détermine si c'est groupe ou nom_dns qui a été choisi
				if ($nom_dns != "")
				{
					$applicable_a = "nom_dns"; 
					$valeur_application = $nom_dns;
				}
				else
				{
					$applicable_a = "nom_groupe"; 
					$valeur_application = $nom_groupe;
				}
				# 1.2 On corrige à l'aise
				mysql_query("UPDATE predeinstall_scripts SET id_script=\"$id_script\", nom_script=\"$nom_script\", repertoire=\"$repertoire\", applicable_a=\"$applicable_a\", valeur_application=\"$valeur_application\" WHERE id_script=\"$id_script\"");
				print("<P><I>Le predeinstall script <FONT COLOR=RED>$nom_script</FONT> (id_script=$id_script) a été corrigé sans stress dans la table des predeinstall scripts.</I></P>\n");
				# 2. On va réinsérer dans la table pdis_est_associe_a
				# 2.1 On détruit les anciennes associations de logiciels
				mysql_query("DELETE FROM pdis_est_associe_a where id_script=\"$id_script\"");
				# 2.2 On cree la liste des logiciels concernes
				$nb_logiciels_concernes=0;
				$liste_id_logiciels_concernes="";
				for($i=1;$i<=$nb_logiciels;$i++)
				{
					if (isset($checked_log[$i]))
					{
						$nb_logiciels_concernes++;
						$id_logiciels_concernes[$nb_logiciels_concernes] = $checked_log[$i];
						$liste_id_logiciels_concernes .= $id_logiciels_concernes[$nb_logiciels_concernes]." ";
					}
				}
		
				# 2.3 On insère serein dans la table pdis_est_associe_a
				# On initialise la clause WHERE
				$clause_where=" WHERE 1=0";
				for($i=1;$i<=$nb_logiciels_concernes;$i++)
				{
					mysql_query("INSERT INTO pdis_est_associe_a (id_script, id_logiciel) VALUES(\"$id_script\", \"$id_logiciels_concernes[$i]\")");
					$clause_where .= " OR id_logiciel=\"$id_logiciels_concernes[$i]\"";
				}
				# Pour l'affichage on va récupérer les noms des logiciels auxquels 
				# on va associer un predeinstall script
				$request = "SELECT id_logiciel, nom_logiciel FROM logiciels".$clause_where;
				$result = mysql_query($request);
				$logiciels_associes = "<UL>\n";
				while ($ligne = mysql_fetch_array($result))
				{
					$logiciels_associes .= "<LI>$ligne[nom_logiciel] (<FONT SIZE=-1>id_logiciel = $ligne[id_logiciel])</FONT></LI>\n";
				}
				$logiciels_associes .= "</UL>\n";
				mysql_free_result($result);
				print("<I><P>Le script de predeinstall $nom_script a été associé dans la base aux logiciels :</P>".$logiciels_associes."</I>");
				break;
				################################## LA SUITE EST A FAIRE !!!!!!!
			case "suppression" :
				# Le nombre de predeinstall scripts choisis est non nul
				if (count($checked_pdis) > 0)
				{
					$nb_scripts_concernes=0;
					$liste_id_scripts_concernes="";
					for($i=1;$i<=$nb_scripts;$i++)
					{
						if (isset($checked_pdis[$i]))
						{
							$nb_scripts_concernes++;
							$id_scripts_concernes[$nb_scripts_concernes] = $checked_pdis[$i];
							$liste_id_scripts_concernes .= $id_scripts_concernes[$nb_scripts_concernes]." ";
						}
					}
							# On initialise la clause WHERE
							$clause_where=" WHERE 1=0";
							for($i=1;$i<=$nb_scripts_concernes;$i++)
							{
								$clause_where .= " OR id_script=\"$id_scripts_concernes[$i]\"";
							}
							# Les tables où $id_script apparaît
							$tables_concernees = array("predeinstall_scripts", "pdis_est_associe_a");
							# La liste des tables séparées par des "," pour l'affichage
							$liste_tables_concernees = implode(", ", $tables_concernees);
							# Pour l'affichage on va récupérer les noms des scripts qu'on va supprimer
							$request = "SELECT id_script, nom_script, repertoire FROM predeinstall_scripts".$clause_where;
							$result = mysql_query($request);
							$scripts_detruits = "<UL>\n";
							while ($ligne = mysql_fetch_array($result))
							{
								$scripts_detruits .= "<LI>$ligne[nom_script], répertoire $ligne[repertoire] (<FONT SIZE=-1>id_script = $ligne[id_script])</FONT></LI>\n";
							}
							$scripts_detruits .= "</UL>\n";
							mysql_free_result($result);
							# On detruit les enregistrements des scripts concernes dans les
							# tables concernees
							foreach($tables_concernees as $table)
							{
								$request="DELETE FROM $table".$clause_where;
								mysql_query($request);
							}
							print("<P>Les scripts suivants ont été supprimés de la base (tables <TT>$liste_tables_concernees</TT>) :</P>".$scripts_detruits);
							print("<P><I><FONT COLOR = RED>MAIS ATTENTION :</FONT> rien n'a été fait ici pour supprimer ces scripts sur le serveur REMBO ; il vous faudra y intervenir si vous souhaitez qu'ils soient détruits AUSSI du point de vue de REMBO. <FONT SIZE=-1 COLOR=GREEN>Notez que leur présence sous REMBO ne pose pas de problème sous JeDDLaJ : pour lui, ils ne sont plus référencés dans la base, donc ils n'existent plus...</FONT></I></FONT></P>");
				}# end if(count($checked_pdis) > 0)
				else
				{
					print ("<P><I><FONT COLOR=RED>ATTENTION : Vous n'avez choisi aucun predeinstall script</FONT>. Utilisez le bouton <TT>BACK</TT> de votre navigateur pour faire une sélection valide.</I></P>");
				}
		}
		break;
}

print("<BR><HR><P><CENTER><A HREF=accueil.php TARGET=\"_top\">Retour</A></CENTER></P>\n");

DisconnectMySQL();
PiedPage();
?>

