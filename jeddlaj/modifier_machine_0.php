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


# On recupere les variables
if (isset($_GET["nom_dns"])){$nom_dns = $_GET["nom_dns"];}
if (isset($_POST["new_nom_dns"])){$new_nom_dns = $_POST["new_nom_dns"];}
if (preg_match("/ /",$new_nom_dns))
{
	explication_erreur_saisie_formulaire("Nouveau nom DNS", "Tu le sais pourtant, qu'il ne faut pas d'espaces dans le nom DNS... Qu'est-ce qu'on va faire de toi ?...<BR>");
}
if (isset($_POST["modif_base"])){$modif_base = $_POST["modif_base"];}
# toutes les variables ont ete recuperees



# Main()
entete("G�rard Milhaud & Fr�d�ric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Machine $nom_dns, partitionnement");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);
if (! isset($new_nom_dns))
{
	print("<CENTER><H1>$nom_dns : on se pr�pare � changer de nom...</H1></CENTER>\n");
}
else
{
	print("<CENTER><H1>$nom_dns est mort, vive $new_nom_dns !!!...</H1></CENTER>\n");
}

if (est_verrouille_pour_mon_ip($nom_dns))
{
	# On resette la duree de verrouillage 
	deverrouille_pour_mon_ip($nom_dns);
	verrouille_pour_mon_ip($nom_dns);
}
else 
{
	print("La s�lection a expir�. Retournez � l'<A HREF=accueil.php>accueil</A> pour relancer une proc�dure de modification machine.<BR>\n");
	$expirade = 1;
}



if (!isset($expirade))
{
	# On a deja propose un nouveau nom, 
	# il est temps de modifier effectivement la base
	if (isset($modif_base))
	{	
		if (est_verrouille_pour_mon_ip($nom_dns))
		{
			# On commence par de menues verifications
			# Si le nom existe deja, alerte !!!!
			$request = "SELECT nom_dns FROM ordinateurs where nom_dns=\"$new_nom_dns\"";
			$result=mysql_query($request);
			if ( mysql_num_rows($result) > 0 ) # Ce nom existe deja !!! On stoppe...
			{
				explication_erreur_saisie_formulaire("Nouveau nom DNS","La machine $new_nom_dns est d�j� dans la base JeDDLaJ !!! Il faut mieux vous concentrer, puis tenter un nouvel essai...");
			}

			# Le nom est correct : on lance la modif.


			# Les tables o� nom_dns appara�t
			$tables_concernees = array("composant_est_installe_sur", "depannage", "idb_est_installe_sur", "package_est_installe_sur", "ord_appartient_a_gpe", "ordinateurs", "ordinateurs_en_consultation", "partitions", "stockages_de_masse");
			# On update les tables concernees
			foreach($tables_concernees as $table)
			{
			        $request = "UPDATE $table SET nom_dns=\"$new_nom_dns\" WHERE nom_dns=\"$nom_dns\"";
			        mysql_query($request);
				if (mysql_affected_rows() > 0)
				{
				        printf ("<P><FONT SIZE=-1><I>Table <TT>$table</TT> : Renommage des %d enregistrements relatifs � l'ancien $nom_dns. </I></P></FONT>\n", mysql_affected_rows());
				}
			}
			# Il y a aussi les tables postinstall_scripts et predeinstall_scripts qu'il faut UPDATER pour les scripts qui s'appliquaient a la machine renomm�e
			$tables_concernees = array("postinstall_scripts", "predeinstall_scripts");
			foreach($tables_concernees as $table)
			{
				$request = "UPDATE $table SET valeur_application=\"$new_nom_dns\" WHERE applicable_a=\"nom_dns\" AND valeur_application=\"$nom_dns\"";
				mysql_query($request);
				if (mysql_affected_rows() > 0)
				{
			        	printf ("<P><FONT SIZE=-1><I>Table <TT>$table</TT> : Renommage des %d enregistrements correspondant � des scripts s'appliquant � l'ancien $nom_dns. </I></FONT></P>\n", mysql_affected_rows());
				}
			}
			print ("<P><FONT SIZE=+1>Si d'aventure vous souhaitiez changer aussi le <FONT COLOR=RED><B>nom netbios</B></FONT> de $new_nom_dns, <A HREF=modifier_machine_1.php?nom_dns=$new_nom_dns>ce petit raccourci <I>tioun�</I> au poil rien que pour vous serait adapt�...</A></FONT></P>\n");
			# Ici le retour se fait sur le nouveau nom DNS, que l'on vient de definir...
			print("<BR><HR><P><CENTER><A HREF=modifier_machine.php?nom_dns=$new_nom_dns>Retour</A></CENTER>\n");
		}
		else
		{
			print("La s�lection a expir�. Retournez � l'<A HREF=accueil.php>accueil</A> pour relancer une proc�dure de modification machine.<BR>\n");
		}
	}
	# On arrive a peine de modifier_machine : on va donc demander le nouveau nom
	else
	{
		EnteteFormulaire("POST","modifier_machine_0.php?nom_dns=$nom_dns");
		print("<P>Nouveau nom DNS (anciennement $nom_dns) : <INPUT TYPE=TEXT NAME=new_nom_dns></P>");
		echo("<INPUT TYPE=HIDDEN NAME=modif_base VALUE=1>\n");
		print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
		FinFormulaire();
		# Ici le retour se fait sur le nom DNS non encore change...
		print("<BR><HR><P><CENTER><A HREF=modifier_machine.php?nom_dns=$nom_dns>Retour</A></CENTER>\n");
	}
}

DisconnectMySQL();


PiedPage();
//FIN Main()

?>
