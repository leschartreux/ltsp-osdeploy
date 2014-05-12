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
# toutes les variables ont ete recuperees

entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Ajout image de base ($action)");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);
print("<CENTER><H1>Ajout d'images de base</H1></CENTER>\n");

switch ($action)
{
	case "NewIdb":
		print("<H2>Image de base associée à une <A HREF=\"ajouter_image_de_base.php?action=NewIdbNewDistrib\">nouvelle</A> ou une <A HREF=\"ajouter_image_de_base.php?action=ChoixOS\">ancienne</A> distribution?</H2>\n");
#		print("<CENTER>");
		print("<FONT SIZE=-1><I><B>Nouvelle distribution</B> = distribution <FONT COLOR=RED>non présente</FONT> dans la base pour le système d'exploitation de l'image de base que vous voulez ajouter. Le concept de distribution correspond au couplet (nom du logiciel, version) dans la table logiciels, pour ceux des logiciels qui sont des OS. Par exemple, pour Linux, <B>Slackware, version 10.0</B> ou <B>Debian, version Sid</B> si vous n'aviez jusqu'à présent que des <B>Debian version Woody</B>. Pour Windows, c'est pareil, mais un peu moins instinctif ;) : pour le système Windows XP, <B>Windows XP, version SP2</B> sera une nouvelle distribution si vous n'aviez que des <B>Windows XP, version SP1</B>, ou des <B>Windows XP, sans numéro de version</B>.</I></FONT><BR>\n");
		print("<FONT SIZE=-1><I><B>Distribution existante</B> = distribution <FONT COLOR=RED>déjà présente</FONT> dans la base pour le système d'exploitation de l'image de base que vous voulez ajouter</I></FONT>");
#		print("</CENTER>");
		break;
	# On factorise le code pour le choix de l'OS, avec un bidouillage vilain (reévaluation de $action) pour savoir dans quel cas on est...
	case "ChoixOS":
		EnteteFormulaire("POST","ajouter_image_de_base.php?action=NewIdbOldDistribChoixLogiciel"); 
		print("<FONT COLOR=BLUE SIZE=+1>Type d'OS associé à l'image de base à ajouter :</I> </FONT> <SELECT NAME=nom_os>\n");
		foreach ($GLOBALS['oss'] as $os)
		{
		    print("<OPTION VALUE=\"$os\">$os</OPTION>\n");
		}
		print("</SELECT>\n");
		print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
		FinFormulaire();
		break;
	case "NewIdbOldDistribChoixLogiciel":
		# On recupere les variables
		$nom_os = $_POST["nom_os"];
		# toutes les variables ont ete recuperees
		# On veut, dans la table logiciels, seulement ceux qui correspondent à des os, et non à des logiciels, donc on ne prend 
		# que ceux dont l'id apparaît dans la table images_de_base (car une image de base est associée à un OS, pas à un logiciel...)
		$request="SELECT id_os, nom_logiciel, version, icone FROM logiciels AS a, images_de_base AS b WHERE id_logiciel=id_os AND nom_os=\"$nom_os\" GROUP BY id_logiciel ORDER BY nom_logiciel,version";
		$result=mysql_query($request);
		print("<H2>Sélectionnez la distribution associée à l'image de base à ajouter :</H2>\n");
		EnteteFormulaire("POST","ajouter_image_de_base.php?action=NewIdbOldDistribAjoutIdb");
		print("<INPUT TYPE=HIDDEN NAME=nom_os VALUE=\"$nom_os\">\n");
		EnteteTable("BORDER=2 CELLPADDING=2 CELLSPACING=1");
		while ($ligne = mysql_fetch_array($result))
		{
			print("<TR>\n");
			print("<TD>\n<IMG ALIGN=CENTER WIDTH=\"$largeur_image_distrib_et_idb\" HEIGHT=\"$hauteur_image_distrib_et_idb\" SRC=\"ICONES/$ligne[icone]\">\n</TD><TD>\n$ligne[nom_logiciel]");
			if ($ligne['version'] != "") { print(", version $ligne[version]"); }
			print("\n</TD>\n<TD>\n <INPUT TYPE=RADIO NAME=\"id_os\" VALUE=\"$ligne[id_os]\">\n </TD>\n");
			print("</TR>\n");
		}
		mysql_free_result($result);
		FinTable();
		print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
		FinFormulaire();
		break;
	case "NewIdbOldDistribAjoutIdb":
		# On recupere les variables
		$nom_os = $_POST["nom_os"];
		$id_os = $_POST["id_os"];
		# toutes les variables ont ete recuperees
        	$request="SELECT id_logiciel, nom_logiciel, version, icone FROM logiciels WHERE id_logiciel=\"$id_os\"";
		$result=mysql_query($request);
		$ligne = mysql_fetch_array($result);
		mysql_free_result($result);
		print("<H2>Distribution</H2>");
		EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
		print("<TR><TD><IMG ALIGN=CENTER WIDTH=\"$largeur_image_distrib_et_idb\" HEIGHT=\"$hauteur_image_distrib_et_idb\" SRC=\"ICONES/$ligne[icone]\"></TD><TD><I>$ligne[nom_logiciel], version $ligne[version]</I></TD></TR>\n");
		FinTable();
		print("<H2>Images de base existant pour cette distribution</H2>");
		$request = "SELECT nom_idb, specificite, valeur_specificite, repertoire FROM images_de_base WHERE id_os=\"$id_os\"";
		$result = mysql_query($request);
		$idb_existantes = "<UL>\n";
		$nb_idb_existantes = 0;
		while ($curr_ligne = mysql_fetch_array($result))
		{
			$idb_existantes .= "<LI><FONT COLOR=RED>$curr_ligne[nom_idb]</FONT>, specificité <FONT COLOR=RED>$curr_ligne[specificite]</FONT>";
			if ($curr_ligne['specificite'] != "aucune") {$idb_existantes .= " de valeur <FONT COLOR=RED>".$curr_ligne['valeur_specificite']."</FONT>";}
			$curr_ligne['repertoire'] == '' ? $repertoire_a_afficher = ", sans répertoire spécifié" : $repertoire_a_afficher = ", répertoire <FONT COLOR=RED>".$curr_ligne['repertoire']."</FONT>";
			$idb_existantes .= "$repertoire_a_afficher.</LI>\n";
			$nb_idb_existantes++;
		}
		$idb_existantes .= "</UL>\n";
		mysql_free_result($result);
		($nb_idb_existantes > 0) ? print $idb_existantes : print "<I>Aucune image de base associé à ce logiciel pour l'instant...</I>";
		print("<H2>Image de base à ajouter</H2>");
		EnteteFormulaire("POST","ajouter_image_de_base.php?action=NewIdbOldDistribValidation");
		print("<INPUT TYPE=HIDDEN NAME=id_logiciel VALUE=\"$ligne[id_logiciel]\">\n");
		print("<INPUT TYPE=HIDDEN NAME=nom_logiciel VALUE=\"$ligne[nom_logiciel]\">\n");
		print("<INPUT TYPE=HIDDEN NAME=version VALUE=\"$ligne[version]\">\n");
		print("<INPUT TYPE=HIDDEN NAME=nom_os VALUE=\"$nom_os\">\n");
		EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
		print("<TR><TD><I>Nom Image de base</I> </TD><TD>: <INPUT TYPE=TEXT NAME=nom_idb SIZE=50 VALUE=\"\"></TD></TR>\n");
		print("<TR><TD><I>Répertoire</I> </TD><TD>: <FONT SIZE=-1 COLOR=GREEN><I>$RemboIDBDir</I></FONT><INPUT TYPE=TEXT NAME=repertoire SIZE=50 VALUE=\"\"></TD></TR>\n");

		print("<TR><TD><I>Spécificité</I> </TD><TD>: <SELECT NAME=specificite>\n");
		print("<OPTION VALUE=\"aucune\">aucune</OPTION>\n");
		print("<OPTION VALUE=\"nom_dns\">nom_dns</OPTION>\n");
		print("<OPTION VALUE=\"signature\">signature</OPTION>\n");
		print("<OPTION VALUE=\"id_composant\">id_composant</OPTION>\n");
		print("</SELECT>\n");
		print("</TD></TR>\n");
		print("<TR><TD><I>Valeur Spécificité</I> </TD><TD>: <INPUT TYPE=TEXT NAME=valeur_specificite SIZE=50 VALUE=\"\"></TD></TR>\n");
		FinTable();
		print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
		FinFormulaire();
		break;
	case "NewIdbOldDistribValidation":
		# On recupere les variables
		$id_os = $_POST["id_logiciel"];
		$nom_logiciel = $_POST["nom_logiciel"];
		empty($_POST['version']) ? $version="non spécifiée" : $version=$_POST['version'];
		$nom_os = $_POST["nom_os"];
		$nom_idb = $_POST["nom_idb"];
		$repertoire = $_POST["repertoire"];
		# On ajoute un slash final au chemin s'il n'y est pas déjà, sauf si répertoire est vide 
		# sinon on aurait $RemboIDBDir/ (donc deux slashes finaux...)
		if (substr($repertoire,-1) != "/" and $repertoire != "") {$repertoire .= "/";}
		$specificite = $_POST["specificite"];
		$valeur_specificite = $_POST["valeur_specificite"];
		# toutes les variables ont ete recuperees

		# On ne veut pas de fichiers de meme nom dans le meme repertoire en dehors du cas ou ce sont des entrees multiples 
		# pour la meme distribution, i.e. mêmes id_os ET specificites identiques ET valeurs specificite differentes. 
		# La requete suivante exprime les cas interdits ET les compte : on ne veut donc editer que si la requete renvoie 0 
		$request = "SELECT COUNT(*) AS total FROM images_de_base WHERE nom_idb=\"$nom_idb\" AND repertoire=\"$repertoire\" AND (id_os<>\"$id_os\" OR specificite<>\"$specificite\" OR valeur_specificite=\"$valeur_specificite\")";
		# On ne veut pas, pour la meme distribution qu'il y ait plus d'une image non specifique OU qu'il y ait des images specifiques 
		# de type de specificite differentes OU (de meme type de specificite ET de meme valeur specificite).
		# La requete suivante exprime ces cas interdits ET les compte : on ne veut donc editer que si la requete renvoie 0 
		$request2 = "SELECT COUNT(*) AS total FROM images_de_base WHERE id_os=\"$id_os\" AND (\"$specificite\"=\"aucune\" OR specificite=\"aucune\" OR specificite<>\"$specificite\" OR (specificite=\"$specificite\" AND valeur_specificite=\"$valeur_specificite\"))";

		$result = mysql_query($request);
		$result2 = mysql_query($request2);
		$line = mysql_fetch_array($result);
		$line2 = mysql_fetch_array($result2);
		$idb_incompatible_meme_rep_deja_existante = ($line["total"] != 0);
		$idb_incompatible_meme_distrib_deja_existante = ($line2["total"] != 0);
		mysql_free_result($result);
		mysql_free_result($result2);

		if ($repertoire=='') {$phrase_rep="dans le même répertoire";} else {$phrase_rep="dans le répertoire <FONT COLOR=RED>$repertoire</FONT>";}
		if ($idb_incompatible_meme_rep_deja_existante)
		{
			print("<P><I><FONT COLOR = RED>ATTENTION DANGER : </FONT>Une AUTRE image de base de nom <FONT COLOR=RED>$nom_idb</FONT> existe déjà $phrase_rep... Or, ceci n'est possible que si les images de base sont attachées à la même distribution, ont le même type de spécifité mais une valeur de spécificité différente(*). Le cas présent ne correspond pas à cette situation... Veuillez utiliser le bouton Back/Précédent de votre navigateur pour modifier votre entrée.</I></P>\n");
			print("<P><FONT SIZE=-2>(*) Il s'agit alors d'entrées multiples pour la même image de base, ce qui est souhaitable dans certains cas : <BR> - le même type de machine avec des composants identiques mais placés différemment sur le bus peut avoir deux signatures différentes car l'algorithme de calcul de la signature Rembo dépend de l'ordre de la détection des composants sur le bus ;<BR> - on peut vouloir restreindre une image de base à quelques machines ; on utilise alors la spécifité nom_dns pour différencier plusieurs entrées de cette image de base.</FONT></P>\n");
		}
		elseif ($idb_incompatible_meme_distrib_deja_existante)
		{
			$phrase_valeur_specificite = ( ($specificite == "aucune" or empty($valeur_specificite)) ? "" : " de valeur <FONT COLOR=RED>".$valeur_specificite."</FONT>");
			print("<P><I><FONT COLOR = RED>ATTENTION DANGER : </FONT>l'image de base que vous souhaitez ajouter (<FONT COLOR = RED>$nom_idb</FONT>, spécificité <FONT COLOR = RED>$specificite</FONT>".$phrase_valeur_specificite.") présente des caractéristiques incompatibles avec au moins une des images de base déjà existantes associées à la même distribution (<FONT COLOR = RED>$nom_logiciel</FONT>, <FONT COLOR = RED>$version</FONT>). L'incompatibilité provient d'une des causes suivantes(*) :<BR>\n<UL>\n<LI>Arrgh : l'image que vous ajoutez n'est pas spécifique ET il existe déjà une image de base non spécifique pour cette distribution(*) !!! Reculez impies, fuyez devant les préceptes JeDDLaJiques !!! ;</LI>\n<LI>Atroce malheur : l'image que vous ajoutez est d'un type de spécifité différent de celui des images déjà existantes pour cette distribution !!! Inconscient !!! Vous risquez de réveiller des forces maléfiques dont vous ne pouvez appréhender la puissance !!! ;</LI>\n<LI>Horreur insondable : l'image que vous ajoutez est du même type de spécifité et de même valeur de spécificité qu'une des images déjà existantes pour cette distribution !!! Vous crachez à la face du Diable !!! Vous pissez face au vent au Cap Horn !!! Puisse la puissance de JeDDLaJ vous protéger contre tous les démons que vous défiez !!!</LI>\n</UL>\n");
			print("<P><FONT SIZE=-2>(*) Tous ces cas violent ce principe fondamental de JeDDLaJ : \"Pour une machine et une distribution données, il ne doit y avoir qu'une image de base possible\". Ceci pour que JeDDLaJ puisse toujours opérer le choix automatique de l'image de base lors des installations/réinstallations d'une distribution donnée sur une machine, ou un groupe quelconque de machines.</FONT></P>\n");
		}
		else # OK, on peut ajouter
		{
			# On insère dans la table images_de_base
			$request = "INSERT INTO images_de_base (id_os, nom_idb, repertoire, specificite, valeur_specificite) VALUES (\"$id_os\", \"$nom_idb\", \"$repertoire\", \"$specificite\", \"$valeur_specificite\")";
			$result = mysql_query($request);

			print("<P><I>L'image de base <FONT COLOR=RED>$nom_idb</FONT> a été ajoutée avec grand brio à la table des images de base.</I></P>\n");
			print("<P><I>Elle est donc désormais disponible pour toutes vos opérations JeDDLaJiques à venir...<FONT SIZE=-1><FONT COLOR=RED> SOUS RÉSERVE QUE </FONT>, bien sûr, l'image de base $nom_idb ait été déposée <FONT COLOR=RED>effectivementi</FONT> sur le serveur REMBO, dans le répertoire $RemboIDBDir$repertoire</I></FONT></P>");
		}
		break;
	case "NewIdbNewDistrib":
		EnteteFormulaire("POST","ajouter_image_de_base.php?action=NewIdbNewDistribValidation");
		print("<H2>Distribution</H2>");
		EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
		print("<TR><TD><I>Nom Logiciel (exemples : Debian, RedHat, Windows 2000...)</I> </TD><TD>: <INPUT TYPE=TEXT NAME=nom_logiciel SIZE=50 VALUE=\"\"></TD></TR>\n");
		print("<TR><TD><I>Version (exemples : Woody, 9, SP4)</I> </TD><TD>: <INPUT TYPE=TEXT NAME=version SIZE=50 VALUE=\"\"></TD></TR>\n");
		print("<TR><TD><I>Icône</I> </TD><TD>: <I><FONT SIZE=-1 COLOR=GREEN>&lt;JeDDLaJ's home sur serveur Web&gt;/ICONES/</I></FONT><INPUT TYPE=TEXT NAME=icone SIZE=50 VALUE=\"\"></TD></TR>\n");
		print("<TR><TD><I>Nom OS</I> </TD><TD>: <SELECT NAME=nom_os>\n");
		foreach ($GLOBALS['oss'] as $os)
		{
		    print("<OPTION VALUE=\"$os\">$os</OPTION>\n");
		}
		print("</SELECT>\n");
		print("</TD></TR>\n");
		FinTable();
		print("<H2>Image de base</H2>");
		EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
		print("<TR><TD><I>Nom image de base</I> </TD><TD>: <INPUT TYPE=TEXT NAME=nom_idb SIZE=50 VALUE=\"\"></TD></TR>\n");
		print("<TR><TD><I>Répertoire</I> </TD><TD>: <FONT SIZE=-1 COLOR=GREEN><I>$RemboIDBDir</I></FONT><INPUT TYPE=TEXT NAME=repertoire SIZE=50 VALUE=\"\"></TD></TR>\n");

		print("<TR><TD><I>Spécificité</I> </TD><TD>: <SELECT NAME=specificite>\n");
		print("<OPTION VALUE=\"aucune\">aucune</OPTION>\n");
		print("<OPTION VALUE=\"nom_dns\">nom_dns</OPTION>\n");
		print("<OPTION VALUE=\"signature\">signature</OPTION>\n");
		print("</SELECT>\n");
		print("</TD></TR>\n");
		print("<TR><TD><I>Valeur Spécificité</I> </TD><TD>: <INPUT TYPE=TEXT NAME=valeur_specificite SIZE=50 VALUE=\"\"></TD></TR>\n");
		FinTable();
		print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
		FinFormulaire();
		break;
	case "NewIdbNewDistribValidation":
		# On recupere les variables
		$nom_logiciel = $_POST["nom_logiciel"];
		$icone = $_POST["icone"];
		if ($icone=="") {$icone = "defaulticon.jpg";};
		$version = $_POST["version"];
		empty($_POST['version']) ? $version_a_afficher="non spécifiée" : $version_a_afficher=$_POST['version'];
		$nom_os = $_POST["nom_os"];
		$nom_idb = $_POST["nom_idb"];
		$repertoire = $_POST["repertoire"];
		# On ajoute un slash final au chemin s'il n'y est pas déjà, sauf si répertoire est vide sinon 
		# on aurait $RemboIDBDir/ (donc deux slashes finaux...)
		if (substr($repertoire,-1) != "/" and $repertoire != "") {$repertoire .= "/";}

		$specificite = $_POST["specificite"];
		$valeur_specificite = $_POST["valeur_specificite"];
		# toutes les variables ont ete recuperees


		# On vérifie qu'on n'insère pas une distribution déjà existante ou une image de base déjà existante...
		# LA DISTRIBUTION
		$request = "SELECT COUNT(*) AS total FROM logiciels WHERE nom_logiciel=\"$nom_logiciel\" AND version=\"$version\" AND nom_os=\"$nom_os\"";
		$result = mysql_query($request);
		$line = mysql_fetch_array($result);
		$distribution_deja_existante = ($line["total"] != 0);
		mysql_free_result($result);
		# L'IMAGE DE BASE
		# On veut juste qu'il n'y en ait pas une autre de meme nom dans le meme repertoire.
		# Les autres cas, meme nom, autre repertoire, ou autre nom, meme repertoire ne sont pas
		# genants dans la mesure ou la nouvelle image est associee a une nouvelle distribution et 
		# ne peut a ce titre etre confondue avec une ancienne...
		$request2 = "SELECT COUNT(*) AS total FROM images_de_base WHERE nom_idb=\"$nom_idb\" AND repertoire=\"$repertoire\"";
		$result2 = mysql_query($request2);
		$line2 = mysql_fetch_array($result2);
		$idb_meme_nom_meme_rep_deja_existante = ($line2["total"] != 0);
		mysql_free_result($result2);
		if ($distribution_deja_existante)
		{
			print("<P><I><FONT COLOR = RED>ATTENTION DANGER : </FONT>La distribution <FONT COLOR=RED>$nom_logiciel</FONT>, version <FONT COLOR=RED>$version_a_afficher</FONT> existe déjà pour le système d'exploitation <FONT COLOR = RED>$nom_os</FONT>... Veuillez utiliser le bouton Back/Précédent de votre navigateur pour modifier votre entrée.</I></P>\n");
		}
		elseif ($idb_meme_nom_meme_rep_deja_existante)
		{
			if ($repertoire=='') {$phrase_rep="dans le même répertoire";} else {$phrase_rep="dans le répertoire <FONT COLOR=RED>$repertoire</FONT>";}
			print("<P><I><FONT COLOR = RED>ATTENTION DANGER : </FONT>Une image de base de nom <FONT COLOR=RED>$nom_idb</FONT>");
			print(" existe déjà $phrase_rep... Ceci est interdit par JeDDLaJ, ainsi que par tout système de fichiers ayant dépassé la version pré-alpha... Veuillez utiliser le bouton Back/Précédent de votre navigateur pour modifier votre entrée.</I></P>\n");
		}
		else # OK, on peut ajouter
		{
			# On insère dans la table logiciels
			$request = "INSERT INTO logiciels (nom_logiciel, icone, version, nom_os) VALUES (\"$nom_logiciel\", \"$icone\", \"$version\", \"$nom_os\")";
			$result = mysql_query($request);
			$id_logiciel = mysql_insert_id();
			# On pourrait aussi faire avec cette fonction interne MySQL qui marche même si id_logiciel est un bigint, ce qui n'est pas le 
			# cas de la fonction PHP mysql_insert_id()...
			# $id_logiciel = mysql_query("SELECT(LAST_INSERT_ID())");
			print("<P><I>La distribution <FONT COLOR=RED>$nom_logiciel</FONT>, version <FONT COLOR=RED>$version_a_afficher</FONT> a été ajoutée avec grand succès à la table des logiciels.</I></P>\n");

			# On insère dans la table images_de_base
			$request = "INSERT INTO images_de_base (id_os, nom_idb, repertoire, specificite, valeur_specificite) VALUES (\"$id_logiciel\", \"$nom_idb\", \"$repertoire\", \"$specificite\", \"$valeur_specificite\")";
			$result = mysql_query($request);

			print("<P><I>L'image de base <FONT COLOR=RED>$nom_idb</FONT> a été ajoutée like a charm à la table des images de base.</I></P>\n");
			print("<P><I>Ils sont donc désormais tous deux disponibles pour toutes vos opérations JeDDLaJiques à venir...<FONT SIZE=-1><FONT COLOR=RED> SOUS RÉSERVE QUE </FONT>, bien sûr, l'image de base $nom_idb ait été déposée <FONT COLOR=RED>effectivementi</FONT> sur le serveur REMBO, dans le répertoire $RemboIDBDir$repertoire</I></FONT></P>");
		}
}

print("<BR><HR><P><CENTER><A HREF=accueil.php TARGET=\"_top\">Retour</A></CENTER></P>\n");

DisconnectMySQL();
PiedPage();
?>

