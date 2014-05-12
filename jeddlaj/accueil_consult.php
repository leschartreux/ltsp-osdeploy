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

# Main()
entete("G�rard Milhaud & Fr�d�ric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : accueil mode consultation");
include ("DBParDefaut.consult.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<CENTER>");
print("<TABLE BORDER=0 WIDTH=100%>");
print("<TR ALIGN=CENTER>");
print("<TD ALIGN=LEFT><A HREF=\"http://la.firme.perso.esil.univmed.fr\" TARGET=\"LaFIRME\" ><IMG ALIGN=CENTER BORDER=0 SRC=\"LOGOS/CopyleftLaFirmeWhiteBack.png\" ALT=\"CopyLeft La FIRME@ESIL\"></TD>");
print("<TD ALIGN=CENTER><IMG ALIGN=CENTER SRC=\"LOGOS/JeDDLaJ_current.png\" ALT=\"JeDDLaJ's Logo\"></TD>");
print("<TD ALIGN=RIGHT><A HREF=\"http://la.firme.perso.esil.univmed.fr/website/rubrique.php3?id_rubrique=11\" TARGET=\"LaDoc\" ><IMG ALIGN=CENTER BORDER=0 SRC=\"LOGOS/UnPeuDeDocWhiteBack.png\" ALT=\"De la doc...\"></TD>");
print("</TR>");
print("</TABLE>");
print("</CENTER>");
print("<CENTER><H1>Accueil mode consultation</H1></CENTER>\n");

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
			$ligne_info.="\">$line[total] ordinateur$s install�$s";
			break;
		case "modifie":
			if ($line["total"]==1) $ligne_info.="examine_machine.php?nom_dns=$line[nom_dns]";
			else $ligne_info.="javascript:document.form.request.value='SELECT * FROM ordinateurs WHERE etat_install=\'$line[etat_install]\'';document.form.submit()";
			$ligne_info.="\">$line[total] ordinateur$s modifi�$s";
			break;
		case "en_cours":
			if ($line["total"]==1) $ligne_info.="examine_machine.php?nom_dns=$line[nom_dns]";
			else $ligne_info.="javascript:document.form.request.value='SELECT * FROM ordinateurs WHERE etat_install=\'$line[etat_install]\'';document.form.submit()";
			$ligne_info.="\">$line[total] ordinateur$s en cours d'installation";
			break;
		case "package":
			if ($line["total"]==1) $ligne_info.="examine_machine.php?nom_dns=$line[nom_dns]";
			else $ligne_info.="javascript:document.form.request.value='SELECT * FROM ordinateurs WHERE etat_install=\'$line[etat_install]\'';document.form.submit()";
			$ligne_info.="\">$line[total] ordinateur$s en cr�ation de packages";
			break;
		case "idb":
			if ($line["total"]==1) $ligne_info.="examine_machine.php?nom_dns=$line[nom_dns]";
			else $ligne_info.="javascript:document.form.request.value='SELECT * FROM ordinateurs WHERE etat_install=\'$line[etat_install]\'';document.form.submit()";
			$ligne_info.="\">$line[total] ordinateur$s en cr�ation d'images de base";
			break;
		case "depannage":
			if ($line["total"]==1) $ligne_info.="examine_machine.php?nom_dns=$line[nom_dns]";
			else $ligne_info.="javascript:document.form.request.value='SELECT * FROM ordinateurs WHERE etat_install=\'$line[etat_install]\'';document.form.submit()";
			$ligne_info.="\">$line[total] ordinateur$s en �tat d�pannage";
			break;
	}
	$ligne_info.="</A>";
}
mysql_free_result($result);
$request="SELECT COUNT(a.nom_dns) AS total FROM ordinateurs AS a LEFT JOIN partitions AS b ON a.nom_dns=b.nom_dns WHERE num_disque IS NULL";
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
mysql_free_result($result);
			
############ Menu g�n�ral

# On attaque la tables Ordinateurs pour les infos g�n�rales

print("<H2><A HREF=\"examine_machine.php\">Examiner</A> machine</H2>\n");
print("<H2><A HREF=\"examine_groupe.php\">Examiner</A> groupe</H2>\n");
print("<H2><A HREF=\"examine_logiciel.php\">Examiner</A> logiciel</H2>\n");
print("<H2><A HREF=\"examine_distribution.php\">Examiner</A> distribution</H2>\n");
print("<H2><A HREF=\"Interro.php\">Consulter la base JeDDLaJ</A></H2>\n");

PiedPage();
//FIN Main()

?>
