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
$action = $_POST["action"];
if (!isset($action)) { $action = $_GET["action"]; }
# toutes les variables ont ete recuperees

entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Édition package ($action)");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS[host], $GLOBALS[user], $GLOBALS[pwd]);
SelectDb($GLOBALS[db]);
print("<CENTER><H1>Edition de packages</H1></CENTER>\n");

switch ($action)
{
	case "ChoixOS":
		EnteteFormulaire("POST","editer_package.php?action=ChoixLogiciel");
		print("<FONT COLOR=BLUE SIZE=+1>OS associé au package à éditer :</I> </FONT> <SELECT NAME=nom_os>\n");
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
		print("<H2>Sélectionnez le logiciel associé au package à éditer :</H2>\n");
		EnteteFormulaire("POST","editer_package.php?action=ChoixPack");
		EnteteTable("BORDER=2 CELLPADDING=2 CELLSPACING=1");
		while ($ligne = mysql_fetch_array($result))
		{
			print("<TR>\n");
			print("<TD>\n<IMG ALIGN=CENTER SRC=\"ICONES/$ligne[icone]\">\n</TD><TD>\n$ligne[nom_logiciel], version $ligne[version]\n</TD>\n<TD>\n <INPUT TYPE=RADIO NAME=\"id_logiciel\" VALUE=\"$ligne[id_logiciel]\">\n </TD>\n");
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
		# toutes les variables ont ete recuperees
        	$request="SELECT nom_logiciel, version, icone FROM logiciels WHERE id_logiciel=\"$id_logiciel\"";
		$result=mysql_query($request);
		$ligne = mysql_fetch_array($result);
		mysql_free_result($result);
		$nom_logiciel = $ligne[nom_logiciel];
		$version = $ligne[version];
		print("<H2>Logiciel</H2>");
		EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
		print("<TR><TD><IMG ALIGN=CENTER SRC=\"ICONES/$ligne[icone]\"></TD><TD><I>$nom_logiciel, version $version</I></TD></TR>\n");
		FinTable();
        	$request="SELECT * FROM packages WHERE id_logiciel=\"$id_logiciel\" ORDER BY nom_package";
		$result=mysql_query($request);
		$nb_packages = mysql_num_rows($result);
		# Un "s" à Package que s'il y en a plus d'un (ou 0, car à ce moment-là on parle de la potentialité d'existence
		# de plusieurs packages...) : on ne badine pas avec l'orthographe...
		($nb_packages > 1 OR $nb_packages==0) ? $pas_de_faute = "Packages" : $pas_de_faute = "Package";
		print("<H2>$pas_de_faute</H2>");
		if ($nb_packages > 0)
		{
			EnteteFormulaire("POST","editer_package.php?action=EditPack");
			EnteteTable("BORDER=2 CELLPADDING=2 CELLSPACING=1");
			while ($ligne = mysql_fetch_array($result))
			{
				print("<TR>\n");
				print("<TD>\n<FONT COLOR=RED>$ligne[nom_package]</FONT>, <I>spécificité</I> <FONT COLOR=RED>$ligne[specificite]</FONT>");
				if ($ligne[specificite] != "aucune") {print("<I> de valeur</I> <FONT COLOR=RED>".$ligne[valeur_specificite]."</FONT>");}
				print("\n</TD>\n<TD>\n <INPUT TYPE=RADIO NAME=\"id_package\" VALUE=\"$ligne[id_package]\">\n </TD>\n");
				print("</TR>\n");
			}
			mysql_free_result($result);
			FinTable();
			print("<INPUT TYPE=HIDDEN NAME=id_logiciel VALUE=\"$id_logiciel\">\n");
			print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
			FinFormulaire();
		}
		else
		{
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
		}
		break;
	case "EditPack":
		# On recupere les variables
		$id_package = $_POST["id_package"];
		$id_logiciel = $_POST["id_logiciel"];
		# toutes les variables ont ete recuperees
        	$request="SELECT id_logiciel, nom_logiciel, version, icone FROM logiciels WHERE id_logiciel=\"$id_logiciel\"";
		$result=mysql_query($request);
		$ligne = mysql_fetch_array($result);
		mysql_free_result($result);
		print("<H2>Logiciel</H2>");
		EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
		print("<TR><TD><IMG ALIGN=CENTER SRC=\"ICONES/$ligne[icone]\"></TD><TD><I>$ligne[nom_logiciel], version $ligne[version]</I></TD></TR>\n");
		FinTable();
		print("<H2>Package</H2>");
        	$request="SELECT * FROM packages WHERE id_package=\"$id_package\"";
		$result=mysql_query($request);
		$ligne = mysql_fetch_array($result);
		mysql_free_result($result);
		EnteteFormulaire("POST","editer_package.php?action=Validation");
		print("<INPUT TYPE=HIDDEN NAME=id_logiciel VALUE=\"$ligne[id_logiciel]\">\n");
		print("<INPUT TYPE=HIDDEN NAME=id_package VALUE=\"$ligne[id_package]\">\n");
		print("<INPUT TYPE=HIDDEN NAME=nom_os VALUE=\"$ligne[nom_os]\">\n");
		EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
		print("<TR><TD><I>Nom package</I> </TD><TD>: <INPUT TYPE=TEXT NAME=nom_package SIZE=50 VALUE=\"$ligne[nom_package]\"></TD></TR>\n");
		print("<TR><TD><I>Répertoire</I> </TD><TD>: <FONT SIZE=-1 COLOR=GREEN><I>$RemboPackagesDir</I></FONT><INPUT TYPE=TEXT NAME=repertoire SIZE=50 VALUE=\"$ligne[repertoire]\"></TD></TR>\n");
		print("<TR><TD><I>Spécificité</I> </TD><TD>: <SELECT NAME=specificite>\n");
		$ligne[specificite] == "aucune" ? $selected_aucune = "SELECTED" : $selected_aucune = "";
		$ligne[specificite] == "nom_dns" ? $selected_nom_dns = "SELECTED" : $selected_nom_dns = "";
		$ligne[specificite] == "signature" ? $selected_signature = "SELECTED" : $selected_signature = "";
		$ligne[specificite] == "id_composant" ? $selected_id_composant = "SELECTED" : $selected_id_composant = "";
		print("<OPTION $selected_aucune VALUE=\"aucune\">aucune</OPTION>\n");
		print("<OPTION $selected_nom_dns VALUE=\"nom_dns\">nom_dns</OPTION>\n");
		print("<OPTION $selected_signature VALUE=\"signature\">signature</OPTION>\n");
		print("<OPTION $selected_id_composant VALUE=\"id_composant\">id_composant</OPTION>\n");
		print("</SELECT>\n");
		print("</TD></TR>\n");
		print("<TR><TD><I>Valeur Spécificité</I> </TD><TD>: <INPUT TYPE=TEXT NAME=valeur_specificite SIZE=50 VALUE=\"$ligne[valeur_specificite]\"></TD></TR>\n");
		FinTable();
		print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
		FinFormulaire();
		break;
	case "Validation":
		# On recupere les variables
		$id_logiciel = $_POST["id_logiciel"];
		$nom_os = $_POST["nom_os"];
		$nom_package = $_POST["nom_package"];
		$repertoire = $_POST["repertoire"];
		# On ajoute un slash final au chemin s'il n'y est pas déjà
		if (substr($repertoire,-1) != "/") {$repertoire .= "/";}
		$specificite = $_POST["specificite"];
		$valeur_specificite = $_POST["valeur_specificite"];
		# toutes les variables ont ete recuperees
		# On corrige dans la table packages
		mysql_query("UPDATE packages SET id_logiciel=\"$id_logiciel\", nom_package=\"$nom_package\", repertoire=\"$repertoire\", specificite=\"$specificite\", valeur_specificite=\"$valeur_specificite\" WHERE id_package=\"$id_package\"");
		print("<P><I>Le package <FONT COLOR=RED>$nom_package</FONT> a été mis à jour souplement dans la table des packages.</I></P>\n");
		print("<P><I><FONT COLOR = RED>MAIS ATTENTION :</FONT> prenez grand soin de répercuter toute modification sur le serveur REMBO, en particulier les changements de nom de pacakge ou de répertoire, sous peine de perdre complètement la cohérence de la base !!!</I></FONT></P>");
}

print("<BR><HR><P><CENTER><A HREF=accueil.php TARGET=\"_top\">Retour</A></CENTER></P>\n");

DisconnectMySQL();
PiedPage();
?>

