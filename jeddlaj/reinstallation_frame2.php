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

if (isset($_GET["nom_dns"])) $nom_dns = $_GET["nom_dns"]; else $nom_dns="";
if (isset($_GET["nom_groupe"])) $nom_groupe = $_GET["nom_groupe"]; else $nom_groupe="";
if (isset($_GET["nom_dns_checked"])) $nom_dns_checked = $_GET["nom_dns_checked"];
if (isset($_GET["operation"])) $operation = $_GET["operation"];
if (isset($_GET["distributions"])) $distributions = $_GET["distributions"];

function statusOrdinateur($nom_dns,$mon_ip,$operation) {
	$requestlocal = "SELECT nom_logiciel,version,date_install,etat_idb,dd_a_partitionner FROM idb_est_installe_sur AS a, images_de_base AS b, logiciels AS c, stockages_de_masse AS d WHERE a.id_idb=b.id_idb AND id_os=id_logiciel AND a.nom_dns=\"$nom_dns\" AND a.nom_dns=d.nom_dns AND date_install!=\"0000-00-00 00:00:00\" GROUP BY nom_logiciel,version";
	$resultlocal= mysql_query($requestlocal);
  if (mysql_num_rows($resultlocal) < 1 ) {
		print("<TD><IMG SRC=\"ICONES/ordi_lock.png\"></TD><TD></TD><TD>$nom_dns</TD><TD></TD><TD>Aucune distribution installée</TD>\n");
		mysql_free_result($resultlocal);
		return 0;
	}
	$distributions="";
	$annulation=false;
	for ($i=0;$i<mysql_num_rows($resultlocal);$i++) {
	  $line=mysql_fetch_array($resultlocal);
		if ($line["etat_idb"]=="a_synchroniser" || $line["etat_idb"]=="a_ajouter") {
			if ($operation=="annuler")  $annulation=true;
				$distributions.="<b>".$line["nom_logiciel"]." ".$line["version"]."</b><br>";
		} else $distributions.=$line["nom_logiciel"]." ".$line["version"]."<br>";
	}
	if ($operation=="annuler" && !$annulation) {
		print("<TD><IMG SRC=\"ICONES/ordi_lock.png\"></TD><TD></TD><TD>$nom_dns</TD><TD>$distributions</TD><TD>Aucune opération à annuler</TD>\n");
		mysql_free_result($resultlocal);
		return 0;
	}
  $requestlocal = "SELECT etat_install FROM ordinateurs WHERE nom_dns=\"$nom_dns\" AND etat_install NOT IN (\"installe\",\"modifie\")"; 
  $resultlocal = mysql_query($requestlocal);
  if (mysql_num_rows($resultlocal) > 0 ) {
		$line=mysql_fetch_array($resultlocal);
		print("<TD><IMG SRC=\"ICONES/ordi_lock.png\"></TD><TD></TD><TD>$nom_dns</TD><TD>$distributions</TD><TD>Cet ordinateur est en état $line[etat_install]</TD>\n");
  	mysql_free_result($resultlocal);
		return 0;
	}		
  $requestlocal = "SELECT timestamp,ip_distante FROM ordinateurs_en_consultation WHERE nom_dns=\"$nom_dns\""; 
  $resultlocal = mysql_query($requestlocal);
  if (mysql_num_rows($resultlocal) > 0 ) {
 		$linelocal=mysql_fetch_array($resultlocal);
    $ip=$linelocal["ip_distante"];
  	mysql_free_result($resultlocal);
    print("<TD><IMG SRC=\"ICONES/ordi_lock.png\"></TD><TD></TD><TD>$nom_dns</TD><TD>$distributions</TD><TD>Cet ordinateur est en consultation depuis $ip </TD>\n");
		return 0;
	}
	$requestlocal="INSERT INTO ordinateurs_en_consultation (nom_dns,ip_distante,timestamp) VALUES(\"$nom_dns\",\"$mon_ip\",NOW())";
	mysql_query($requestlocal);
  print("<TD><IMG SRC=\"ICONES/ordi_ok.png\"></TD><TD ALIGN=CENTER><INPUT TYPE=\"checkbox\" NAME=\"nom_dns_checked[]\" VALUE=\"$nom_dns\" CHECKED></TD><TD>$nom_dns</TD><TD>$distributions</TD><TD> OK </TD>");
	return 1;
}

entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Reinstallation");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<CENTER>\n");

$mon_ip=getenv('REMOTE_ADDR');

if (isset($_GET["validation"])) {
	$request="SELECT * FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\" AND NOW()-timestamp<=500";
	$result=mysql_query($request);
	$expired=(mysql_num_rows($result)==0);
	mysql_free_result($result);
	if ($expired) {
  	print("La sélection a expiré.<BR>\n");
	} else {
		if (isset($nom_dns_checked)) {
			if ($operation=="repartitionner") { 
				for ($i=0;$i<count($nom_dns_checked);$i++) {
					$request="UPDATE stockages_de_masse SET dd_a_partitionner=\"oui\" WHERE nom_dns=\"$nom_dns_checked[$i]\"";	
					mysql_query($request);
					$request="UPDATE idb_est_installe_sur SET etat_idb=\"a_ajouter\" WHERE nom_dns=\"$nom_dns_checked[$i]\"";	
					mysql_query($request);
					$request="UPDATE ordinateurs SET etat_install=\"modifie\" WHERE nom_dns=\"$nom_dns_checked[$i]\"";
					mysql_query($request);
				}
				printf("<P><CENTER><I>Mofications insérées dans la base.<FONT COLOR=RED>ATTENTION :</FONT> Le repartitionnement et la réinstallation totale ne seront effectifs sur les ordinateurs concernés qu'après leur reboot...</I></CENTER></P>\n");
				}
			else
			if ($operation=="annuler") {
				for ($i=0;$i<count($nom_dns_checked);$i++) {
					$request="UPDATE stockages_de_masse SET dd_a_partitionner=\"non\" WHERE nom_dns=\"$nom_dns_checked[$i]\"";	
					mysql_query($request);
					$request="UPDATE idb_est_installe_sur SET etat_idb=\"installe\" WHERE nom_dns=\"$nom_dns_checked[$i]\" AND date_install!=\"0000-00-00 00:00:00\" AND (etat_idb=\"a_synchroniser\" OR etat_idb=\"a_ajouter\")";	
					mysql_query($request);
					$request="SELECT DISTINCT a.num_disque,a.num_partition FROM idb_est_installe_sur AS a, package_est_installe_sur AS b WHERE a.nom_dns=\"$nom_dns_checked[$i]\" AND a.nom_dns=b.nom_dns AND etat_idb=\"installe\" AND (etat_package=\"a_ajouter\" OR etat_package=\"a_supprimer\") AND a.num_disque=b.num_disque AND a.num_partition=b.num_partition";	
					$result=mysql_query($request);
					for ($j=0;$j<mysql_num_rows($result);$j++) {
						$line=mysql_fetch_array($result);
						$request2="UPDATE idb_est_installe_sur SET etat_idb=\"modif_softs\" WHERE nom_dns=\"$nom_dns_checked[$i]\" AND num_disque=$line[num_disque] AND num_partition=$line[num_partition]";
						mysql_query($request2);
					}
					mysql_free_result($result);
					$request="SELECT id_idb FROM idb_est_installe_sur WHERE nom_dns=\"$nom_dns_checked[$i]\" AND etat_idb!=\"installe\"";
					$result=mysql_query($request);
					if (mysql_num_rows($result)==0) {
						$request2="UPDATE ordinateurs SET etat_install=\"installe\" WHERE nom_dns=\"$nom_dns_checked[$i]\"";
          	mysql_query($request2);
					}
					mysql_free_result($result);
					}
				printf("<P><CENTER><I>Mofications insérées dans la base. Les opérations ont été annulées sur les ordinateurs concernés qu'après leur reboot...</I></CENTER></P>\n");
				}
			else
			if (isset($distributions)) {
				for ($j=0;$j<count($distributions);$j++) {
					for ($i=0;$i<count($nom_dns_checked);$i++) {
						$request="SELECT num_disque,num_partition FROM idb_est_installe_sur AS a, images_de_base AS b WHERE nom_dns=\"$nom_dns_checked[$i]\" AND a.id_idb=b.id_idb AND id_os=\"$distributions[$j]\"";
						$result=mysql_query($request);
						for ($k=0;$k<mysql_num_rows($result);$k++) {
							$line=mysql_fetch_array($result);
							$request2="UPDATE idb_est_installe_sur SET etat_idb=\"$operation\" WHERE nom_dns=\"$nom_dns_checked[$i]\" AND num_disque=\"$line[num_disque]\" AND num_partition=\"$line[num_partition]\"";	
							mysql_query($request2);
							$request2="UPDATE ordinateurs SET etat_install=\"modifie\" WHERE nom_dns=\"$nom_dns_checked[$i]\"";
							mysql_query($request2);
						}
						mysql_free_result($result);
					}
				}
				printf("<P><CENTER><I>Mofications insérées dans la base. <FONT COLOR=RED>ATTENTION :</FONT> La %s ne sera effective sur les ordinateurs concernés qu'après leur reboot...</I></CENTER></P>\n",($operation=="a_synchroniser"?"synchronisation":"réinstallation"));
			} else print("<P><CENTER>Aucune distribuiton sélectionnée</CENTER></P>\n");
		} else print("<P><CENTER>Aucun ordinateur sélectionné</CENTER></P>\n");
		$request = "DELETE FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\""; 
		mysql_query($request);
	}
} else if ($nom_groupe.$nom_dns!="") {
	$request = "DELETE FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\" OR NOW()-timestamp>500";
	mysql_query($request);
	$ok=0;
	print("<FORM NAME=\"form\">\n");
	print("<TABLE>");
	print("<TR><TD></TD><TD ALIGN=\"center\"  BGCOLOR=\"#CC00AA\"><b>Sélection</b></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Nom DNS</b></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Distribution(s)</b></TD><TD ALIGN=\"left\"  BGCOLOR=\"#CC00AA\"><b>Status</b></TD></TR>\n");
	if ($nom_groupe!="") {
		$request = "SELECT nom_dns FROM ord_appartient_a_gpe WHERE nom_groupe=\"$nom_groupe\" ORDER BY nom_dns";
		$result = mysql_query($request);
	  for ($i=0;$i<mysql_num_rows($result);$i++) {
	    $line=mysql_fetch_array($result);
	    print("<TR>\n");
			$ok+=statusOrdinateur($line["nom_dns"],$mon_ip,$operation);
			print("</TR>\n");
	  }
		mysql_free_result($result);
	} else {
		print("<TR>\n");
		$ok+=statusOrdinateur($nom_dns,$mon_ip,$operation);
		print("</TR>\n");
	}
	print("</TABLE>\n");
	if ($operation=="a_ajouter" || $operation=="a_synchroniser") {
		if ($nom_groupe!="") 
			$request = "SELECT nom_logiciel,version,id_os FROM idb_est_installe_sur AS a, images_de_base AS b, logiciels AS c, ord_appartient_a_gpe AS d WHERE a.id_idb=b.id_idb AND id_os=id_logiciel AND nom_groupe=\"$nom_groupe\" AND d.nom_dns=a.nom_dns AND date_install!=\"0000-00-00 00:00:00\" GROUP BY nom_logiciel,version";
		else 
			$request = "SELECT nom_logiciel,version,id_os FROM idb_est_installe_sur AS a, images_de_base AS b, logiciels AS c WHERE a.id_idb=b.id_idb AND id_os=id_logiciel AND nom_dns=\"$nom_dns\" AND date_install!=\"0000-00-00 00:00:00\" GROUP BY nom_logiciel,version";
		$result = mysql_query($request);
		if (mysql_num_rows($result)>0) {
			print("<BR><TABLE><TR>\n");
			print("<TD>Distribution(s) :</TD>");
			 for ($i=0;$i<mysql_num_rows($result);$i++) {
			   $line=mysql_fetch_array($result);
				print("<TD><INPUT TYPE=checkbox name=distributions[] CHECKED VALUE=$line[id_os]>$line[nom_logiciel] $line[version]</TD>");
			}
			mysql_free_result($result);
			print("</TR></TABLE>\n");
		}
	}
	print("<BR><TABLE><TR>\n");
  	print("<TD><INPUT TYPE=\"button\" VALUE=\"ACTUALISER\" onClick=\"javascript:location.reload()\"></TD>\n");
	if ($ok>0) { 
		if ($ok > 1) {
			print("<TD><INPUT TYPE=button VALUE=\"INVERSER SELECTION\" onClick=\"javascript:for (i=0;typeof(document.form['nom_dns_checked[]'][i])!='undefined';i++) document.form['nom_dns_checked[]'][i].checked=!document.form['nom_dns_checked[]'][i].checked\"></TD>\n");
			print("<TD><INPUT TYPE=button VALUE=\"TOUT SELECTIONNER\" onClick=\"javascript:for (i=0;typeof(document.form['nom_dns_checked[]'][i])!='undefined';i++) document.form['nom_dns_checked[]'][i].checked=true\"></TD>\n");
		}
		print("<TD><INPUT TYPE=\"submit\" VALUE=\"VALIDER\"></TD>\n");
		print("<TD><INPUT TYPE=\"hidden\" NAME=\"operation\" VALUE=\"$operation\"></TD>\n");
		print("<TD><INPUT TYPE=\"hidden\" NAME=\"validation\"></TD>\n");
	}
	print("</TR></TABLE>\n");
	print("</FORM>\n");
}
print("</CENTER>\n");

DisconnectMySQL();


PiedPage();

?>
