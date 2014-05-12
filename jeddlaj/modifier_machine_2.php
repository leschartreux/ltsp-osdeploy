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
if (isset($_GET["nom_dns"])){$nom_dns = $_GET["nom_dns"];}
if (isset($_GET["num_disque"])){$num_disque = $_GET["num_disque"];}
if (isset($_POST["num_partition"])){$num_partition = $_POST["num_partition"];}
if (isset( $_POST["nb_part"])){$nb_part = $_POST["nb_part"];}
if (isset($_POST["modif_base"])){$modif_base = $_POST["modif_base"];}
if (isset($_POST["num_partition"])){$num_partition = $_POST["num_partition"];}
if (isset($_POST["type_partition"])){$type_partition = $_POST["type_partition"];}
if (isset($_POST["taille_partition"])){$taille_partition = $_POST["taille_partition"];}
if (isset($_POST["nom_partition"])){$nom_partition = $_POST["nom_partition"];}
if (isset($_POST["systeme"])){$systeme = $_POST["systeme"];}
# toutes les variables ont ete recuperees


include("UtilsHTML.php");
include("UtilsMySQL.php");
include("UtilsJeDDLaJ.php");


# Main()
entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Machine $nom_dns, partitionnement");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);
print("<CENTER><H1>$nom_dns : partitionnement disque $num_disque</H1></CENTER>\n");


if (est_verrouille_pour_mon_ip($nom_dns))
{
	# On resette la duree de verrouillage 
	deverrouille_pour_mon_ip($nom_dns);
	verrouille_pour_mon_ip($nom_dns);
}
else 
{
	print("La sélection a expiré. Retournez à l'<A HREF=accueil.php>accueil</A> pour relancer une procédure de modification machine.<BR>\n");
	$expirade = 1;
}



if (!isset($expirade))
{
	# On a deja fait des modifs dans le formulaire des partitions, 
	# il est temps de modifier effectivement la base
	if (isset($modif_base))
	{	
		if (est_verrouille_pour_mon_ip($nom_dns))
		{
			# On commence par de menues verifications
			# Si la partition est de type linux, son nom doit commencer par /
			for($i=1;$i<=$nb_part;$i++)
			{
				if (preg_match("/^EXT[23]/",$type_partition[$i]) and ! preg_match("/^\/.*/",$nom_partition[$i])) {explication_erreur_saisie_formulaire("nom_partition","le nom d'une partition <I>linux</I> DOIT commencer par \"/\" !!!");}
			}

			# On detruit les anciennes partitions
			mysql_query("DELETE from partitions where nom_dns=\"$nom_dns\" AND num_disque=\"$num_disque\"");
			printf ("<P><I>Ancienne table des partitions détruite. Nombre d'enregistrements détruits : %d. </I></P>\n", mysql_affected_rows());
			# On detruit aussi les softs installes sur ce disque, puisqu'on
			# ne sait plus s'ils seront installés sur les memes partitions.
			# De plus meme l'OS va peut-etre changer...
			mysql_query("DELETE from idb_est_installe_sur where nom_dns=\"$nom_dns\" AND num_disque=\"$num_disque\"");
			mysql_query("DELETE from package_est_installe_sur where nom_dns=\"$nom_dns\" AND num_disque=\"$num_disque\"");
			printf ("<P><I>Configuration logicielle disque $num_disque detruite. Nombre d'enregistrements détruits : %d. </I></P>\n", mysql_affected_rows());
			# Puis on insere les nouvelles partitions
			# On commence par determiner la device linux de num_disque
			$request=" SELECT linux_device FROM stockages_de_masse WHERE nom_dns=\"$nom_dns\" and num_disque=\"$num_disque\" and type=\"disque dur\"";
			$result=mysql_query($request);
			$ligne=mysql_fetch_array($result);
			$dd_linux_device=$ligne["linux_device"];
			mysql_free_result($result);
			# Puis on insere en vrai
			$inserted = 0;
			if ($nb_part > 4) # Il y a une partition étendue
			{
				$taille_partition[4] = 0;
				for($i=5;$i<=$nb_part;$i++)
				{
					$taille_partition[4] += $taille_partition[$i];
				}
			}
			for($i=1;$i<=$nb_part;$i++)
			{
				$partition_linux_device = $dd_linux_device.$i;
				# Si SWAP, on met en force l'attribut système à "non"
				if ($type_partition[$i] == "LINUX-SWAP") {$systeme[$i] = "non";}
				# Si système pas coché, on le met logiquement à "non"
				if (!isset($systeme[$i])) {$systeme[$i] = "non";}
				mysql_query("INSERT into partitions (nom_dns, num_disque, num_partition, taille_partition, type_partition, nom_partition, linux_device, systeme) VALUES (\"$nom_dns\", \"$num_disque\", \"$i\", \"$taille_partition[$i]\", \"$type_partition[$i]\", \"$nom_partition[$i]\", \"$partition_linux_device\", \"$systeme[$i]\")");
				$inserted += mysql_affected_rows();
			}
			printf ("<P><I>Nouvelle table des partitions insérée dans la base. Nombre d'enregistrements ajoutés : %d. </I></P>\n", $inserted);
			# On positionne a oui le champ dd_a_partitionner du disque dur 
			# num_disque de nom_dns dans la table stockages_de_masse
			mysql_query("UPDATE stockages_de_masse set dd_a_partitionner=\"oui\" where nom_dns=\"$nom_dns\" and type=\"disque dur\" and num_disque=\"$num_disque\"");
			# Notons qu'on ne met pas l'ordinateur en état "modifié" pour éviter le réinstall et l'écrasement de tout
			# au prochain reboot. On ne passe en état modifié QUE SI on modifie ensuite la configuration logicielle.
			print ("<P><FONT COLOR=RED><I>ATTENTION : Le disque $num_disque de la machine $nom_dns a été repartitionné. Il faut donc <A HREF=\"configuration_logicielle_0.php\">redéfinir la configuration logicielle sur ce disque</A> sous peine que $nom_dns ne boote plus.");
		
		}
		else
		{
			print("La sélection a expiré. Retournez à l'<A HREF=accueil.php>accueil</A> pour relancer une procédure de modification machine.<BR>\n");
		}
	}
	# On arrive a peine de modifier_machine : on va donc demander 
	# combien de partitions on souhaite
	# Formulaire de demande du nombre de partitions
	elseif (!isset($nb_part))
	{
		EnteteFormulaire("POST","modifier_machine_2.php?nom_dns=$nom_dns&num_disque=$num_disque");
		print("<P>Nombre total de partitions voulues pour le disque $num_disque : <INPUT TYPE=TEXT NAME=nb_part></P>");
		print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
		FinFormulaire();
	}
	# On a donne le nombre de partitions : on va donc presenter le formulaire
	# permettant de les définir complètement
	else
	{
		# On commence par donner de l'info sur la taille du disque et 
		# sur les préconisation de cache
		$request = "SELECT capacite FROM stockages_de_masse where nom_dns=\"$nom_dns\" AND type=\"disque dur\" AND num_disque=\"$num_disque\"";
		$result=mysql_query($request);
		$ligne = mysql_fetch_array($result);
		$cap_mega = $ligne["capacite"]*1024;
		print("<I>Vous travaillez sur un disque dur de <FONT SIZE = +1 COLOR=GREEN>".$ligne["capacite"]."</FONT> Go, soit <FONT SIZE = +1 COLOR=GREEN> $cap_mega Mo</FONT>.<BR>\n");
		mysql_free_result($result);
		print("<B><FONT COLOR=RED>N'oubliez pas de laisser de la place pour le cache REMBO...</FONT></B><BR>\n");
		print("<FONT >(un exemple pour information : un système Windows XP qui prend 3,2 Go sur le disque occupe 788 Mo dans le cache avec un taux de compression du serveur REMBO (réglable dans les options du serveur) de 5. De plus n'oubliez pas que c'est sur le cache que JeDDLaJ travaille pour créer ses \"photos\" de système lorsque l'on crée des packages sur la machine. Prévoyez donc un peu large...)</FONT><BR><BR>\n");
		print("Une partition doit être étiquetée <TT><FONT COLOR=RED>système</FONT></TT> lorsque l'on désire qu'elle soit concernée lors des réinstallations/synchronisations de la machine. Typiquement, les partitions où sont installés l'OS et les applications devraient être étiquetées <TT>système</TT> alors que les partitions destinées à recevoir des données utilisateurs évoluant dans le temps (donc non stockées dans l'image REMBO) et que l'on désire conserver (par exemple une partition de partage pour un groupe d'utilisateurs, un espace de stockage réservé pour le déchargement de données d'un logiciel, etc.) NE DOIVENT PAS être étiquetées <TT>système</TT> sous peine de perte de ces données lors de la réinstallation/synchronisation. Enfin, Les partitions de swap n'ayant aucun intérêt à être considérées par les reinstallations/synchrosations, JeDDLaJ positionnera automatiquement l'attribut <TT>système</TT> à <TT>non</TT>.</I><BR>\n");
		print("<BR><HR><BR>\n");
		EnteteFormulaire("POST","modifier_machine_2.php?nom_dns=$nom_dns&num_disque=$num_disque");
		EnteteTable("BORDER=1 CELLPADDING=2 CELLSPACING=1");
		print("<TR><TH>num_disque</TH><TH>num_partition</TH><TH>taille_partition (Mo)</TH><TH>type_partition</TH><TH>nom_partition</TH><TH>Système ?</TH></TR>");
		echo("<INPUT TYPE=HIDDEN NAME=modif_base VALUE=1>\n");
		# S'il y a une partition étendue, on augmente de 1 le nombre de
		# partitions, car il s'agit du nombre de partitions utilisables
		# pour les données : la partition étendue ne doit donc pas
		# compter.
		if ($nb_part>4){$nb_part++;}
		echo("<INPUT TYPE=HIDDEN NAME=nb_part VALUE=$nb_part>\n");
		for($i=1;$i<=$nb_part;$i++)
		{
			print("<TR>\n");
			print("<TD>$num_disque</TD>\n");
			print("<TD>$i</TD>\n");
			print("<INPUT TYPE=HIDDEN NAME=\"num_partition[$i]\" VALUE=\"$i\">\n");
			if ($i==4 and $nb_part>4)
			{
				print("<TD>AUTO</TD>\n"); # La taille sera calculée en fonction des tailles des partitions logiques contenues
				print("<TD>ETENDUE</TD>\n"); # Le type est fixé à "ETENDUE"
				print("<INPUT TYPE=HIDDEN NAME=\"type_partition[$i]\" VALUE=\"EXT\">\n");
				print("<TD><INPUT TYPE=TEXT NAME=\"nom_partition[$i]\"></TD>\n");
				print("<TD ALIGN=CENTER><INPUT DISABLED TYPE=CHECKBOX NAME=\"systeme[$i]\"></TD></TR>\n");
			}
			else
			{
				print("<TD><INPUT TYPE=TEXT NAME=\"taille_partition[$i]\"></TD>\n");
				print("<TD>\n<SELECT NAME=\"type_partition[$i]\">\n");
				print("<OPTION> NTFS\n");
				print("<OPTION> EXT2\n");
				print("<OPTION> EXT3\n");
				print("<OPTION> LINUX-SWAP\n");
				print("<OPTION> FAT32\n");
				print("<OPTION> UNSUPPORTED\n</TD>\n");
				print("</SELECT>");
				print("<TD><INPUT TYPE=TEXT NAME=\"nom_partition[$i]\"></TD>\n");
				print("<TD ALIGN=CENTER><INPUT TYPE=CHECKBOX NAME=\"systeme[$i]\" VALUE=\"oui\"></TD></TR>\n");
			}
			print("</TR>\n");
		}
		FinTable();
		print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
		FinFormulaire();
	}
}

print("<BR><HR><P><CENTER><A HREF=modifier_machine.php?nom_dns=$nom_dns>Retour</A></CENTER>\n");
DisconnectMySQL();


PiedPage();
//FIN Main()

?>
