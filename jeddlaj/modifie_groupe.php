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



## TO DO
## 


include("UtilsHTML.php");
include("UtilsMySQL.php");


# Main()
entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Modification du groupe $nom_groupe");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS[host], $GLOBALS[user], $GLOBALS[pwd]);
SelectDb($GLOBALS[db]);
print("<CENTER><H1>Modification du groupe $nom_groupe</H1></CENTER>\n");

# On a déjà fait des modifs sur le groupe : on va les valider dans la base
if (isset($deja_passe_dans_modifie_groupe))
{
	# Le nombre de machines choisies est non nul
	if (count($checked) > 0)
	{
		$nb_ordinateurs_concernes=0;
		$liste_ordinateurs_concernes="";
		for($i=1;$i<=$nb_ordinateurs;$i++)
		{
			if (isset($checked[$i]))
			{
				$nb_ordinateurs_concernes++;
				$ordinateurs_concernes[$nb_ordinateurs_concernes] = $checked[$i];
				$liste_ordinateurs_concernes .= $ordinateurs_concernes[$nb_ordinateurs_concernes]." ";
			}
		}
		mysql_query("UPDATE groupes set description_groupe=\"$description_groupe\", photo=\"$photo\" WHERE nom_groupe=\"$nom_groupe\"");
		print ("<P><I>Modifications du groupe $nom_groupe insérées dans la base. </I></P>\n");
		mysql_query("DELETE FROM ord_appartient_a_gpe WHERE nom_groupe=\"$nom_groupe\"");
		printf ("<P><I>Destruction des anciens enregistrements dans la table ord_appartient_a_groupe pour le groupe $nom_groupe et les ordinateurs $liste_ordinateurs_concernes. Nombre d'enregistrements détruits : %d. </I></P>\n", mysql_affected_rows());
		$inserted = 0;
		for($i=1;$i<=$nb_ordinateurs_concernes;$i++)
		{
			mysql_query("INSERT INTO ord_appartient_a_gpe (nom_dns,nom_groupe) VALUES (\"$ordinateurs_concernes[$i]\", \"$nom_groupe\")");
			$inserted++;
		}
		printf ("<P><I>Insertion des nouveaux enregistrements dans la table ord_appartient_a_groupe pour le groupe $nom_groupe et les ordinateurs $liste_ordinateurs_concernes. Nombre d'enregistrements ajoutés : %d. </I></P>\n", $inserted);
		print("<CENTER><A HREF=accueil.php>Retour</A></CENTER>\n");
	} # end if (count($checked) > 0)
	else
	{
		print ("<P><I><FONT COLOR=RED>ATTENTION : Vous n'avez choisi aucune machine</FONT>. Utilisez le bouton <TT>BACK</TT> de votre navigateur pour faire une sélection valide.</I></P>");
	}
}# end if (isset($deja_passe_dans_modifie_groupe))
else
{
	
	# On commence par récupérer les ordinateurs du groupe
	$request="SELECT nom_dns FROM ord_appartient_a_gpe WHERE nom_groupe=\"$nom_groupe\"";
	$result=mysql_query($request);
	$nb_ordinateurs_du_groupe=0;
	while ($ligne=mysql_fetch_array($result))
	{
		$nb_ordinateurs_du_groupe++;
		$ordinateurs_du_groupe[$nb_ordinateurs_du_groupe] = $ligne[nom_dns];
	}
	mysql_free_result($result);

	$request="SELECT nom_dns FROM ordinateurs";
	$result=mysql_query($request);
	EnteteFormulaire("POST","modifie_groupe.php");
	print("<INPUT TYPE=HIDDEN NAME=\"deja_passe_dans_modifie_groupe\" VALUE=1>\n");
	print("<INPUT TYPE=HIDDEN NAME=\"nom_groupe\" VALUE=\"$nom_groupe\">\n");
	EnteteTable("BORDER=2 CELLPADDING=2 CELLSPACING=1");
	$nb_ordinateurs = 0;
	while ($ligne = mysql_fetch_array($result)) {
		$nb_ordinateurs++;
		print("<TR>\n");
		$present = 0;
		$i=1;
		while(!$present and $i<=$nb_ordinateurs_du_groupe)
		{
			$present = ($ordinateurs_du_groupe[$i] ==  $ligne[nom_dns]);
			$i++;
		}
		$present ? $checked = " CHECKED " : $checked = "";
		print("<TD>\n$ligne[nom_dns]\n</TD>\n<TD>\n <INPUT TYPE=CHECKBOX NAME=\"checked[$nb_ordinateurs]\" VALUE=\"$ligne[nom_dns]\"".$checked.">\n </TD>\n");
		print("</TR>\n");
	}
	print("<INPUT TYPE=HIDDEN NAME=\"nb_ordinateurs\" VALUE=$nb_ordinateurs>\n");
	mysql_free_result($result);
	FinTable();
	$request="SELECT description_groupe, photo FROM groupes where nom_groupe=\"$nom_groupe\"";
	$result=mysql_query($request);
	$ligne = mysql_fetch_array($result);
	EnteteTable("BORDER=0 CELLPADDING=2 CELLSPACING=5");
	print("<TR><TD>Description </TD><TD>: <INPUT TYPE=TEXT NAME=description_groupe SIZE=50 VALUE=\"$ligne[description_groupe]\"></TD></TR>");
	print("<TR><TD>Photo </TD><TD>: <INPUT TYPE=TEXT NAME=photo SIZE=50 VALUE=\"$ligne[photo]\"></TD></TR>");
	mysql_free_result($result);
	FinTable();
	print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
	FinFormulaire();
}
DisconnectMySQL();
PiedPage();
//FIN Main()

?>
