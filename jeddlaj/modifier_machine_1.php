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
if (isset($_GET["nom_dns"])) {$nom_dns = $_GET["nom_dns"];}
if (isset($_POST["modif_base"])) {$modif_base = $_POST["modif_base"];}
# toutes les variables ont ete recuperees


include("UtilsHTML.php");
include("UtilsMySQL.php");
include("UtilsJeDDLaJ.php");


# Main()
entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Machine $nom_dns, informations générales");
print("<CENTER><H1>$nom_dns : informations générales</H1></CENTER>\n");

include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);


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
	# Cas ou on arrive a peine de modifier_machine
	if (!isset($modif_base))
	{
		$request1 = "SELECT nom_netbios, numero_serie, ram, adresse_mac, adresse_ip, netmask, gateway, affiliation_windows,nom_affiliation,ou,hres,vres,hfreq,vfreq,bpp,modeline,poweroff FROM ordinateurs WHERE nom_dns=\"$nom_dns\"";
		$request2 = "SELECT num_disque, capacite, connectique, linux_device FROM stockages_de_masse where nom_dns=\"$nom_dns\" AND type=\"disque dur\"";
	

	
		$result1 = mysql_query($request1);
		$result2 = mysql_query($request2);
		$ligne1 = mysql_fetch_array($result1);
		
		
		# Formulaire
		
		print("<FORM METHOD=POST ACTION=\"modifier_machine_1.php?nom_dns=$nom_dns\">\n");
		print("<INPUT TYPE=HIDDEN NAME=modif_base VALUE=1>\n");
		print("<H2>Main stuff</H2>");
		print("<P>Nom netbios : <INPUT TYPE=TEXT NAME=nom_netbios SIZE=30 VALUE=".$ligne1["nom_netbios"]."></P>");
		print("<P>Numéro de série : <INPUT TYPE=TEXT NAME=numero_serie SIZE=30 VALUE=".$ligne1["numero_serie"]."></P>");
		print("<P>RAM : <INPUT TYPE=TEXT NAME=ram SIZE=30 VALUE=".$ligne1["ram"]."></P>");
		print("<P>Adresse MAC : <INPUT TYPE=TEXT NAME=adresse_mac SIZE=30 VALUE=".$ligne1["adresse_mac"]."></P>");
		print("<P>Adresse IP : <INPUT TYPE=TEXT NAME=adresse_ip SIZE=30 VALUE=".$ligne1["adresse_ip"]."></P>");
		print("<P>Masque de sous-réseau : <INPUT TYPE=TEXT NAME=netmask SIZE=30 VALUE=".$ligne1["netmask"]."></P>");
		print("<P>Passerelle : <INPUT TYPE=TEXT NAME=gateway SIZE=30 VALUE=".$ligne1["gateway"]."></P>");
		print("<P>Affiliation Windows : <SELECT NAME=affiliation_windows>\n");
		switch ($ligne1["affiliation_windows"])
		{
			case "sambadomain":
				print("<OPTION>\n <OPTION> workgroup \n <OPTION> domain \n <OPTION SELECTED> sambadomain ");
				break;
			case "domain":
				print("<OPTION>\n <OPTION> workgroup \n <OPTION SELECTED> domain \n <OPTION> sambadomain ");
				break;
			case "workgroup":
				print("<OPTION>\n <OPTION SELECTED> workgroup \n <OPTION> domain \n <OPTION> sambadomain ");
				break;
			default:
				print("<OPTION SELECTED>\n <OPTION> workgroup \n <OPTION> domain \n <OPTION> sambadomain ");
		}
		print("</SELECT></P>");
		print("<P>Nom Affiliation : <INPUT TYPE=TEXT NAME=nom_affiliation SIZE=30 VALUE=".$ligne1["nom_affiliation"]."></P>");
		print("<P>OU (Organisational Unit) : <INPUT TYPE=TEXT NAME=ou SIZE=30 VALUE=".$ligne1["ou"]."></P>");
		print("<P>Type d'extinction : <SELECT NAME=poweroff>\n");
		switch ($ligne1["poweroff"])
		{
			case "freedos":
				print("<OPTION>\n <OPTION SELECTED> freedos \n <OPTION> native ");
				break;
			case "native":
				print("<OPTION>\n <OPTION> freedos \n <OPTION SELECTED> native ");
				break;
			default:
				print("<OPTION SELECTED>\n <OPTION> freedos \n <OPTION> native ");
		}
		print("</SELECT></P>");
		print("<H2>Reglages affichage</H2>");
		print("<P><FONT COLOR=RED SIZE=-1>Les informations liees au moniteur declarees ci-dessous viennent se substituer a celle contenues dans l'image de base. Inutile donc de les preciser si celles de l'image conviennent.</FONT></P>\n");
		print("<P>Resolution moniteur : <SELECT NAME=resolution>\n");
		if ($ligne1["hres"] == 0 or $ligne1["vres"]==0)
		{
			$resolution = "non definie";
			print("<OPTION VALUE=\"0x0\" SELECTED>non definie</OPTION>\n");
		}
		else
		{
			$resolution = $ligne1["hres"]."x".$ligne1["vres"];
			print("<OPTION VALUE=\"0x0\">non definie</OPTION>\n");
		}
		foreach ($GLOBALS['resolutions'] as $res)
		{
			if ($res == $resolution)
			{
				print("<OPTION SELECTED> $res \n");
			}
			else
			{
				print("<OPTION> $res \n");
			}
		}
		print("</SELECT></P>");
		print("<P>Fréquence horizontale moniteur (hfreq) en kHz (0 si non definie) : <INPUT TYPE=TEXT NAME=hfreq SIZE=30 VALUE=".$ligne1["hfreq"]."></P>");
		print("<P>Fréquence verticale moniteur (vfreq) en Hz (0 si non definie) : <INPUT TYPE=TEXT NAME=vfreq SIZE=30 VALUE=".$ligne1["vfreq"]."></P>");
		print("<P>\"Color Depth\" moniteur (bpp) : <SELECT NAME=bpp>\n");
		if ($ligne1["bpp"] == 0)
		{
			print("<OPTION SELECTED VALUE=\"0\">non definie</OPTION>\n");
		}
		else
		{
			print("<OPTION VALUE=\"0\">non definie</OPTION>\n");
		}
		foreach ($GLOBALS['bpps'] as $bpp)
		{
			if ($bpp == $ligne1["bpp"])
			{
				print("<OPTION SELECTED> $bpp \n");
			}
			else
			{
				print("<OPTION> $bpp \n");
			}
		}
		print("</SELECT></P>");
		print("<P><FONT COLOR=blue SIZE=-1>Syntaxe modeline : --&gt;dotclock hres x x x vres y y y&lt;--. Exemple : --&gt;139.93 1024 1072 1312 1408 768 770 782 808&lt;--</FONT></P>\n");
			# Syntaxe Modeline :
			# dotclock width x x x heigth y y y
			# Exemple :
			# 139.93 1024 1072 1312 1408 768 770 782 808
		print("<P>Modeline moniteur (modeline) (vide si non defini) : <INPUT TYPE=TEXT NAME=modeline SIZE=50 VALUE=\"".$ligne1["modeline"]."\"></P>");

		print("<H2>Disque dur</H2>");
		print("<P><FONT COLOR=RED SIZE=-1>En cas de changement de disque dur, il est pratique de declarer ici sa nouvelle taille et eventuellement sa nouvelle connectique, sans avoir a faire de redetection...</FONT></P>\n");
		while ($ligne2 = mysql_fetch_array($result2))
		{
			print("<P>Capacite du disque ".$ligne2["num_disque"]." : <INPUT TYPE=TEXT NAME=capacite[".$ligne2["num_disque"]."] SIZE=30 VALUE=".$ligne2["capacite"]."></P>");
			print("<P>Connectique du disque".$ligne2["num_disque"]." : <SELECT NAME=connectique_HD[".$ligne2["num_disque"]."]>\n");
			switch ($ligne2["connectique"])
			{
				case "ide":
					print("<OPTION>\n <OPTION SELECTED> ide \n <OPTION> scsi ");
					break;
				case "scsi":
					print("<OPTION>\n <OPTION> ide \n <OPTION SELECTED> scsi ");
					break;
				default:
					print("<OPTION SELECTED>\n <OPTION> ide \n <OPTION> scsi ");
			}
			print("</SELECT></P>");
			print("<P>Device linux associee au disque ".$ligne2["num_disque"]." : <INPUT TYPE=TEXT NAME=linux_device_HD[".$ligne2["num_disque"]."] SIZE=30 VALUE=".$ligne2["linux_device"]."></P>");
		}

		print("<P><INPUT TYPE=SUBMIT VALUE=\"Valider\">   <INPUT TYPE=RESET VALUE=\"Annuler\"></P>\n");
		print("</FORM>");
		
		# Fin Formulaire
		
		mysql_free_result($result1);
		mysql_free_result($result2);
	}
	# Cas ou on a deja fait des modifs via le formulaire ci-dessus
	# Il faut maintenant modifier la base en fonction de ce qui a ete rentre 
	else
	{
		if (est_verrouille_pour_mon_ip($nom_dns))
		{

			# On recupere les variables qui n'existe que si $modif_base==1 donc si on est déjà passé dans modifier_machine_1.php
			$nom_netbios = $_POST["nom_netbios"];
			if (! preg_match("/^[a-zA-Z0-9\-]{1,15}$/", $nom_netbios))
			{
				erreur_saisie_formulaire("Nom Netbios");
			}
			$numero_serie = $_POST["numero_serie"];
			$ram = $_POST["ram"];
			if (! preg_match("/^[0-9]+$/", $ram))
			{
				erreur_saisie_formulaire("RAM");
			}
			$adresse_mac = $_POST["adresse_mac"];
			$adresse_ip = $_POST["adresse_ip"];
			$netmask = $_POST["netmask"];
			$gateway = $_POST["gateway"];
			# On fait les tests sur adresses mac, @ip, netmask 
			# et gateway si @IP n'est pas vide : on interdit a 
			# ce moment-la que @Mac, netmask et gateway soient vides.
			# En revanche, si @IP est vide, alors on permet que 
			# @MAC, netmask et gateway soient vides, car on peut etre
			# dans le cas d'une machine copiee par copier_machine.
			# Neanmoins, si @MAC et netmask ne sont pas vides,
			# alors ils doivent etre a la bonne syntaxe.
			# Au passage, ça permet une fausse manip.
			# qui est de vider ces champs dans le formulaire
			# pour une machine normale... Mais c'est pas grave
			# car le script Rembo va remplir ces champs s'il
			# voit que l'adresse ip n'est pas renseignée...
			if (!empty($adresse_ip))
			{
				if (! preg_match("/^([0-9]{1,3}\.){3}[0-9]{1,3}$/", $adresse_ip))
				{
					erreur_saisie_formulaire("Adresse IP");
				}
				if (! preg_match("/^([a-fA-F0-9]{2}[:]){5}[a-fA-F0-9]{2}$/", $adresse_mac))
				{
					erreur_saisie_formulaire("Adresse MAC");
				}
				if (! preg_match("/^([0-9]{1,3}\.){3}[0-9]{1,3}$/", $netmask))
				{
					erreur_saisie_formulaire("Masque de sous-réseau");
				}
				if (! preg_match("/^([0-9]{1,3}\.){3}[0-9]{1,3}$/", $gateway))
				{
					erreur_saisie_formulaire("Passerelle");
				}
			}
			else
			{
				if (! preg_match("/^$|^([a-fA-F0-9]{2}[:]){5}[a-fA-F0-9]{2}$/", $adresse_mac))
				{
					erreur_saisie_formulaire("Adresse MAC");
				}
				if (! preg_match("/^$|^([0-9]{1,3}\.){3}[0-9]{1,3}$/", $netmask))
				{
					erreur_saisie_formulaire("Masque de sous-réseau");
				}
				if (! preg_match("/^$|^([0-9]{1,3}\.){3}[0-9]{1,3}$/", $gateway))
				{
					erreur_saisie_formulaire("Passerelle");
				}
			}
			$affiliation_windows = $_POST["affiliation_windows"];
			$nom_affiliation = $_POST["nom_affiliation"];
			if (! preg_match("/^[a-zA-Z0-9\-\.]+$/", $nom_affiliation))
			{
				erreur_saisie_formulaire("Nom Affiliation");
			}
			# Traitement de l'OU
			# Mise a vide si nouvelle affectation != domain car l'OU n'a alors pas de sens
			if ($affiliation_windows != "domain")
			{
				$ou = "";
			}
			else
			{
				# On impose que l'OU soit soit vide, soit reponde a [ou=...,]..[ou=...,][dc=...,]..[dc=...,]
				#sans espace nulle part, ce qui est un peu plus restrictif que la syntaxe officielle...
				$ou = $_POST["ou"];
				if (! preg_match("/^$|^(ou=[^,\s]+,)+(dc=[^,\s]+,)*dc=[^,\s]+$/", $ou))
				{
					erreur_saisie_formulaire("OU (Organisational Unit)");
				}
			}
			$poweroff = $_POST["poweroff"];
			$resolution = $_POST["resolution"];
			list($hres, $vres) = explode("x", $resolution);
			$hfreq = $_POST["hfreq"];
			if ($hfreq != 0 and ! preg_match("/^[1-9]([0-9]){1,2}$/", $hfreq))
			{
				erreur_saisie_formulaire("Fréquence horizontale moniteur");
			}
			$vfreq = $_POST["vfreq"];
			if ($vfreq != 0 and ! preg_match("/^[1-9]([0-9]){1,2}$/", $vfreq))
			{
				erreur_saisie_formulaire("Fréquence verticale moniteur");
			}
			$bpp = $_POST["bpp"];
			$modeline = $_POST["modeline"];
			# Syntaxe Modeline :
			# dotclock width x x x heigth y y y
			# Exemple :
			# 139.93 1024 1072 1312 1408 768 770 782 808
			if ($modeline != "" and ! preg_match("/^ *[0-9]+\.[0-9]+ +[1-9]([0-9]){1,3} +[1-9]([0-9]){1,3} +[1-9]([0-9]){1,3} +[1-9]([0-9]){1,3} +[1-9]([0-9]){1,3} +[1-9]([0-9]){1,3} +[1-9]([0-9]){1,3} +[1-9]([0-9]){1,3} *$/", $modeline))
			{
				erreur_saisie_formulaire("Modeline");
			}
			$capacite = $_POST["capacite"];
			$connectique_HD = $_POST["connectique_HD"];
			$linux_device_HD = $_POST["linux_device_HD"];
			# toutes les variables ont ete recuperees

			### Verifications donnees disques
			# On decrit tous les disques. Dans le formulaire, 
			# on a donne comme nom capacite[x] et linux_device_HD[x]
			# au champs capacite et linux_device 
			# pour le disque de numero x (num_disque).
			for($i=0;$i<count($capacite);$i++)
			{
				if (! preg_match("/^[0-9]+$/", $capacite[$i]))
				{
					erreur_saisie_formulaire("Capacite du disque $i");
				}
				if (! preg_match("/^\/dev\/[hs]d[a-z]$/", $linux_device_HD[$i]))
				{
					erreur_saisie_formulaire("Device linux du disque $i");
				}
				# OK, bonne syntaxe du device linux... mais reste a
				# verifier qu'on a bien du /dev/hd avec ide et /dev/sd avec scsi...
				elseif (preg_match("/^\/dev\/h/", $linux_device_HD[$i]) and $connectique_HD[$i] == "scsi")
				{
					explication_erreur_saisie_formulaire("Device linux et connectique du disque $i", "Vous declarez une interface scsi et une device linux en /dev/hd : allons, faites un effort...<BR>");
				}
				elseif (preg_match("/^\/dev\/s/", $linux_device_HD[$i]) and $connectique_HD[$i] == "ide")
				{
					explication_erreur_saisie_formulaire("Device linux et connectique du disque $i", "Vous declarez une interface ide et une device linux en /dev/sd : allons, faites un effort...<BR>");
				}
			}
			### FIN Verifications donnees disques

			# On fait une verification sur le changement de workgroup -> domaine/sambadomain ou de domaine -> domaineSamba ou de domaineSamba -> domaine ou de domaine -> autre domaine ou de domainesamba -> autre domaine Samba : car dans ces cas, il faut détruire le SID.
			# En effet, on ne fait JoinDomain que si on n'a pas de SID (pour accelerer ET pour s'affranchir au max des ratages fréquents d'entrée dans le domaine avec JoinDomain) : donc on supprime le SID pour s'assurer qu'on fera bien JoinDomain dans les 2 cas suivants :
			# 1) si on a mis cette fois domain et qu'on était avant dans un workgroup 
			# 2) si on a mis cette fois domain et qu'on était avant dans un autre domaine.
			# On checke l'état avant la modif.
			$request = "SELECT  sid, affiliation_windows, nom_affiliation FROM ordinateurs WHERE nom_dns=\"$nom_dns\"";
			$result = mysql_query($request);
			$ligne = mysql_fetch_array($result);
			$old_affiliation = $ligne["affiliation_windows"];
			$new_affiliation = $affiliation_windows;
			$old_nom_affiliation = $ligne["nom_affiliation"];
			$new_nom_affiliation = $nom_affiliation;
			$old_sid = $ligne["sid"];
			# Par défaut on laisse l'ancien SID...
			$new_sid = $old_sid;
			# ...sauf si...
			if ($old_affiliation == "workgroup" AND $new_affiliation == "domain" OR $old_affiliation == "workgroup" AND $new_affiliation == "sambadomain" OR $old_affiliation == "domain" AND $new_affiliation == "sambadomain" OR $old_affiliation == "sambadomain" AND $new_affiliation == "domain" OR $old_affiliation == "domain" AND $new_affiliation == "domain" AND $old_nom_affiliation != $new_nom_affiliation OR $old_affiliation == "sambadomain" AND $new_affiliation == "sambadomain" AND $old_nom_affiliation != $new_nom_affiliation) { 
				# ...auquel cas on le vire
				$new_sid = "";
			}

			# On est prêt à commettre les modifs
			mysql_query("UPDATE ordinateurs set nom_netbios=\"$nom_netbios\", numero_serie=\"$numero_serie\", adresse_mac=\"$adresse_mac\", adresse_ip=\"$adresse_ip\", netmask=\"$netmask\", gateway=\"$gateway\", sid=\"$new_sid\", ram=\"$ram\", affiliation_windows=\"$affiliation_windows\", nom_affiliation=\"$nom_affiliation\", ou=\"$ou\", poweroff=\"$poweroff\", hres=\"$hres\", vres=\"$vres\", hfreq=\"$hfreq\", vfreq=\"$vfreq\", bpp=\"$bpp\", modeline=\"$modeline\" where nom_dns=\"$nom_dns\"");
			# On decrit tous les disques. Dans le formulaire, 
			# on a donne comme noms capacite[x], connectique_HD[x]
			# et linux_device_HD[x] aux champs capacite, 
			# connectique et linux_device pour le disque 
			# de numero x (num_disque).
			for($i=0;$i<count($capacite);$i++)
			{
				# UPDATE de la table stockages de masse...
				mysql_query("UPDATE stockages_de_masse set capacite=\"$capacite[$i]\", connectique=\"$connectique_HD[$i]\", linux_device=\"$linux_device_HD[$i]\" where nom_dns=\"$nom_dns\" AND num_disque=\"$i\" AND type=\"disque dur\"");
				# ... puis UPDATE de la table partitions
				# la device d'une partition = "device du disque"."num_partition"
				$request = "SELECT num_partition, linux_device FROM partitions where nom_dns=\"$nom_dns\" AND num_disque=\"$i\"";
				$result = mysql_query($request);
				while ($ligne = mysql_fetch_array($result))
				{
					$num_partition = $ligne['num_partition'];
					mysql_query("UPDATE partitions set linux_device=\"$linux_device_HD[$i]$num_partition\" where nom_dns=\"$nom_dns\" AND num_disque=\"$i\" AND num_partition=\"$num_partition\"");
				}
			}
			# On deverrouille la machine
			deverrouille_pour_mon_ip($nom_dns);
			printf ("<I>Vos modifications ont été insérées dans la base. Nombre d'enregistrements modifiés : %d. </I>\n", mysql_affected_rows());
		}
		else 
		{
			print("La sélection a expiré. Retournez à l'<A HREF=accueil.php>accueil</A> pour relancer une procédure de modification machine.<BR>\n");
		}
	}
}
print("<BR><HR><P><CENTER><A HREF=modifier_machine.php?nom_dns=$nom_dns>Retour</A></CENTER>\n");
DisconnectMySQL();


PiedPage();
//FIN Main()

?>
