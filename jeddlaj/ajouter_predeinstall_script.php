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

entete("G�rard Milhaud & Fr�d�ric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Ajout predeinstall script ($action)");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);
print("<CENTER><H1>Ajout de predeinstall scripts</H1></CENTER>\n");

switch ($action)
{
	case "AjoutPreDeinstScript":
		print("<H2>Predeinstall script � ajouter</H2>");
		print("<FORM NAME=\"edit_predeinst\" METHOD=\"POST\" ACTION=\"ajouter_predeinstall_script.php?action=Validation\">");
		EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
		print("<TR><TD><I>Nom predeinstall script</I> </TD><TD>: <INPUT TYPE=TEXT NAME=nom_script SIZE=50 VALUE=\"\"></TD></TR>\n");
		print("<TR><TD><I>R�pertoire</I> </TD><TD>: <FONT SIZE=-1 COLOR=GREEN><I>$RemboPreDeinstScriptsDir</I></FONT><INPUT TYPE=TEXT NAME=repertoire SIZE=50 VALUE=\"\"></TD></TR>\n");
		print("<TR>\n<TD><I>Applicable � : </I></TD></TR>\n");
		print("<TR><TD><I>&nbsp;&nbsp;&nbsp; <IMG ALIGN=CENTER SRC=ICONES/purpleball.png> un groupe  </I></TD>\n");
		print("<TD>: <SELECT name=\"nom_groupe\" onChange=\"javascript:document.edit_predeinst.nom_dns.selectedIndex=0\">\n");
		print("<OPTION value=\"\"></OPTION>\n");
		$request = "SELECT nom_groupe FROM groupes";
		$result = mysql_query($request);
		for ($i=0;$i<mysql_num_rows($result);$i++) {
		  $line = mysql_fetch_array($result);
		  print("<OPTION value=\"".$line["nom_groupe"]."\">".$line["nom_groupe"]."</OPTION>\n");
		}
		mysql_free_result($result);
		print("</SELECT></TD></TR>\n");
		print("<TR><I><TD>&nbsp;&nbsp;&nbsp; <IMG SRC=ICONES/purpleball.png> <FONT COLOR=RED>OU</FONT> � un ordinateur sp�cifique </I></TD>\n");
		print("<TD>: <SELECT name=\"nom_dns\" onChange=\"javascript:document.edit_predeinst.nom_groupe.selectedIndex=0\">\n");
		print("<OPTION value=\"\"></OPTION>\n");
		$request = "SELECT nom_dns FROM ordinateurs";
		$result = mysql_query($request);
		for ($i=0;$i<mysql_num_rows($result);$i++) {
		  $line = mysql_fetch_array($result);
		  print("<OPTION value=\"".$line["nom_dns"]."\">".$line["nom_dns"]."</OPTION>\n");
		}
		FinTable();
		print("<H2>Logiciels associ�s � ce predeinstall script</H2>");
		$request="SELECT * FROM logiciels ORDER BY nom_logiciel,version";
		$result=mysql_query($request);
		$nb_logiciels = mysql_num_rows($result);
		EnteteTable("BORDER=2 CELLPADDING=2 CELLSPACING=1");
		$i = 1;
		while ($ligne = mysql_fetch_array($result))
		{
			print("<TR>\n");
			print("<TD>\n<IMG ALIGN=CENTER  WIDTH=\"$largeur_image_logiciel_et_package\" HEIGHT=\"$hauteur_image_logiciel_et_package\" SRC=\"ICONES/$ligne[icone]\">\n</TD><TD>\n<TT><FONT COLOR=RED>$ligne[nom_logiciel]</FONT></TT>, version <TT><FONT COLOR=GREEN>$ligne[version]</FONT></TT>, (<TT><FONT COLOR=RED>$ligne[nom_os]</FONT></TT>)\n</TD>\n<TD>\n <INPUT TYPE=CHECKBOX  NAME=\"checked[".$i++."]\" VALUE=\"$ligne[id_logiciel]\">\n </TD>\n");
			print("</TR>\n");
		}
		mysql_free_result($result);
		FinTable();
		print("<INPUT TYPE=HIDDEN NAME=nb_logiciels VALUE=\"$nb_logiciels\">\n");
		print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
		FinFormulaire();
		break;
	case "Validation":
		# On recupere les variables
		$nom_script = $_POST["nom_script"];
		$repertoire = $_POST["repertoire"];
		$checked = $_POST["checked"];
		$nb_logiciels = $_POST["nb_logiciels"];
		# On ajoute un slash final au chemin s'il n'y est pas d�j�, sauf si r�pertoire est vide 
		# sinon on aurait $RemboPredeInstScriptsDir/ (donc deux slashes finaux...)
		if (substr($repertoire,-1) != "/" and $repertoire != "") {$repertoire .= "/";}
		$nom_groupe = $_POST["nom_groupe"];
		$nom_dns = $_POST["nom_dns"];
		# toutes les variables ont ete recuperees

		# On �num�re les cas pour lesquels on de doit pas ajouter

		# Le nombre de logiciels choisis est nul
		if (count($checked) == 0)
		{
			print ("<P><I><FONT COLOR=RED>ATTENTION : Vous n'avez choisi aucun logiciel</FONT>. Utilisez le bouton <TT>BACK</TT> de votre navigateur pour faire une s�lection valide.</I></P>");
			break;
		}
		# Le nom du predeinstall script n'est pas pr�cis�
		if ($nom_script == "")
		{
			print("<P><I><FONT COLOR = RED>ATTENTION DANGER : </FONT>Vous n'avez pas donn� de nom au predeinstall script que vous souhaitez ajouter... Veuillez utiliser le bouton Back/Pr�c�dent de votre navigateur pour modifier votre entr�e.</I></P>\n");
			break;
		}
		# Pas de valeur d'application... : on interdit l'ajout
		if ($nom_groupe == "" and $nom_dns == "")
		{
			print("<P><I><FONT COLOR = RED>ATTENTION DANGER : </FONT>Vous n'avez pas sp�cifi� � quoi s'applique ce predeinstall script... Veuillez utiliser le bouton Back/Pr�c�dent de votre navigateur pour modifier votre entr�e.</I></P>\n");
			break;
		}

		# Reste des cas n�cessitant des requ�tes
		# On v�rifie qu'on n'ins�re pas un predeinstall script d�j� existant i.e. de m�me nom dans le m�me r�pertoire...
		$request = "SELECT COUNT(*) AS total FROM predeinstall_scripts WHERE nom_script=\"$nom_script\" AND repertoire=\"$repertoire\"";
		$result = mysql_query($request);
		$line = mysql_fetch_array($result);
		$predeinstscript_meme_rep_deja_existant = ($line["total"] != 0);
		mysql_free_result($result);
		if ($predeinstscript_meme_rep_deja_existant)
		{
			print("<P><I><FONT COLOR = RED>ATTENTION DANGER : </FONT>Un predeinstall script de nom <FONT COLOR=RED>$nom_script</FONT> existe d�j� dans le r�pertoire $RemboPreDeinstScriptsDir<FONT COLOR=RED>$repertoire</FONT>... Veuillez utiliser le bouton Back/Pr�c�dent de votre navigateur pour modifier votre entr�e.</I></P>\n");
			break;
		}
 		# OK, on peut ajouter
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
		# On ins�re serein dans la table predeinstall_scripts
		# 1. On d�termine si c'est groupe ou nom_dns qui a �t� choisi
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
		# 2. On ins�re
		$request = "INSERT INTO predeinstall_scripts (nom_script, repertoire, applicable_a, valeur_application) VALUES (\"$nom_script\", \"$repertoire\", \"$applicable_a\", \"$valeur_application\")";
		$result = mysql_query($request);

		print("<P><I>Le predeinstall script <FONT COLOR=RED>$nom_script</FONT> a �t� ajout� avec grand brio � la table des predeinstall scripts.</I></P>\n");
		print("<P><I>Il est donc d�sormais disponible pour toutes vos op�rations JeDDLaJiques � venir...<FONT SIZE=-1><FONT COLOR=RED> SOUS R�SERVE QUE </FONT>, bien s�r, le predeinstall script $nom_script ait �t� d�pos� <FONT COLOR=RED>effectivement</FONT> sur le serveur REMBO, dans le r�pertoire $RemboPreDeinstScriptsDir$repertoire</I></FONT></P>");
		$id_script = mysql_insert_id();
		# On ins�re serein dans la table pdis_est_associe_a
		# On initialise la clause WHERE
		$clause_where=" WHERE 1=0";
		for($i=1;$i<=$nb_logiciels_concernes;$i++)
		{
			mysql_query("INSERT INTO pdis_est_associe_a (id_script, id_logiciel) VALUES(\"$id_script\", \"$id_logiciels_concernes[$i]\")");
			$clause_where .= " OR id_logiciel=\"$id_logiciels_concernes[$i]\"";
		}
		# Pour l'affichage on va r�cup�rer les noms des logiciels auxquels 
		# on va associer un predeinstall script
	#	Debug("clause_where");
		$request = "SELECT id_logiciel, nom_logiciel FROM logiciels".$clause_where;
		$result = mysql_query($request);
		$logiciels_associes = "<UL>\n";
		while ($ligne = mysql_fetch_array($result))
		{
			$logiciels_associes .= "<LI>$ligne[nom_logiciel] (<FONT SIZE=-1>id_logiciel = $ligne[id_logiciel])</FONT></LI>\n";
		}
		$logiciels_associes .= "</UL>\n";
		mysql_free_result($result);
		print("<I><P>Le script de predeinstall $nom_script a �t� associ� dans la base aux logiciels :</P>".$logiciels_associes."</I>");
}

print("<BR><HR><P><CENTER><A HREF=accueil.php TARGET=\"_top\">Retour</A></CENTER></P>\n");

DisconnectMySQL();
PiedPage();
?>

