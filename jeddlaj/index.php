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

# Main()
entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : accueil");
include ("DBParDefaut.consult.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<CENTER>");
print("<TABLE BORDER=0 WIDTH=100%>");
print("<TR ALIGN=CENTER>");
print("<TD ALIGN=LEFT><A HREF=\"http://la.firme.perso.esil.univmed.fr\" TARGET=\"LaFIRME\" ><IMG ALIGN=CENTER BORDER=0 SRC=\"LOGOS/CopyleftLaFirmeWhiteBack.png\" ALT=\"CopyLeft La FIRME@ESIL\"></TD>");
print("<TD ALIGN=CENTER><A HREF=\"HWC/index.php\"><IMG ALIGN=CENTER SRC=\"LOGOS/JeDDLaJ_current.png\" BORDER=0 ALT=\"JeDDLaJ's Logo\"></A></TD>");
print("<TD ALIGN=RIGHT><A HREF=\"http://la.firme.perso.esil.univmed.fr/website/rubrique.php3?id_rubrique=11\" TARGET=\"LaDoc\" ><IMG ALIGN=CENTER BORDER=0 SRC=\"LOGOS/UnPeuDeDocWhiteBack.png\" ALT=\"De la doc...\"></TD>");
print("</TR>");
print("</TABLE>");
print("</CENTER>");
print("<CENTER><H1>Accueil</H1></CENTER>\n");

############ Info 

$request="SELECT etat_install,COUNT(etat_install) AS total,nom_dns FROM ordinateurs GROUP BY etat_install";
$result=mysql_query($request);
$total_ordinateurs=0;
$ligne_info="";
for ($i=0;$i<mysql_num_rows($result);$i++) {
	$line=mysql_fetch_array($result);
	$total_ordinateurs+=$line["total"];
	$s=($line["total"]>1)?"s":"";
	$ligne_info.="/<A HREF=\"";
	switch ($line["etat_install"]) {
		case "installe" :
			if ($line["total"]==1) $ligne_info.="examine_machine.php?nom_dns=$line[nom_dns]";
			else $ligne_info.="javascript:document.form.request.value='SELECT * FROM ordinateurs WHERE etat_install=\'$line[etat_install]\'';document.form.submit()";
			$ligne_info.="\">$line[total] ordinateur$s installé$s";
			break;
		case "modifie":
			if ($line["total"]==1) $ligne_info.="examine_machine.php?nom_dns=$line[nom_dns]";
			else $ligne_info.="javascript:document.form.request.value='SELECT * FROM ordinateurs WHERE etat_install=\'$line[etat_install]\'';document.form.submit()";
			$ligne_info.="\">$line[total] ordinateur$s modifié$s";
			break;
		case "en_cours":
			if ($line["total"]==1) $ligne_info.="examine_machine.php?nom_dns=$line[nom_dns]";
			else $ligne_info.="javascript:document.form.request.value='SELECT * FROM ordinateurs WHERE etat_install=\'$line[etat_install]\'';document.form.submit()";
			$ligne_info.="\">$line[total] ordinateur$s en cours d'installation";
			break;
		case "package":
			if ($line["total"]==1) $ligne_info.="examine_machine.php?nom_dns=$line[nom_dns]";
			else $ligne_info.="javascript:document.form.request.value='SELECT * FROM ordinateurs WHERE etat_install=\'$line[etat_install]\'';document.form.submit()";
			$ligne_info.="\">$line[total] ordinateur$s en création de packages";
			break;
		case "idb":
			if ($line["total"]==1) $ligne_info.="examine_machine.php?nom_dns=$line[nom_dns]";
			else $ligne_info.="javascript:document.form.request.value='SELECT * FROM ordinateurs WHERE etat_install=\'$line[etat_install]\'';document.form.submit()";
			$ligne_info.="\">$line[total] ordinateur$s en création d'images de base";
			break;
		case "depannage":
			$ligne_info.="depannage_1.php\">$line[total] ordinateur$s en attente de dépannage";
			break;
	}
	$ligne_info.="</A>";
}
mysql_free_result($result);
$request="SELECT COUNT(a.nom_dns) AS total,a.nom_dns FROM ordinateurs AS a LEFT JOIN partitions AS b ON a.nom_dns=b.nom_dns WHERE num_disque IS NULL GROUP BY num_disque";
$result=mysql_query($request);
$line=mysql_fetch_array($result);
if ($line["total"]>0) {
	$s=($line["total"]>1)?"s":"";
	$ligne_info.="/<A HREF=\"";
	if ($line["total"]==1) $ligne_info.="examine_machine.php?nom_dns=$line[nom_dns]";
	else $ligne_info.="javascript:document.form.request.value='SELECT * FROM ordinateurs AS a LEFT JOIN partitions AS b ON a.nom_dns=b.nom_dns WHERE num_disque IS NULL';document.form.submit()";
	$ligne_info.="\">$line[total] ordinateur$s non configure$s</A>";
}
print("<FORM NAME=form METHOD=POST ACTION=Interro.php>\n");
print("<INPUT TYPE=hidden NAME=db VALUE=$GLOBALS[db]>\n");
print("<INPUT TYPE=hidden NAME=request>\n");
print("<H3><A HREF=\"examine_groupe.php?nom_groupe=tous%20les%20ordinateurs\">$total_ordinateurs ordinateurs</A>$ligne_info <A HREF=\"javascript:location.reload()\"><IMG ALIGN=CENTER BORDER=0 SRC=\"ICONES/recycler.png\"></A></H3>\n");
print("</FORM>\n");
			
############ Menu général

# On attaque la tables Ordinateurs pour les infos générales

print("<H2><A HREF=\"examine_machine.php\">Examiner</A>/<A HREF=\"copie_machine.php\">Copier</A>/<A HREF=\"modifier_machine_wrapper.php\">Modifier</A>/"
#."<A HREF=\"choix_machines_multiples.php?action=packages\">Passer en état \"Création de packages\"</A>"
."/<A HREF=\"choix_machines_multiples.php?action=idbs\">Passer en état \"Création d'images de base\"</A>/<A HREF=\"choix_machines_multiples.php?action=suppression\">Supprimer</A> machine</H2>\n");
print("<H2><A HREF=\"examine_groupe.php\">Examiner</A>/<A HREF=\"choix_groupes_multiples.php?action=creation_de_groupe\">Créer</A>/<A HREF=\"choix_groupes_multiples.php?action=modification_de_groupe\">Modifier</A>/<A HREF=\"choix_groupes_multiples.php?action=suppression\">Supprimer</A> groupe</H2>\n");
#print("<H2><A HREF=\"configuration_logicielle_0.php\">Modifier configuration logicielle</A>/<A HREF=\"reinstallation.html\">Réinstaller/Synchroniser</A> machine ou groupe</H2>\n");
print("<H2><A HREF=\"lancement_tache_0.php\">Lancement d'une tâche</A>/ avec Pyddlaj</H2>\n");
print("<H2><A HREF=\"ajouter_image_de_base.php?action=NewIdb\">Ajouter</A>/<A HREF=\"editer_ou_supprimer_image_de_base.php?mode=edition&action=ChoixOS\">Éditer</A>/<A HREF=\"editer_ou_supprimer_image_de_base.php?mode=suppression&action=ChoixOS\">Supprimer</A> image_de_base</H2>\n");
#print("<H2><A HREF=\"examine_logiciel.php\">Examiner</A>/<A HREF=\"editer_ou_supprimer_logiciel.php?mode=edition&action=ChoixOS\">Éditer</A>/<A HREF=\"editer_ou_supprimer_logiciel.php?mode=suppression&action=ChoixOS\">Supprimer</A> logiciel</H2>\n");
print("<H2><A HREF=\"examine_distribution.php\">Examiner</A>/<A HREF=\"editer_ou_supprimer_distribution.php?mode=edition&action=ChoixOS\">Éditer</A>/<A HREF=\"editer_ou_supprimer_distribution.php?mode=suppression&action=ChoixOS\">Supprimer</A> distribution</H2>\n");
#print("<H2><A HREF=\"ajouter_package.php?action=NewPackage\">Ajouter</A>/<A HREF=\"editer_ou_supprimer_package.php?mode=edition&action=ChoixOS\">Éditer</A>/<A HREF=\"editer_ou_supprimer_package.php?mode=suppression&action=ChoixOS\">Supprimer</A> package</H2>\n");
#print("<H2><A HREF=\"ajouter_postinstall_script.php?action=AjoutPostInstScript\">Ajouter</A>/<A HREF=\"editer_ou_supprimer_postinstall_script.php?mode=edition&action=ChoixPostInstallScript\">Éditer</A>/<A HREF=\"editer_ou_supprimer_postinstall_script.php?mode=suppression&action=ChoixPostInstallScript\">Supprimer</A> postinstall_script</H2>\n");
#print("<H2><A HREF=\"ajouter_predeinstall_script.php?action=AjoutPreDeinstScript\">Ajouter</A>/<A HREF=\"editer_ou_supprimer_predeinstall_script.php?mode=edition&action=ChoixPreDeinstallScript\">Éditer</A>/<A HREF=\"editer_ou_supprimer_predeinstall_script.php?mode=suppression&action=ChoixPreDeinstallScript\">Supprimer</A> predeinstall_script</H2>\n");
#print("<H2><A HREF=\"wol.html\">Réveil par le réseau</A>/<A HREF=\"boot.html\">Paramètres de boot</A></H2>\n");
print("<H2><A HREF=\"Interro.php\">Consulter la base JeDDLaJ</A>");
#/<A HREF=\"connect.html\">Connexion au serveur Rembo</A></H2>\n");


########### Les Addons

print("<H2><A HREF=\"addons.php\">Addons</A> : ");
$request="SELECT * FROM addons WHERE actif='oui'";
$result=mysql_query($request);
for ($i=0;$i<mysql_num_rows($result);$i++) {
	if ($i>0) print("/");
	$line=mysql_fetch_array($result);
	print("<A HREF=\"addons/$line[start_page]\">$line[nom]</A>\n");    
}
print("</H2>");

DisconnectMySQL();

PiedPage();
//FIN Main()

?>
