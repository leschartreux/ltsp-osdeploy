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
include ("DBParDefaut.php");

entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Copie Machine");

print("<CENTER><H1>Copie Machine</H1></CENTER>\n");

ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

function insertion_base($nom_dns,$nom_dns2) {
	global $affected;
	#poweroff et ou ne sont plus des champs de ordinateurs ...
	#les postes inseres sont en etat installe par defaut
	#Pyddlaj fonctionne par Tâche. Elles sont correctement gérées lors des lancements de tâches
	$request="INSERT INTO ordinateurs "
	."(nom_dns,nom_netbios,etat_install,affiliation_windows,nom_affiliation,marque,modele,nombre_proc,processeur,frequence,socket,ram,type_ram,nombre_slots,slots_libres,signature,hres,vres,vfreq,hfreq,bpp,modeline)"
	." SELECT \"$nom_dns2\",\"".substr($nom_dns2,0,strpos($nom_dns2,"."))."\",\"installe\",affiliation_windows,nom_affiliation,marque,modele,nombre_proc,processeur,frequence,socket,ram,type_ram,nombre_slots,slots_libres,signature,hres,vres,vfreq,hfreq,bpp,modeline FROM ordinateurs WHERE nom_dns=\"$nom_dns\"";
	mysql_query($request);
	// On vérifie que l'insertion de la machine dans la base a bien fonctionné
	// car les enregistrements suivants dépendent tous de cette entrée
	if (mysql_affected_rows()>0) {
		$affected++;
		$request="INSERT INTO composant_est_installe_sur(id_comp_sur,nom_dns,id_composant,ajout) SELECT id_comp_sur,\"$nom_dns2\",id_composant,ajout FROM composant_est_installe_sur WHERE nom_dns=\"$nom_dns\"";
		mysql_query($request);
		$request="INSERT INTO stockages_de_masse(nom_dns,type,connectique,capacite,num_disque,linux_device,dd_a_partitionner) SELECT \"$nom_dns2\",type,connectique,capacite,num_disque,linux_device,\"oui\" FROM stockages_de_masse WHERE nom_dns=\"$nom_dns\"";
		mysql_query($request);
		$request="INSERT INTO partitions(nom_dns,num_disque,num_partition,taille_partition,type_partition,nom_partition,systeme,linux_device) SELECT \"$nom_dns2\",num_disque,num_partition,taille_partition,type_partition,nom_partition,systeme,linux_device FROM partitions WHERE nom_dns=\"$nom_dns\"";
		mysql_query($request);
		$request="INSERT INTO idb_est_installe_sur (nom_dns,id_idb,num_disque,num_partition,etat_idb,cache,boot_options) SELECT \"$nom_dns2\",id_idb,b.num_disque,b.num_partition,\"a_ajouter\",cache,boot_options FROM idb_est_installe_sur AS a, partitions AS b WHERE a.nom_dns=\"$nom_dns\" AND a.nom_dns=b.nom_dns AND a.num_disque=b.num_disque AND a.num_partition=b.num_partition";
		mysql_query($request);
		$request="INSERT INTO package_est_installe_sur (nom_dns,id_package,num_disque,num_partition,etat_package) SELECT \"$nom_dns2\",id_package,b.num_disque,b.num_partition,\"a_ajouter\" FROM package_est_installe_sur AS a, partitions AS b WHERE a.nom_dns=\"$nom_dns\" AND a.nom_dns=b.nom_dns AND a.num_disque=b.num_disque AND a.num_partition=b.num_partition AND etat_package!=\"a_supprimer\"";
		mysql_query($request);
		$request="INSERT INTO ord_appartient_a_gpe (nom_dns,nom_groupe) SELECT \"$nom_dns2\",nom_groupe FROM ord_appartient_a_gpe WHERE nom_dns=\"$nom_dns\"";	
		mysql_query($request);
	}
}

$copier=false;
$nom_dns=$_GET["nom_dns"];
if (isset($_GET["nom_dns2"])) {
	$nom_dns2=str_replace(" ","",$_GET["nom_dns2"]);
	$tab=preg_replace("/(.*)\[([0-9]+-[0-9]+)\](.*)/","$1 $2 $3",$nom_dns2);
	if ($tab!=$nom_dns2) {
		list ($nom_dns2_pre,$laps,$nom_dns2_suf)=split(" ",$tab);
		list ($from,$to)=split("-",$laps);
		$pad=strlen($from);
	}
	if (isset($_GET["copier"])) $copier=true;
}

if ($copier) {
	$affected=0;
	if (isset($from)) 
		for ($i=$from;$i<=$to;$i++) insertion_base($nom_dns,sprintf("%s%0${pad}d%s",$nom_dns2_pre,$i,$nom_dns2_suf));
	else insertion_base($nom_dns,$nom_dns2);
}

print("<CENTER>\n");
print("<FORM METHOD=GET NAME=\"form\">\n");
print("<TABLE>\n");
print("<TR><TD>Source : </TD>\n");
print("<TD><SELECT name=\"nom_dns\" onChange=\"document.form.nom_dns2.enabled=true;parent.location.href='copie_machine.php?nom_dns='+document.form.nom_dns.options[document.form.nom_dns.selectedIndex].value\">\n");
if ($nom_dns=="") print("<OPTION SELECTED></OPTION>\n");
$request = "SELECT nom_dns FROM ordinateurs";
$result = mysql_query($request);
for ($i=0;$i<mysql_num_rows($result);$i++) {
  $line = mysql_fetch_array($result);
  print("<OPTION value=\"".$line["nom_dns"]."\"");
	if ($line["nom_dns"]==$nom_dns) print("SELECTED");
	print(">".$line["nom_dns"]."</OPTION>\n");
}
print("</SELECT></TD>");
mysql_free_result($result);
print("<TD>Destination : </TD>\n");
print("<TD><INPUT TYPE=\"text\" SIZE=\"50\" NAME=\"nom_dns2\"");
if (isset($nom_dns2)) print(" VALUE=\"$nom_dns2\"");
if ($nom_dns=="") print(" DISABLED");
print("></TD>");
print("</TR>\n</TABLE>\n");
print("</FORM>\n");

if (isset($nom_dns2) && !$copier) {
	if ($nom_dns2=="") print("<FONT COLOR=\"red\"><I>Nom DNS incorrect</I></FONT>\n");
	else {
		if (isset($from)) {
			$request_end=" IN (";
			for ($i=$from;$i<$to;$i++) $request_end.=sprintf("\"%s%0${pad}d%s\",",$nom_dns2_pre,$i,$nom_dns2_suf);
			$request_end.=sprintf("\"%s%0${pad}d%s\") LIMIT 1",$nom_dns2_pre,$i,$nom_dns2_suf);
		} else $request_end="=\"$nom_dns2\"";
		$request="SELECT nom_dns FROM ordinateurs WHERE nom_dns='$request_end'";	
		$result=mysql_query($request);
		if (mysql_num_rows($result)!=0) {
			$line=mysql_fetch_array($result);
			print("<FONT COLOR=\"red\"><I>Le nom DNS $line[nom_dns] existe déjà dans la base</I></FONT>\n");
		} else print("<INPUT TYPE=\"button\" VALUE=\"Copier\" onClick=\"location.href='copie_machine_frame1.php?nom_dns=$nom_dns&nom_dns2=$nom_dns2&copier'\">\n");
		mysql_free_result($result);
	}
}
if ($copier) printf("<FONT COLOR=\"red\"><I>%d copie%s effectuée%s. Saississez un nouveau nom DNS.</I></FONT>\n",$affected,($affected>1?"s":""),($affected>1?"s":""));
print("</CENTER>\n");

print("<P><CENTER><A HREF=\"javascript:parent.location.href='accueil.php'\">Retour</A></CENTER></P>\n");

DisconnectMySQL();

PiedPage();
?>		
