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

# On recupere les variables
# Si on vient de choix_machines_multiples
if (isset( $_POST['nb_ordinateurs'])) { $nb_ordinateurs = $_POST['nb_ordinateurs']; }
if (isset( $_POST['checked'])) { $checked = $_POST['checked']; }
# Si on vient de choix_groupes_multiples
if (isset( $_POST['nb_groupes'])) { $nb_groupes = $_POST['nb_groupes']; }
if (isset( $_POST['deja_passe_dans_choix_groupes'])) { $deja_passe_dans_choix_groupes = $_POST['deja_passe_dans_choix_groupes']; }
# toutes les variables ont ete recuperees


include("UtilsHTML.php");
include("UtilsMySQL.php");
include("UtilsJeDDLaJ.php");

# Main()
entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : modification de moulon de machines");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<CENTER><H1>Domaines de modifications</H1></CENTER>\n");

# Recuperation des ordinateurs coches dans choix_machines_multiples
$nb_ordinateurs_concernes=0;
$liste_ordinateurs_concernes="";
# On fait la liste des ordinateurs concernés 
# et on resette leur verrouillage

# Cas ou l'on vient de choix_machines_multiples : on recupere dans ordinateurs_concernes 
# toutes les machines cochees
if (!isset( $_POST['deja_passe_dans_choix_groupes']))
{
	if (count($checked) > 0)
	{
		for($i=1;$i<=$nb_ordinateurs;$i++)
		{
			if (isset($checked[$i]))
			{
				$nb_ordinateurs_concernes++;
				$ordinateurs_concernes[$nb_ordinateurs_concernes] = $checked[$i];
				$liste_ordinateurs_concernes .= $ordinateurs_concernes[$nb_ordinateurs_concernes]." ";
				# On verrouille les ordinateurs sur lesquels on travaille
				verrouille_pour_mon_ip($ordinateurs_concernes[$nb_ordinateurs_concernes]);
			}
		}
	}
}
# Cas ou l'on vient de choix_groupes_multiples : on recupere dans ordinateurs_concernes 
# toutes les machines de tous les groupes coches
else
{
	if (count($checked) > 0)
	{
		$where = "0 ";
		for($i=1;$i<=$nb_groupes;$i++)
		{
			if (isset($checked[$i]))
			{
				$where .= "or nom_groupe=\"$checked[$i]\" ";
			}
		}
		$request = "SELECT DISTINCT nom_dns FROM ord_appartient_a_gpe WHERE $where";
		$result = mysql_query($request);
		while ($ligne = mysql_fetch_array($result))
		{
			$nb_ordinateurs_concernes++;
			$ordinateurs_concernes[$nb_ordinateurs_concernes] = $ligne['nom_dns'];
			$liste_ordinateurs_concernes .= $ordinateurs_concernes[$nb_ordinateurs_concernes]." ";
			# On verrouille les ordinateurs sur lesquels on travaille
			verrouille_pour_mon_ip($ordinateurs_concernes[$nb_ordinateurs_concernes]);
		}

	}
}

# On serialize le tableau pour le passer facile par POST :
$ordinateurs_concernes = urlencode(serialize($ordinateurs_concernes));
############ Infos générales

print("<H2>Général</H2>\n");
print("<P>Fixer à une valeur unique certaines des caractéristiques générales de machines sélectionées (affiliation Windows, OU, type d'extinction, données d'affichage, <S>etc.</S> et c'est tout pour l'instant) </P>\n");
EnteteFormulaire("POST","modifier_machines_nombreuses_1.php");
print("<INPUT TYPE=HIDDEN NAME=ordinateurs_concernes VALUE=\"$ordinateurs_concernes\">\n");
print("<P><INPUT TYPE=SUBMIT VALUE=\"On y va...\"></P>\n");
FinFormulaire();


############ Fin Infos Générales

############ Infos de partitionnement

print("<H2>Partitionnement</H2>\n");
print("<P>Modifier en bloc le partitionnement disque (premier disque uniquement) pour toutes les machines sélectionnées ?</P>\n");
EnteteFormulaire("POST","modifier_machines_nombreuses_2.php");
print("<INPUT TYPE=HIDDEN NAME=ordinateurs_concernes VALUE=\"$ordinateurs_concernes\">\n");
print("<P><INPUT TYPE=SUBMIT VALUE=\"C'est parti...\"></P>\n");
FinFormulaire();


############ FIN Infos de partitionnement

############ Infos groupe


#print("<BR><H2>Groupes</H2>\n");



print("<BR><BR><HR><P><CENTER><A HREF=accueil.php>Retour</A></CENTER></P>\n");

DisconnectMySQL();


PiedPage();
//FIN Main()

?>
