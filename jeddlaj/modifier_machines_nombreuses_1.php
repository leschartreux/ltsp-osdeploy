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
if (isset($_POST["modif_base"])) {$modif_base = $_POST["modif_base"];}
if (isset( $_POST['nb_ordinateurs'])) { $nb_ordinateurs = $_POST['nb_ordinateurs']; }
if (isset( $_POST['ordinateurs_concernes'])) { $ordinateurs_concernes = unserialize(urldecode($_POST['ordinateurs_concernes'])); }
# toutes les variables ont ete recuperees

include("UtilsHTML.php");
include("UtilsMySQL.php");
include("UtilsJeDDLaJ.php");


# Main()
entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : modifications multi-machines, informations générales");
print("<CENTER><H1>Informations générales</H1></CENTER>\n");

include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

# On resette le verrouillage
foreach ($ordinateurs_concernes as $nom_dns)
{
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
		# On sort du foreach...
		break 1;
	}
}

if (!isset($expirade))
{
	# Cas ou on arrive a peine de modifier_machines_nombreuses
	if (!isset($modif_base))
	{
		# Formulaire
		
		print("<FORM METHOD=POST ACTION=\"modifier_machines_nombreuses_1.php\">\n");
		print("<INPUT TYPE=HIDDEN NAME=modif_base VALUE=1>\n");
		# On serialize le tableau pour le passer facile par POST :
		$ordinateurs_concernes = urlencode(serialize($ordinateurs_concernes));
		print("<INPUT TYPE=HIDDEN NAME=ordinateurs_concernes VALUE=\"$ordinateurs_concernes\">\n");

		print("<P><FONT COLOR=RED SIZE=-1><I><B>IMPORTANT</B> : Laisser un champ non renseigné = pas de changement ; renseigner un champ = lui donner cette valeur pour toutes les machines sélectionnées.</I></FONT></P>");
		print("<H2>Main stuff</H2>");
		print("<P>RAM : <INPUT TYPE=TEXT NAME=ram SIZE=30></P>");
		print("<P>Affiliation Windows : <SELECT NAME=affiliation_windows>\n");
		print("<OPTION SELECTED VALUE=\"PasDeModif\">Je ne veux pas modifier ce champ</OPTION> \n <OPTION> workgroup \n <OPTION> domain \n <OPTION> sambadomain ");
		print("</SELECT></P>");
		print("<P>Nom Affiliation : <INPUT TYPE=TEXT NAME=nom_affiliation SIZE=30 VALUE=''></P>");
		print("<P>OU (Organisational Unit) : <INPUT TYPE=TEXT NAME=ou SIZE=30 VALUE=''></P>");
		print("<P>Type d'extinction : <SELECT NAME=poweroff>\n");
		print("<OPTION SELECTED VALUE=\"PasDeModif\">Je ne veux pas modifier ce champ</OPTION> \n <OPTION> freedos \n <OPTION> native ");
		print("</SELECT></P>");
		print("<H2>Réglages affichage</H2>");
		print("<P><FONT COLOR=RED SIZE=-1>Les informations liees au moniteur declarees ci-dessous viennent se substituer a celle contenues dans l'image de base. Inutile donc de les preciser si celles de l'image conviennent.</FONT></P>\n");
		print("<P>Résolution moniteur : <SELECT NAME=resolution>\n");
		print("<OPTION SELECTED VALUE=\"PasDeModif\">Je ne veux pas modifier ce champ</OPTION>\n");
		print("<OPTION VALUE=\"0x0\">non definie i.e. idem image de base</OPTION>\n");
		foreach ($GLOBALS['resolutions'] as $res)
		{
			print("<OPTION> $res \n");
		}
		print("</SELECT></P>");
		print("<P>Fréquence horizontale moniteur (hfreq) en kHz : <INPUT TYPE=TEXT NAME=hfreq SIZE=30></P>");
		print("<P>Fréquence verticale moniteur (vfreq) en Hz : <INPUT TYPE=TEXT NAME=vfreq SIZE=30></P>");
		print("<P>\"Color Depth\" moniteur (bpp) : <SELECT NAME=bpp>\n");
		print("<OPTION SELECTED VALUE=\"PasDeModif\">Je ne veux pas modifier ce champ</OPTION>\n");
		print("<OPTION VALUE=\"0\">non definie i.e. idem image de base</OPTION>\n");
		foreach ($GLOBALS['bpps'] as $bpp)
		{
			print("<OPTION> $bpp \n");
		}
		print("</SELECT></P>");
		print("<P><FONT COLOR=blue SIZE=-1>Syntaxe modeline : --&gt;dotclock hres x x x vres y y y&lt;--. Exemple : --&gt;139.93 1024 1072 1312 1408 768 770 782 808&lt;--</FONT></P>\n");
			# Syntaxe Modeline :
			# dotclock width x x x heigth y y y
			# Exemple :
			# 139.93 1024 1072 1312 1408 768 770 782 808
		print("<P>Modeline moniteur (modeline) : <INPUT TYPE=TEXT NAME=modeline SIZE=50></P>");

#		print("<H2>Disque dur</H2>");
#		print("<P><FONT COLOR=RED SIZE=-1>En cas de changement de disque dur, il est pratique de declarer ici sa nouvelle taille, sans avoir a faire de redetection...</FONT></P>\n");
#		while ($ligne2 = mysql_fetch_array($result2))
#		{
#			print("<P>Capacite du disque ".$ligne2["num_disque"]." : <INPUT TYPE=TEXT NAME=capacite[".$ligne2["num_disque"]."]"."SIZE=30 VALUE=".$ligne2["capacite"]."></P>");
#		}
#
		print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
		print("</FORM>");
		
		# Fin Formulaire
		
	}
	# Cas ou on a deja fait des modifs via le formulaire ci-dessus
	# Il faut maintenant modifier la base en fonction de ce qui a ete rentre 
	else
	{
		# On recupere les variables qui n'existe que si $modif_base==1 donc si on est déjà passé dans choix_machines_nombreuses.php
		if (isset($_POST["ram"])) { $ram = $_POST["ram"]; }
		if (isset($_POST["affiliation_windows"])) { $affiliation_windows = $_POST["affiliation_windows"]; }
		if (isset($_POST["nom_affiliation"])) { $nom_affiliation = $_POST["nom_affiliation"]; }
		if (isset($_POST["ou"])) { $ou = $_POST["ou"]; }
		if (isset($_POST["poweroff"])) { $poweroff = $_POST["poweroff"]; }
		if (isset($_POST["resolution"])) { $resolution = $_POST["resolution"]; }
		if (isset($_POST["hfreq"])) { $hfreq = $_POST["hfreq"]; }
		if (isset($_POST["vfreq"])) { $vfreq = $_POST["vfreq"]; }
		if (isset($_POST["bpp"])) { $bpp = $_POST["bpp"]; }
		if (isset($_POST["modeline"])) { $modeline = $_POST["modeline"]; }

		# On ne fait les verifs de syntaxe que si les champs ont été rempli car on veut pouvoir ici
		# les laisser vide ce qui signifie qu'on ne change rien pour la sélection de machines
		# pour le champ en question...
		if (! empty($ram))
		{
			if (! preg_match("/^[0-9]+$/", $ram))
			{
				erreur_saisie_formulaire("RAM");
			}
		}
		if (! empty($nom_affiliation))
		{
			if (! preg_match("/^[a-zA-Z0-9\-\.]+$/", $nom_affiliation))
			{
				erreur_saisie_formulaire("Nom Affiliation");
			}
		}
		if (! empty($ou))
		{
			# On impose que l'OU soit soit vide, soit reponde a [ou=...,]..[ou=...,][dc=...,]..[dc=...,]
			# sans espace nulle part, ce qui est un peu plus restrictif que la syntaxe officielle...
			if (! preg_match("/^$|^(ou=[^,\s]+,)+(dc=[^,\s]+,)*dc=[^,\s]+$/", $ou))
			{
				erreur_saisie_formulaire("OU (Organisational Unit)");
			}
		}
		if ($resolution != "PasDeModif")
		{
			list($hres, $vres) = explode("x", $resolution);
		}
		if (! empty($hfreq))
		{
			$hfreq = $_POST["hfreq"];
			if ($hfreq != 0 and ! preg_match("/^[1-9]([0-9]){1,2}$/", $hfreq))
			{
				erreur_saisie_formulaire("Fréquence horizontale moniteur");
			}
		}
		if (! empty($vfreq))
		{
			$vfreq = $_POST["vfreq"];
			if ($vfreq != 0 and ! preg_match("/^[1-9]([0-9]){1,2}$/", $vfreq))
			{
				erreur_saisie_formulaire("Fréquence verticale moniteur");
			}
		}
		if (! empty($modeline))
		{
			# Syntaxe Modeline :
			# dotclock width x x x heigth y y y
			# Exemple :
			# 139.93 1024 1072 1312 1408 768 770 782 808
			if (! preg_match("/^ *[0-9]+\.[0-9]+ +[1-9]([0-9]){1,3} +[1-9]([0-9]){1,3} +[1-9]([0-9]){1,3} +[1-9]([0-9]){1,3} +[1-9]([0-9]){1,3} +[1-9]([0-9]){1,3} +[1-9]([0-9]){1,3} +[1-9]([0-9]){1,3} *$/", $modeline))
			{
				erreur_saisie_formulaire("Modeline");
			}
		}
		#$capacite = $_POST["capacite"];
		## On decrit tous les disques. Dans le formulaire, 
		## on a donne comme nom capacite[x] au champ capacite 
		## pour le disque de numero x (num_disque).
		#for($i=0;$i<count($capacite);$i++)
		#{
		#	if (! preg_match("/^[0-9]+$/", $capacite[$i]))
		#	{
		#		erreur_saisie_formulaire("Capacite du disque $i");
		#	}
		#}
		# toutes les variables ont ete recuperees

		# On initialise a vide car il va etre incremente par les modifs sur chaque machine
		# et testé en sortie de boucle pour voir s'il y a des modifs 
		# On ne le fait que pour eux, car ce sont les seul qui sont machine-dependant
		$message_modif_ou = "";
		$message_modif_affiliation = "";

		print("<H2>Machines concernées par les modifications</H2>");
		print("<UL>\n");
		foreach ($ordinateurs_concernes as $nom_dns)
		{
			if (est_verrouille_pour_mon_ip($nom_dns))
			{
				print("<LI>$nom_dns</LI>\n");

				if (! empty($ram))
				{
					mysql_query("UPDATE ordinateurs set ram=\"$ram\" where nom_dns=\"$nom_dns\"");
					$message_modif_ram = "<LI><FONT COLOR=RED>RAM modifiée à : <B>$ram</B></FONT></LI>\n";
				}
				else
				{
					$message_modif_ram = "<LI><FONT COLOR=GREEN>RAM : pas de modification</FONT></LI>\n";
				}

				# Affiliation et NomAffiliation

				# On checke l'état avant la modif.
				# Ca servira pour l'affiliation ET pour l'OU
				$request = "SELECT  sid, affiliation_windows, nom_affiliation, ou FROM ordinateurs WHERE nom_dns=\"$nom_dns\"";
				$result = mysql_query($request);
				$ligne = mysql_fetch_array($result);
				# On garde les anciens affiliation ou nom affiliation si les nouveaux
				# sont vides : l'utilisateur les a laissés blancs dans ce but
				$old_affiliation = $ligne["affiliation_windows"];
				$old_ou = $ligne["ou"];

				# On ne fait de modifs sur l'affiliation QUE SI au moins une valeur a été donnée
				# Dans le cas contraire ça signifie qu'on ne voulait pas faire de modif...
				# Notons qu'on permet donc qu'on change juste le nom du {domain/sambadomain/workgroup}
				# ou qu'au contraire on change juste le type de domain en gardant le nom...
				if ($affiliation_windows != "PasDeModif" or !empty($nom_affiliation))
				{
	
					# On fait une verification sur le changement de workgroup -> domaine/sambadomain ou de domaine -> domaineSamba ou de domaineSamba -> domaine ou de domaine -> autre domaine ou de domainesamba -> autre domaine Samba : car dans ces cas, il faut détruire le SID.
					# En effet, on ne fait JoinDomain que si on n'a pas de SID (pour accelerer ET pour s'affranchir au max des ratages fréquents d'entrée dans le domainei avec JoinDomain) : donc on supprime le SID pour s'assurer qu'on fera bien JoinDomain dans les 2 cas suivants :
					# 1) si on a mis cette fois domain et qu'on était avant dans un workgroup 
					# 2) si on a mis cette fois domain et qu'on était avant dans un autre domaine.
					# On checke l'état avant la modif.
					$request = "SELECT  sid, affiliation_windows, nom_affiliation FROM ordinateurs WHERE nom_dns=\"$nom_dns\"";
					$result = mysql_query($request);
					$ligne = mysql_fetch_array($result);
					# On garde les anciens affiliation ou nom affiliation si les nouveaux
					# sont vides : l'utilisateur les a laissés blancs dans ce but
					$old_affiliation = $ligne["affiliation_windows"];
					if ($affiliation_windows != "PasDeModif")
					{
						$new_affiliation = $affiliation_windows;
					}
					else
					{
						$new_affiliation = $old_affiliation;
					}
					$old_nom_affiliation = $ligne["nom_affiliation"];
					if (!empty($nom_affiliation))
					{
						$new_nom_affiliation = $nom_affiliation;
					}
					else
					{
						$new_nom_affiliation = $old_nom_affiliation;
					}
					$old_sid = $ligne["sid"];
					# Par défaut on laisse l'ancien SID...
					$new_sid = $old_sid;
					# ...sauf si...
					if ($old_affiliation == "workgroup" AND $new_affiliation == "domain" OR $old_affiliation == "workgroup" AND $new_affiliation == "sambadomain" OR $old_affiliation == "domain" AND $new_affiliation == "sambadomain" OR $old_affiliation == "sambadomain" AND $new_affiliation == "domain" OR $old_affiliation == "domain" AND $new_affiliation == "domain" AND $old_nom_affiliation != $new_nom_affiliation OR $old_affiliation == "sambadomain" AND $new_affiliation == "sambadomain" AND $old_nom_affiliation != $new_nom_affiliation) { 
						# ...auquel cas on le vire
						$new_sid = "";
					}
	
					# On est prêt à commettre les modifs
					mysql_query("UPDATE ordinateurs set sid=\"$new_sid\", affiliation_windows=\"$new_affiliation\", nom_affiliation=\"$new_nom_affiliation\" where nom_dns=\"$nom_dns\"");
					$message_modif_affiliation .= "<LI><FONT COLOR=RED>Données d'affiliation modifiées à : <B>$new_affiliation $new_nom_affiliation</B></FONT></FONT></LI>\n";
				}

				# OU
				$new_ou = $ou;
				# Independamment du fait que la nouvelle OU soit vide ou precisee, 
				# on vide de force l'OU, car elle n'a pas de sens, si :
				# - on a modifie l'affiliation windows et la nouvelle n'est pas "domain"
				# - on n'a pas modifie l'affiliation windows, et elle ne vaut pas "domain"
				if ($affiliation_windows != "PasDeModif" and $affiliation_windows != "domain" or $affiliation_windows == "PasDeModif" and $old_affiliation != "domain") 
				{
					$ou="";
					if (!empty($old_ou)) # sinon, pas de modif. car deja vide avant...
					{
						mysql_query("UPDATE ordinateurs set ou=\"$ou\" where nom_dns=\"$nom_dns\"");
						$message_modif_ou .= "<LI><FONT COLOR=RED>$nom_dns : OU vidée (ancienne valeur $old_ou) car n'a pas de sens avec (affiliation windows != domain)</FONT></LI>\n";
					}
				}
				else # L'OU a du sens, on ne la vide pas de force
				{
					# Cas nouvelle "OU" non vide ET differente de l'ancienne : on fait la modif
					# Sinon, pas de modif...
					if (! empty($new_ou) and $new_ou != $old_ou)
					{
						mysql_query("UPDATE ordinateurs set ou=\"$new_ou\" where nom_dns=\"$nom_dns\"");
						$message_modif_ou .= "<LI><FONT COLOR=RED>$nom_dns : OU modifiée de -->$old_ou<-- à <B>$new_ou</B></FONT></LI>\n";
					}
				}

				# Le type d'extinction
				if ($poweroff != "PasDeModif")
				{
					# On est prêt à commettre les modifs
					mysql_query("UPDATE ordinateurs set poweroff=\"$poweroff\" where nom_dns=\"$nom_dns\"");
					$message_modif_poweroff = "<LI><FONT COLOR=RED>Type d'extinction modifié à : <B>$poweroff</B></FONT></FONT></LI>\n";
				}
				else
				{
					$message_modif_poweroff = "<LI><FONT COLOR=GREEN>Type d'extinction : pas de modification</FONT></LI>\n";
				}

				if ( $resolution != "PasDeModif")
				{

					# On est prêt à commettre les modifs
					mysql_query("UPDATE ordinateurs set hres=\"$hres\", vres=\"$vres\" where nom_dns=\"$nom_dns\"");
					$message_modif_resolution = "<LI><FONT COLOR=RED>Résolution modifiée à : <B>${hres}x$vres</B></FONT></LI>\n";
				}
				else
				{
					$message_modif_resolution = "<LI><FONT COLOR=GREEN>Résolution : pas de modification</FONT></LI>\n";
				}

				if (! empty($hfreq))
				{
					mysql_query("UPDATE ordinateurs set hfreq=\"$hfreq\" where nom_dns=\"$nom_dns\"");
					$message_modif_hfreq = "<LI><FONT COLOR=RED>Fréquence horizontale modifiée à : <B>$hfreq</B></FONT></LI>\n";
				}
				else
				{
					$message_modif_hfreq = "<LI><FONT COLOR=GREEN>Fréquence horizontale : pas de modification</FONT></LI>\n";
				}

				if (! empty($vfreq))
				{
					mysql_query("UPDATE ordinateurs set vfreq=\"$vfreq\" where nom_dns=\"$nom_dns\"");
					$message_modif_vfreq = "<LI><FONT COLOR=RED>Fréquence verticale modifiée à : <B>$vfreq</B></FONT></LI>\n";
				}
				else
				{
					$message_modif_vfreq = "<LI><FONT COLOR=GREEN>Fréquence verticale : pas de modification</FONT></LI>\n";
				}

				if ( $bpp != "PasDeModif")
				{
					mysql_query("UPDATE ordinateurs set bpp=\"$bpp\" where nom_dns=\"$nom_dns\"");
					$message_modif_bpp = "<LI><FONT COLOR=RED>Color depth (bpp) modifiée à : <B>$bpp</B></FONT></LI>\n";
				}
				else
				{
					$message_modif_bpp = "<LI><FONT COLOR=GREEN>Color depth (bpp) : pas de modification</FONT></LI>\n";
				}

				if (! empty($modeline))
				{

					mysql_query("UPDATE ordinateurs set modeline=\"$modeline\" where nom_dns=\"$nom_dns\"");
					$message_modif_modeline = "<LI><FONT COLOR=RED>Modeline modifié à : <B>$modeline</B></FONT></LI>\n";
				}
				else
				{
					$message_modif_modeline = "<LI><FONT COLOR=GREEN>Modeline : pas de modification</FONT></LI>\n";
				}
				## hres=\"$hres\", vres=\"$vres\", hfreq=\"$hfreq\", vfreq=\"$vfreq\", bpp=\"$bpp\", modeline=\"$modeline\" 

				# On decrit tous les disques. Dans le formulaire, 
				# on a donne comme nom capacite[x] au champ capacite 
				# pour le disque de numero x (num_disque).
#				for($i=0;$i<count($capacite);$i++)
#				{
#					mysql_query("UPDATE stockages_de_masse set capacite=\"$capacite[$i]\" where nom_dns=\"$nom_dns\" AND num_disque=\"$i\"");
#				}
#				# On deverrouille la machine

				deverrouille_pour_mon_ip($nom_dns);
			}
			else 
			{
				print("La sélection multiple a expiré. Retournez à l'<A HREF=accueil.php>accueil</A> pour relancer une procédure de modification machine.<BR>\n");
				break 1;
			}
		}
		print("</UL>\n");
		# On printe hors de la boucle, car les modifs sont les memes pour toutes les machines, sauf pour l'OU...
		# Pour l'OU, il peut y avoir des modifs differentes selon l'etat AVANT et selon la correlation
		# affiliation windows et OU, donc on a un traitement particulier : soit il y a eu des modifs
		# pour certaines machines et message_modif_ou n'est pas vide (on le remplit incrementalement
		# au fil des machines et uniquement avec les modifs au contraire des autres, qui sont 
		# machine-independants i.e. identique pour toutes les machines, ce qui signifie au passage 
		# qu'on instancie les messages modif par la meme valeur autant de fois qu'il y a de machines,
		# pas genial...), soit il est vide i.e. pas de modif
		if (empty($message_modif_ou))
		{
			$message_modif_ou = "<LI><FONT COLOR=GREEN>OU : pas de modification</FONT></LI>\n";
		}
		else
		{
			$message_modif_ou = "<LI><FONT COLOR=RED>OU :</FONT>\n<UL>\n$message_modif_ou</UL>\n";
		}
		if (empty($message_modif_affiliation))
		{
			$message_modif_affiliation = "<LI><FONT COLOR=GREEN>Données d'affiliation : pas de modification</FONT></LI>\n";
		}
		else
		{
			$message_modif_affiliation = "<LI><FONT COLOR=RED>Données d'affiliation :</FONT>\n<UL>\n$message_modif_affiliation</UL>\n";
		}
		print("<H2>Modifications effectuées</H2>");
		print ("<UL>\n");
		print ("$message_modif_ram\n");
		print ("$message_modif_affiliation\n");
		print ("$message_modif_ou\n");
		print ("$message_modif_poweroff\n");
		print ("$message_modif_resolution\n");
		print ("$message_modif_hfreq\n");
		print ("$message_modif_vfreq\n");
		print ("$message_modif_bpp\n");
		print ("$message_modif_modeline\n");
		print ("</UL>\n");
	}
}

print("<BR><HR><P><CENTER><A HREF=accueil.php>Retour</A></CENTER>\n");
DisconnectMySQL();


PiedPage();
//FIN Main()

?>
