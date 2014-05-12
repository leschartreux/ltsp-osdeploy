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
# toutes les variables ont ete recuperees

entete("G�rard Milhaud & Fr�d�ric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Ajout package ($action)");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);
print("<CENTER><H1>Ajout de packages</H1></CENTER>\n");

switch ($action)
{
	case "NewPackage":
		print("<H2>Package associ� � un logiciel <A HREF=\"ajouter_package.php?action=NewPackageNewSoft\">nouveau</A> ou <A HREF=\"ajouter_package.php?action=NewPackageOldSoftChoixOS\">existant</A> ?</H2>\n");
#		print("<CENTER>");
		print("<FONT SIZE=-1><I><B>Logiciel nouveau</B> = logiciel <FONT COLOR=RED>non pr�sent</FONT> dans la base pour le syst�me d'exploitation du package que vous voulez ajouter</I></FONT><BR>\n");
		print("<FONT SIZE=-1><I><B>Logiciel existant</B> = logiciel <FONT COLOR=RED>d�j� pr�sent</FONT> dans la base pour le syst�me d'exploitation du package que vous voulez ajouter</I></FONT>");
#		print("</CENTER>");
		break;
	case "NewPackageOldSoftChoixOS":
		EnteteFormulaire("POST","ajouter_package.php?action=NewPackageOldSoftChoixLogiciel");
		print("<FONT COLOR=BLUE SIZE=+1>OS associ� au package � ajouter :</I> </FONT> <SELECT NAME=nom_os>\n");
		foreach ($GLOBALS['oss'] as $os)
		{
		    print("<OPTION VALUE=\"$os\">$os</OPTION>\n");
		}
		print("</SELECT>\n");
		print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
		FinFormulaire();
		break;
	case "NewPackageOldSoftChoixLogiciel":
		# On recupere les variables
		$nom_os = $_POST["nom_os"];
		# toutes les variables ont ete recuperees
		# On veut, dans la table logiciels, seulement ceux qui correspondent � des logiciels, et non � des OS, donc on ne prend 
		# que ceux dont l'id appara�t dans la table packages (car un package est associ� � un "vrai logiciel", pas un OS...
		$request="SELECT a.id_logiciel, nom_logiciel, version, icone FROM logiciels AS a, packages AS b WHERE a.id_logiciel=b.id_logiciel AND nom_os=\"$nom_os\" GROUP BY id_logiciel ORDER BY nom_logiciel,version";
		$result=mysql_query($request);
		print("<H2>S�lectionnez le logiciel associ� au package � ajouter :</H2>\n");
		EnteteFormulaire("POST","ajouter_package.php?action=NewPackageOldSoftAddPack");
		print("<INPUT TYPE=HIDDEN NAME=nom_os VALUE=\"$nom_os\">\n");
		EnteteTable("BORDER=2 CELLPADDING=2 CELLSPACING=1");
		while ($ligne = mysql_fetch_array($result))
		{
			empty($ligne['version']) ? $version="non sp�cifi�e" : $version=$ligne['version'];
			print("<TR>\n");
			print("<TD>\n<IMG ALIGN=CENTER WIDTH=\"$largeur_image_logiciel_et_package\" HEIGHT=\"$hauteur_image_logiciel_et_package\" SRC=\"ICONES/$ligne[icone]\">\n</TD><TD>\n$ligne[nom_logiciel], version $version\n</TD>\n<TD>\n <INPUT TYPE=RADIO NAME=\"id_logiciel\" VALUE=\"$ligne[id_logiciel]\">\n </TD>\n");
			print("</TR>\n");
		}
		mysql_free_result($result);
		FinTable();
		print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
		FinFormulaire();
		break;
	case "NewPackageOldSoftAddPack":
		# On recupere les variables
		$nom_os = $_POST["nom_os"];
		$id_logiciel = $_POST["id_logiciel"];
		# toutes les variables ont ete recuperees
        	$request="SELECT id_logiciel, nom_logiciel, version, icone FROM logiciels WHERE id_logiciel=\"$id_logiciel\"";
		$result=mysql_query($request);
		$ligne = mysql_fetch_array($result);
		mysql_free_result($result);
		empty($ligne['version']) ? $version="non sp�cifi�e" : $version=$ligne['version'];
		print("<H2>Logiciel</H2>");
		EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
		print("<TR><TD><IMG ALIGN=CENTER WIDTH=\"$largeur_image_logiciel_et_package\" HEIGHT=\"$hauteur_image_logiciel_et_package\" SRC=\"ICONES/$ligne[icone]\"></TD><TD><I>$ligne[nom_logiciel], version $version</I></TD></TR>\n");
		FinTable();
		print("<H2>Packages existant pour ce logiciel</H2>");
		$request = "SELECT nom_package, specificite, valeur_specificite, repertoire FROM packages WHERE id_logiciel=\"$id_logiciel\"";
		$result = mysql_query($request);
		$packages_existant = "<UL>\n";
		$nb_packages_existant = 0;
		while ($curr_ligne = mysql_fetch_array($result))
		{
			$packages_existant .= "<LI><FONT COLOR=RED>$curr_ligne[nom_package]</FONT>, specificit� <FONT COLOR=RED>$curr_ligne[specificite]</FONT>";
			if ($curr_ligne['specificite'] != "aucune") {$packages_existant .= " de valeur <FONT COLOR=RED>".$curr_ligne['valeur_specificite']."</FONT>";}
			$curr_ligne['repertoire'] == '' ? $repertoire_a_afficher = ", sans r�pertoire sp�cifi�" : $repertoire_a_afficher = ", r�pertoire <FONT COLOR=RED>".$curr_ligne['repertoire']."</FONT>";
			$packages_existant .= "$repertoire_a_afficher</LI>\n";
			$nb_packages_existant++;
		}
		$packages_existant .= "</UL>\n";
		mysql_free_result($result);
		($nb_packages_existant > 0) ? print $packages_existant : print "<I>Aucun package associ� � ce logiciel pour l'instant...</I>";
		print("<H2>Package � ajouter</H2>");
		EnteteFormulaire("POST","ajouter_package.php?action=NewPackageOldSoftValidation");
		print("<INPUT TYPE=HIDDEN NAME=id_logiciel VALUE=\"$ligne[id_logiciel]\">\n");
		print("<INPUT TYPE=HIDDEN NAME=nom_logiciel VALUE=\"$ligne[nom_logiciel]\">\n");
		print("<INPUT TYPE=HIDDEN NAME=version VALUE=\"$ligne[version]\">\n");
		print("<INPUT TYPE=HIDDEN NAME=nom_os VALUE=\"$nom_os\">\n");
		EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
		print("<TR><TD><I>Nom package</I> </TD><TD>: <INPUT TYPE=TEXT NAME=nom_package SIZE=50 VALUE=\"\"></TD></TR>\n");
		print("<TR><TD><I>R�pertoire</I> </TD><TD>: <FONT SIZE=-1 COLOR=GREEN><I>$RemboPackagesDir</I></FONT><INPUT TYPE=TEXT NAME=repertoire SIZE=50 VALUE=\"\"></TD></TR>\n");

		print("<TR><TD><I>Sp�cificit�</I> </TD><TD>: <SELECT NAME=specificite>\n");
		print("<OPTION VALUE=\"aucune\">aucune</OPTION>\n");
		print("<OPTION VALUE=\"nom_dns\">nom_dns</OPTION>\n");
		print("<OPTION VALUE=\"signature\">signature</OPTION>\n");
		print("<OPTION VALUE=\"id_composant\">id_composant</OPTION>\n");
		print("</SELECT>\n");
		print("</TD></TR>\n");
		print("<TR><TD><I>Valeur Sp�cificit�</I> </TD><TD>: <INPUT TYPE=TEXT NAME=valeur_specificite SIZE=50 VALUE=\"\"></TD></TR>\n");
		FinTable();
		print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
		FinFormulaire();
		break;
	case "NewPackageOldSoftValidation":
		# On recupere les variables
		$id_logiciel = $_POST["id_logiciel"];
		$nom_logiciel = $_POST["nom_logiciel"];
		empty($_POST['version']) ? $version="non sp�cifi�e" : $version=$_POST['version'];
		$nom_os = $_POST["nom_os"];
		$nom_package = $_POST["nom_package"];
		$repertoire = $_POST["repertoire"];
		# On ajoute un slash final au chemin s'il n'y est pas d�j�, sauf si r�pertoire est vide sinon 
		# on aurait $RemboPackagesDir/ (donc deux slashes finaux...)
		if (substr($repertoire,-1) != "/" and $repertoire != "") {$repertoire .= "/";}
		$specificite = $_POST["specificite"];
		$valeur_specificite = $_POST["valeur_specificite"];
		# toutes les variables ont ete recuperees

		# On ne veut pas de fichiers de meme nom dans le meme repertoire en dehors du cas ou ce sont des entrees multiples 
		# pour le meme logiciel, i.e. m�mes id_logiciel ET specificites identiques ET valeurs specificite differentes. 
		# La requete suivante exprime les cas interdits ET les compte : on ne veut donc editer que si la requete renvoie 0 
		$request = "SELECT COUNT(*) AS total FROM packages WHERE nom_package=\"$nom_package\" AND repertoire=\"$repertoire\" AND (id_logiciel<>\"$id_logiciel\" OR specificite<>\"$specificite\" OR valeur_specificite=\"$valeur_specificite\")";
		# On ne veut pas, pour le meme logiciel, qu'il y ait plus d'un package non specifique OU qu'il y ait des packages specifiques 
		# de type de specificite differentes OU (de meme type de specificite ET de meme valeur specificite).
		# La requete suivante exprime ces cas interdits ET les compte : on ne veut donc editer que si la requete renvoie 0 
		$request2 = "SELECT COUNT(*) AS total FROM packages WHERE id_logiciel=\"$id_logiciel\" AND (\"$specificite\"=\"aucune\" OR specificite=\"aucune\" OR specificite<>\"$specificite\" OR (specificite=\"$specificite\" AND valeur_specificite=\"$valeur_specificite\"))";

		$result = mysql_query($request);
		$result2 = mysql_query($request2);
		$line = mysql_fetch_array($result);
		$line2 = mysql_fetch_array($result2);
		$package_incompatible_meme_rep_deja_existant = ($line["total"] != 0);
		$package_incompatible_meme_distrib_deja_existant = ($line2["total"] != 0);
		mysql_free_result($result);
		mysql_free_result($result2);

		if ($repertoire=='') {$phrase_rep="dans le m�me r�pertoire";} else {$phrase_rep="dans le r�pertoire <FONT COLOR=RED>$repertoire</FONT>";}
		if ($package_incompatible_meme_rep_deja_existant)
		{
			print("<P><I><FONT COLOR = RED>ATTENTION DANGER : </FONT>Un AUTRE package de nom <FONT COLOR=RED>$nom_package</FONT> existe d�j� $phrase_rep... Or, ceci n'est possible que si les packages sont attach�es au m�me logiciel, ont le m�me type de sp�cifit� mais une valeur de sp�cificit� diff�rente(*). Le cas pr�sent ne correspond pas � cette situation... Veuillez utiliser le bouton Back/Pr�c�dent de votre navigateur pour modifier votre entr�e.</I></P>\n");
			print("<P><FONT SIZE=-2>(*) Il s'agit alors d'entr�es multiples pour le m�me package, ce qui est souhaitable dans certains cas : <BR> - le m�me type de machine avec des composants identiques mais plac�s diff�remment sur le bus peut avoir deux signatures diff�rentes car l'algorithme de calcul de la signature Rembo d�pend de l'ordre de la d�tection des composants sur le bus ;<BR> - un m�me package de sp�cificit� id_composant peut parfois convenir pour plusieurs composants diff�rents : par exemple le package d'un driver vid�o.</FONT></P>\n");
		}
		elseif ($package_incompatible_meme_distrib_deja_existant)
		{
			$phrase_valeur_specificite = ( ($specificite == "aucune" or empty($valeur_specificite)) ? "" : " de valeur <FONT COLOR=RED>".$valeur_specificite."</FONT>");
			print("<P><I><FONT COLOR = RED>ATTENTION DANGER : </FONT>le package que vous souhaitez ajouter (<FONT COLOR = RED>$nom_package</FONT>, sp�cificit� <FONT COLOR = RED>$specificite</FONT>".$phrase_valeur_specificite.") pr�sente des caract�ristiques incompatibles avec au moins un des packages d�j� existants associ�s au m�me logiciel (<FONT COLOR = RED>$nom_logiciel</FONT>, <FONT COLOR = RED>$version</FONT>). L'incompatibilit� provient d'une des causes suivantes(*) :<BR>\n<UL>\n<LI>Arrgh : le package que vous ajoutez n'est pas sp�cifique ET il existe d�j� un package non sp�cifique pour cette distribution(*) !!! Reculez impies, fuyez devant les pr�ceptes JeDDLaJiques !!! ;</LI>\n<LI>Atroce malheur : le package que vous ajoutez est d'un type de sp�cifit� diff�rent de celui des packages d�j� existants pour cette distribution !!! Inconscient !!! Vous risquez de r�veiller des forces mal�fiques dont vous ne pouvez appr�hender la puissance !!! ;</LI>\n<LI>Horreur insondable : le package que vous ajoutez est du m�me type de sp�cifit� et de m�me valeur de sp�cificit� qu'un des packages d�j� existants pour ce logiciel !!! Vous crachez � la face du Diable !!! Vous pissez face au vent au Cap Horn !!! Puisse la puissance de JeDDLaJ vous prot�ger contre tous les d�mons que vous d�fiez !!!</LI>\n</UL>\n");
			print("<P><FONT SIZE=-2>(*) Tous ces cas violent ce principe fondamental de JeDDLaJ : \"Pour une machine et un logiciel donn�s, il ne doit y avoir qu'un package possible\". Ceci pour que JeDDLaJ puisse toujours op�rer le choix automatique du package lors des installations/r�installations d'un logiciel donn� sur une machine, ou un groupe quelconque de machines.</FONT></P>\n");
		}
		else # OK, on peut ajouter
		{
			# On ins�re dans la table packages
			$request = "INSERT INTO packages (id_logiciel, nom_package, repertoire, specificite, valeur_specificite) VALUES (\"$id_logiciel\", \"$nom_package\", \"$repertoire\", \"$specificite\", \"$valeur_specificite\")";
			$result = mysql_query($request);

			print("<P><I>Le package <FONT COLOR=RED>$nom_package</FONT> a �t� ajout� avec grand brio � la table des packages.</I></P>\n");
			print("<P><I>Il est donc d�sormais disponible pour toutes vos op�rations JeDDLaJiques � venir...<FONT SIZE=-1><FONT COLOR=RED> SOUS R�SERVE QUE </FONT>, bien s�r, le package $nom_package ait �t� d�pos� <FONT COLOR=RED>effectivement</FONT> sur le serveur REMBO, dans le r�pertoire $RemboPackagesDir$repertoire</I></FONT></P>");
		}
		break;
	case "NewPackageNewSoft":
		EnteteFormulaire("POST","ajouter_package.php?action=NewPackageNewSoftValidation");
		print("<H2>Logiciel</H2>");
		EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
		print("<TR><TD><I>Nom Logiciel</I> </TD><TD>: <INPUT TYPE=TEXT NAME=nom_logiciel SIZE=50 VALUE=\"\"></TD></TR>\n");
		print("<TR><TD><I>Version</I> </TD><TD>: <INPUT TYPE=TEXT NAME=version SIZE=50 VALUE=\"\"></TD></TR>\n");
		print("<TR><TD><I>Icone</I> </TD><TD>: <I><FONT SIZE=-1 COLOR=GREEN>&lt;JeDDLaJ's home sur serveur Web&gt;/ICONES/</I></FONT><INPUT TYPE=TEXT NAME=icone SIZE=50 VALUE=\"\"></TD></TR>\n");
		print("<TR><TD><I>Nom OS </I></TD><TD>: <SELECT NAME=nom_os>\n");
		foreach ($GLOBALS['oss'] as $os)
		{
		    print("<OPTION VALUE=\"$os\">$os</OPTION>\n");
		}
		print("</SELECT>\n");
		print("</TD></TR>\n");
		FinTable();
		print("<H2>Package</H2>");
		EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
		print("<TR><TD><I>Nom package</I> </TD><TD>: <INPUT TYPE=TEXT NAME=nom_package SIZE=50 VALUE=\"\"></TD></TR>\n");
		print("<TR><TD><I>R�pertoire</I> </TD><TD>: <FONT SIZE=-1 COLOR=GREEN><I>$RemboPackagesDir</I></FONT><INPUT TYPE=TEXT NAME=repertoire SIZE=50 VALUE=\"\"></TD></TR>\n");

		print("<TR><TD><I>Sp�cificit�</I> </TD><TD>: <SELECT NAME=specificite>\n");
		print("<OPTION VALUE=\"aucune\">aucune</OPTION>\n");
		print("<OPTION VALUE=\"nom_dns\">nom_dns</OPTION>\n");
		print("<OPTION VALUE=\"signature\">signature</OPTION>\n");
		print("<OPTION VALUE=\"id_composant\">id_composant</OPTION>\n");
		print("</SELECT>\n");
		print("</TD></TR>\n");
		print("<TR><TD><I>Valeur Sp�cificit�</I> </TD><TD>: <INPUT TYPE=TEXT NAME=valeur_specificite SIZE=50 VALUE=\"\"></TD></TR>\n");
		FinTable();
		print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
		FinFormulaire();
		break;
	case "NewPackageNewSoftValidation":
		# On recupere les variables
		$nom_logiciel = $_POST["nom_logiciel"];
		$icone = $_POST["icone"];
		if ($icone=="") {$icone = "defaulticon.jpg";};
		$version = $_POST["version"];
		empty($_POST['version']) ? $version_a_afficher="non sp�cifi�e" : $version_a_afficher=$_POST['version'];
		$nom_os = $_POST["nom_os"];
		$nom_package = $_POST["nom_package"];
		$repertoire = $_POST["repertoire"];
		# On ajoute un slash final au chemin s'il n'y est pas d�j�, sauf si r�pertoire est vide sinon 
		# on aurait $RemboPackagesDir/ (donc deux slashes finaux...)
		if (substr($repertoire,-1) != "/" and $repertoire != "") {$repertoire .= "/";}

		$specificite = $_POST["specificite"];
		$valeur_specificite = $_POST["valeur_specificite"];
		# toutes les variables ont ete recuperees


		# On v�rifie qu'on n'ins�re pas un logiciel d�j� existant ou un package deja existant...
		# LE LOGICIEL
		$request = "SELECT COUNT(*) AS total FROM logiciels WHERE nom_logiciel=\"$nom_logiciel\" AND version=\"$version\" AND nom_os=\"$nom_os\"";
		$result = mysql_query($request);
		$line = mysql_fetch_array($result);
		$logiciel_deja_existant = ($line["total"] != 0);
		mysql_free_result($result);
		# LE PACKAGE
		# On veut juste qu'il n'y en ait pas un autre de meme nom dans le meme repertoire.
		# Les autres cas, meme nom, autre repertoire, ou autre nom, meme repertoire ne sont pas
		# genants dans la mesure ou le nouveau package est associe a un nouveau logiciel et 
		# ne peut a ce titre etre confondue avec un ancien...
		$request2 = "SELECT COUNT(*) AS total FROM packages WHERE nom_package=\"$nom_package\" AND repertoire=\"$repertoire\"";
		$result2 = mysql_query($request2);
		$line2 = mysql_fetch_array($result2);
		$package_meme_nom_meme_rep_deja_existant = ($line2["total"] != 0);
		mysql_free_result($result2);
		if ($logiciel_deja_existant)
		{
			print("<P><I><FONT COLOR = RED>ATTENTION DANGER : </FONT>Le logiciel <FONT COLOR=RED>$nom_logiciel</FONT>, version <FONT COLOR=RED>$version_a_afficher</FONT> existe d�j� pour le syst�me d'exploitation <FONT COLOR = RED>$nom_os</FONT>... Veuillez utiliser le bouton Back/Pr�c�dent de votre navigateur pour modifier votre entr�e.</I></P>\n");
		}
		elseif ($package_meme_nom_meme_rep_deja_existant)
		{
			if ($repertoire=='') {$phrase_rep="dans le m�me r�pertoire";} else {$phrase_rep="dans le r�pertoire <FONT COLOR=RED>$repertoire</FONT>";}
			print("<P><I><FONT COLOR = RED>ATTENTION DANGER : </FONT>Un package de nom <FONT COLOR=RED>$nom_package</FONT>");
			print(" existe d�j� $phrase_rep... Ceci est interdit par JeDDLaJ, ainsi que par tout syst�me de fichiers ayant d�pass� la version pr�-alpha... Veuillez utiliser le bouton Back/Pr�c�dent de votre navigateur pour modifier votre entr�e.</I></P>\n");
		}
		else # OK, on peut ajouter
		{
			# On ins�re dans la table logiciels
			$request = "INSERT INTO logiciels (nom_logiciel, icone, version, nom_os) VALUES (\"$nom_logiciel\", \"$icone\", \"$version\", \"$nom_os\")";
			$result = mysql_query($request);
			$id_logiciel = mysql_insert_id();
			# On pourrait aussi faire avec cette fonction interne MySQL qui marche m�me si id_logiciel est un bigint, ce qui n'est pas le 
			# cas de la fonction PHP mysql_insert_id()...
			# $id_logiciel = mysql_query("SELECT(LAST_INSERT_ID())");
			print("<P><I>Le logiciel <FONT COLOR=RED>$nom_logiciel</FONT>, version <FONT COLOR=RED>$version_a_afficher</FONT> a �t� ajout� avec grand succ�s � la table des logiciels.</I></P>\n");

			# On ins�re dans la table packages
			$request = "INSERT INTO packages (id_logiciel, nom_package, repertoire, specificite, valeur_specificite) VALUES (\"$id_logiciel\", \"$nom_package\", \"$repertoire\", \"$specificite\", \"$valeur_specificite\")";
			$result = mysql_query($request);

			print("<P><I>Le package <FONT COLOR=RED>$nom_package</FONT> a �t� ajout� tr�s facilement � la table des packages.</I></P>\n");
			print("<P><I>Ils sont donc d�sormais tous deux disponibles pour toutes vos op�rations JeDDLaJiques � venir...<FONT SIZE=-1><FONT COLOR=RED> SOUS R�SERVE QUE </FONT>, bien s�r, le package $nom_package ait �t� d�pos� <FONT COLOR=RED>effectivement</FONT> sur le serveur REMBO, dans le r�pertoire $RemboPackagesDir$repertoire</I></FONT></P>");
		}
}

print("<BR><HR><P><CENTER><A HREF=accueil.php TARGET=\"_top\">Retour</A></CENTER></P>\n");

DisconnectMySQL();
PiedPage();
?>

