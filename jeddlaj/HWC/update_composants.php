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

include("../UtilsHTML.php");
include("../UtilsMySQL.php");
include ("../DBParDefaut.php");

entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "../CSS/g.css", "JeDDLaJ : Mise à jour de la base des composants");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<CENTER><H1>Mise à jour de la base des composants</H1></CENTER>\n");

function myaddslashes($string) {
	return str_replace('"','\"',$string);
}

$types = array ("audio"=>"carte multimedia","bridge"=>"pont pci","broadband"=>"carte reseau","display"=>"carte video","ethernet"=>"carte reseau","ide"=>"controleur disque","isdn"=>"carte reseau","joystick"=>"peripherique entree","miscellaneous"=>"carte multimedia","modem"=>"port de communication","network"=>"carte reseau","pmc"=>"peripherique systeme","scsi"=>"controleur disque","sound"=>"carte multimedia","tvcard"=>"carte multimedia","unknown"=>"inconnu","usb"=>"port serie","video"=>"carte video");

$request="UPDATE composants SET type=\"inconnu\" WHERE type IS NULL OR type=\"\" OR type=\"unknown\" "; 
mysql_query($request);
$request="UPDATE composants SET module_linux=\"inconnu\" WHERE module_linux IS NULL OR module_linux=\"\" OR module_linux=\"unknown\""; 
mysql_query($request);
$request="UPDATE composants SET type_module=\"kernel\" WHERE type_module IS NULL OR type_module=\"\""; 
mysql_query($request);

$url="http://la.firme.perso.luminy.univ-amu.fr/JeDDLaJ/pub/downloads/pci.lst";
$ligne=file($url);


$operation="simuler";
$affichage="aucun";
$ignore_name=false;
$ignore_type=false;
$module_update_filter="EMPTY_PATTERN";
$module_update_filter_checked="";
$module_update_filter_disabled="DISABLED";
$module_ignore_filter="EMPTY_PATTERN";
$module_ignore_filter_checked="";
$module_ignore_filter_disabled="DISABLED";

if (isset($_POST["affichage"])) $affichage=$_POST["affichage"];
if (isset($_POST["operation"])) $operation=$_POST["operation"];
if (isset($_POST["ignore_name"])) $ignore_name=true;
if (isset($_POST["ignore_type"])) $ignore_type=true;
if (isset($_POST["module_update_filter"])) {
	$module_update_filter=$_POST["module_update_filter"];
	$module_update_filter_checked="CHECKED";
	$module_update_filter_disabled="";
}
if (isset($_POST["module_ignore_filter"])) {
	$module_ignore_filter=$_POST["module_ignore_filter"];
	$module_ignore_filter_checked="CHECKED";
	$module_ignore_filter_disabled="";
}


$new_entries=0;
$updated_fields=0;
$conflictual_fields=0;
$updated_entries=0;
$conflictual_entries=0;
$unchanged_entries=0;
$total_entries=0;
$mysql_insert=0;
$mysql_insert_failed=0;
$mysql_update=0;
$mysql_update_failed=0;

# Exemples de filtres :
# $module_update_filter = "vga|snd-.*|vesa|.*fb.*";
# $module_ignore_filter = "nvidia|radeon";

$tableau="";

print("<CENTER>\n");

if ($affichage!="aucun") {
	$tableau.="<TABLE BORDER=1>\n";
	$tableau.="<TR><TD ALIGN=CENTER>id_composant</TD><TD ALIGN=CENTER>type</TD><TD ALIGN=CENTER>module_linux</TD><TD ALIGN=CENTER>type_module</TD><TD ALIGN=CENTER>nom</TD><TD>used</TD><TD>status</TD></TR>\n";
}

for ($i=0;$i<count($ligne);$i++) {
	if (ereg("\t",$ligne[$i])) {	
		list ($vide,$id_composant,$type,$module_linux,$nom) = split ("\t", $ligne[$i]);
		if ($nom!="") { 
			$total_entries++;
			$nom=substr($nom,0,strlen($nom)-1);
			if ($affichage!="aucun") $entree="<TR>\n";
			$type_module="kernel";
			if ($module_linux!="unknown") {
				if (ereg("Server:XFree86\(.*\)",$module_linux)) {
					$type_module="xfree";
					$module_linux=ereg_replace("Server:XFree86\((.*)\)","\\1",$module_linux);
				}
				if (ereg("Server:XF86_.*",$module_linux)) {
					$type_module="xfree";
					$module_linux="vga";
				}
			} else $module_linux="inconnu";
			if ($types[$type]!="inconnu" && $types[$type]!="carte video") $type_module="kernel";
			if ($types[$type]=="carte video") $type_module="xfree";
			$conflict=false;
			$update=false;
			$request="SELECT COUNT(nom_dns) AS total,type,module_linux,type_module,nom FROM composants AS a LEFT JOIN composant_est_installe_sur AS b ON a.id_composant=b.id_composant WHERE a.id_composant=\"".substr($id_composant,0,4).":".substr($id_composant,4,4)."\" GROUP BY a.id_composant"; 
			$result=mysql_query($request);
			if (mysql_num_rows($result)>0) {
				if ($affichage!="aucun") $entree.=sprintf("<TD ALIGN=CENTER>%s:%s</TD>",substr($id_composant,0,4),substr($id_composant,4,4));
				if ($operation=="maj") $request_update="UPDATE composants SET ";
		  		$line=mysql_fetch_array($result);
				$update_type=false;
				if ($types[$type]=="inconnu" || $line["type"]==$types[$type]) { 
					if ($affichage!="aucun") $entree.=sprintf("<TD ALIGN=CENTER>%s</TD>",$line["type"]); 				
				} else if ($line["type"]=="inconnu" || $ignore_type) { 
						if ($affichage!="aucun") $entree.=sprintf("<TD ALIGN=CENTER BGCOLOR=green>%s</TD>",$types[$type]);
						$updated_fields++;
						$update_type=true;
						$update=true;
						if ($operation=="maj") $request_update.=sprintf("type='%s',",$types[$type]);
				} else { 
						if ($affichage!="aucun") $entree.=sprintf("<TD ALIGN=CENTER BGCOLOR=red>%s => %s</TD>",$line["type"],$types[$type]);
						$conflictual_fields++;
						$conflict=true;
				}
				$update_module=false;
				if ($module_linux=="inconnu" || $line["module_linux"]==$module_linux || ereg($module_ignore_filter,$line["module_linux"])) { 
					if ($affichage!="aucun") $entree.=sprintf("<TD ALIGN=CENTER>%s</TD>",$line["module_linux"]); 				
				} else if ($line["module_linux"]=="inconnu" || ereg($module_update_filter,$line["module_linux"])) { 
						if ($affichage!="aucun") $entree.=sprintf("<TD ALIGN=CENTER BGCOLOR=green>%s</TD>",$module_linux);
						$updated_fields++;
						$update_module=true;
						$update=true;
						if ($operation=="maj") $request_update.=sprintf("module_linux='%s',",$module_linux);
				} else { 
						if ($affichage!="aucun") $entree.=sprintf("<TD ALIGN=CENTER BGCOLOR=red>%s => %s</TD>",$line["module_linux"],$module_linux);
						$conflictual_fields++;
						$conflict=true;
				}	
				if ($line["type_module"]!=$type_module && $module_linux!="inconnu" && !ereg($module_ignore_filter,$line["module_linux"])) {
					if ($update_module || $update_type) {
						if ($affichage!="aucun") $entree.=sprintf("<TD ALIGN=CENTER BGCOLOR=green>%s</TD>",$type_module);
						$update=true;
						if ($operation=="maj") $request_update.=sprintf("type_module='%s',",$type_module);
					} else {
						if ($affichage!="aucun") $entree.=sprintf("<TD ALIGN=CENTER BGCOLOR=red>%s => %s</TD>",$line["type_module"],$type_module);
						$conflictual_fields++;
						$conflict=true;
					}
				} else {
					if ($affichage!="aucun") $entree.=sprintf("<TD ALIGN=CENTER>%s</TD>",$line["type_module"]);
				}
				if ($line["nom"]=="inconnu" || ($line["nom"]!=$nom && $ignore_name)) {
					if ($affichage!="aucun") $entree.=sprintf("<TD ALIGN=CENTER BGCOLOR=green>%s</TD>",$nom);
					$update=true;
					if ($operation=="maj") $request_update.=sprintf("nom='%s',",addslashes($nom));
				} else if ($line["nom"]!=$nom && !$ignore_name) {
					if ($affichage!="aucun") $entree.=sprintf("<TD ALIGN=CENTER BGCOLOR=red>%s => %s</TD>",$line["nom"],$nom);
					$conflictual_fields++;
					$conflict=true;
				} else {
					if ($affichage!="aucun") $entree.=sprintf("<TD ALIGN=CENTER>%s</TD>",$line["nom"]);
				}
			if ($affichage!="aucun") $entree.=sprintf("<TD ALIGN=CENTER>%s</TD>\n",$line["total"]);
			if ($conflict) {
				$conflictual_entries++;
				if ($affichage!="aucun" && $affichage!="maj") $tableau.=$entree."<TD ALIGN=CENTER>C</TD></TR>";
			} else if ($update) {
				$updated_entries++;
				if ($affichage!="aucun" && $affichage!="conflit") $tableau.=$entree."<TD ALIGN=CENTER>U</TD></TR>";
				if ($operation=="maj") {
					$request_update=substr($request_update,0,strlen($request_update)-1).sprintf(" WHERE id_composant='%s:%s'",substr($id_composant,0,4),substr($id_composant,4,4));
					if (mysql_query($request_update)) $mysql_update++;
					else $mysql_update_failed++;
				}
			} else {
					$unchanged_entries++;
					if ($affichage=="complet") $tableau.=$entree."<TD ALIGN=CENTER>_</TD></TR>";
				}
			} else {
				if ($affichage!="aucun" && $affichage!="conflit") $tableau.=sprintf("<TR><TD ALIGN=CENTER BGCOLOR=blue>%s:%s</TD><TD ALIGN=CENTER>%s</TD><TD ALIGN=CENTER>%s</TD><TD ALIGN=CENTER>%s</TD><TD ALIGN=CENTER>%s</TD><TD ALIGN=CENTER>0</TD><TD ALIGN=CENTER>N</TD></TR>\n",substr($id_composant,0,4),substr($id_composant,4,4),$types[$type],$module_linux,$type_module,$nom);
				if ($operation=="maj") {
					$request_maj=sprintf("INSERT INTO composants(id_composant,type,module_linux,type_module,nom) VALUES ('%s:%s','%s','%s','%s','%s')",substr($id_composant,0,4),substr($id_composant,4,4),$types[$type],$module_linux,$type_module,addslashes($nom));
					if (mysql_query($request_maj)) $mysql_insert++;
					else $mysql_insert_failed++;
				}
				$new_entries++;
				}
			mysql_free_result($result);
		}
	}
}

$tableau.="</TABLE>\n";

print("<FORM NAME=form METHOD=POST>\n");
print("<TABLE>\n");
printf("<TR><TD><UL><LI>nombre d'entrées lues dans <A HREF=\"%s\">%s</A> : %s",$url,$url,$total_entries);

if ($operation!="maj") printf("<LI>nombre d'entrées identiques : %s<LI>nombre de nouvelles entrées : %s<LI>nombre d'entrées à actualiser sans conflit : %s<LI>nombre de champs à actualiser : %s<LI>nombre d'entrées à actualiser en conflit : %s<LI>nombre de champs à actualiser en conflit : %s</UL></TD></TR>\n",$unchanged_entries,$new_entries,$updated_entries,$updated_fields,$conflictual_entries,$conflictual_fields);
else printf("<LI>nombre de nouvelles entrées insérées : %s <LI>nombre d'entrées mises à jours : %s<LI>nombre de nouvelles entrées non insérées : %s <LI>nombre d'entrées non mises à jours : %s</UL></TD></TR>\n",$mysql_insert,$mysql_update,$mysql_insert_failed,$mysql_update_failed);
print("</TABLE>\n");
print("<TABLE>\n");
print("<TR><TD><INPUT TYPE=checkbox NAME=ignore_name");
if ($ignore_name) print(" CHECKED");
print("></TD><TD>Remplacer systématiquement le nom des composants&nbsp;</TD></TR>\n");
print("<TR><TD><INPUT TYPE=checkbox NAME=ignore_type");
if ($ignore_type) print(" CHECKED");
print("></TD><TD>Remplacer systématiquement le type des composants&nbsp;</TD></TR>\n");
printf("<TR><TD><INPUT TYPE=checkbox NAME=module_update_filter_radio onclick='document.form.module_update_filter.disabled=!document.form.module_update_filter_radio.checked' $module_update_filter_checked></TD><TD>Filtre pour les modules à remplacer systématiquement<TD>:</TD><TD><INPUT TYPE=TEXT SIZE=20 NAME=module_update_filter VALUE='%s' $module_update_filter_disabled></TD></TR>\n",$module_update_filter_disabled?"":$module_update_filter);
printf("<TR><TD><INPUT TYPE=checkbox NAME=module_ignore_filter_radio onclick='document.form.module_ignore_filter.disabled=!document.form.module_ignore_filter_radio.checked' $module_ignore_filter_checked></TD><TD>Filtre pour les modules à conserver systématiquement<TD>:</TD><TD><INPUT TYPE=TEXT SIZE=20 NAME=module_ignore_filter VALUE='%s' $module_ignore_filter_disabled></TD></TR>\n",$module_ignore_filter_disabled?"":$module_ignore_filter);
print("</TABLE>\n");
print("<TABLE>\n");
print("<TR><TD>AFFICHAGE</TD><TD>:</TD>\n");
printf("<TD><INPUT TYPE=radio NAME=affichage VALUE=aucun %s>aucun</TD>\n",($affichage=="aucun"?"CHECKED":""));
printf("<TD><INPUT TYPE=radio NAME=affichage VALUE=complet %s>complet</TD>\n",($affichage=="complet"?"CHECKED":""));
printf("<TD><INPUT TYPE=radio NAME=affichage VALUE=ignorer_identiques %s>ignorer identiques</TD>\n",($affichage=="ignorer_identiques"?"CHECKED":""));
printf("<TD><INPUT TYPE=radio NAME=affichage VALUE=maj %s>maj sans conflit</TD>\n",($affichage=="maj"?"CHECKED":""));
printf("<TD><INPUT TYPE=radio NAME=affichage VALUE=conflit %s>conflits</TD>\n",($affichage=="conflit"?"CHECKED":""));
print("</TR></TABLE>\n");
print("<TABLE>\n");
print("<TD><INPUT TYPE=button VALUE='SIMULER' onclick='document.form.operation.value=\"simuler\";document.form.submit()'></TD>\n");
print("<TD><INPUT TYPE=button VALUE='EFFECTUER LES MAJ' onclick='document.form.operation.value=\"maj\";document.form.submit()'></TD></TR>");
print("</TABLE>\n");
print("<INPUT TYPE=hidden NAME=operation>\n");
print("</FORM>\n");

print("<P><CENTER><A HREF=\"javascript:parent.location.href='accueil.php'\">Retour</A></CENTER></P>\n");

if ($affichage!="aucun") print($tableau);

print("</CENTER>\n");

DisconnectMySQL();

PiedPage();

?>
