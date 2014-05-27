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

$nom_groupe = $_GET["nom_groupe"];
$nom_dns = $_GET["nom_dns"];
$id_os = $_GET["id_os"];

include("UtilsHTML.php");
include("UtilsMySQL.php");

function typePartitionQuery($nom_os) {
  switch($nom_os) {
    case "Windows95" : 
      return "type_partition=\"FAT32\"";
      break;
    case "Windows98" : 
      return "type_partition=\"FAT32\"";
      break;
    case "WindowsME" : 
      return "type_partition=\"FAT32\"";
      break;
    case "WindowsNT" :
      return "type_partition=\"ntfs\"";
      break;
    case "Windows2000" :
      return "type_partition=\"ntfs\"";
      break;
    case "WindowsXP" :
      return "type_partition=\"ntfs\"";
      break;
    case "Windows2003" :
      return "type_partition=\"ntfs\"";
      break;
    case "WindowsVista" :
      return "type_partition=\"ntfs\"";
      break;
    case "Windows7" :
      return "type_partition=\"ntfs\"";
      break;
    case "Windows7_x64" :
      return "type_partition=\"ntfs\"";
      break;
    case "Windows8" :
      return "type_partition=\"ntfs\"";
      break;
    case "Windows8_x64" :
      return "type_partition=\"ntfs\"";
      break;
   case "Linux" :
      return "(nom_partition=\"/\" AND (type_partition=\"ext2\" OR type_partition=\"ext3\" OR type_partition=\"ext4\"))";
      break;
   case "Linux_x64" :
      return "(nom_partition=\"/\" AND (type_partition=\"ext2\" OR type_partition=\"ext3\" OR type_partition=\"ext4\"))";
      break;
  }
}

function statusOrdinateur($id_os,$nom_os,$version,$nom_dns,$mon_ip) {
  global $indice;
  $requestlocal = "SELECT nom_idb FROM logiciels ,images_de_base ,ordinateurs WHERE nom_dns=\"$nom_dns\" AND id_logiciel=\"$id_os\" AND id_os=id_logiciel AND ( specificite=\"aucune\" OR ( specificite=\"signature\" AND valeur_specificite=signature) OR (specificite=\"nom_dns\" AND valeur_specificite=nom_dns))";
  $resultlocal = mysql_query($requestlocal);
  if (mysql_num_rows($resultlocal) < 1 ) { 
    mysql_free_result($resultlocal);
    return "<TD><IMG SRC=\"ICONES/ordi_lock.png\" BORDER=\"0\"></A></TD><TD></TD><TD>$nom_dns</TD><TD></TD><TD>Pas d'image de $nom_os $version pour cet ordinateur</TD>\n";
  }
  $requestlocal = "SELECT etat_install FROM ordinateurs WHERE nom_dns=\"$nom_dns\" AND etat_install NOT IN (\"installe\",\"modifie\")"; 
  $resultlocal = mysql_query($requestlocal);
  if (mysql_num_rows($resultlocal) > 0 ) {
 		$linelocal=mysql_fetch_array($resultlocal);
		$etat=$linelocal["etat_install"];
  	mysql_free_result($resultlocal);
		return "<TD><IMG SRC=\"ICONES/ordi_lock.png\"></TD><TD></TD><TD>$nom_dns</TD><TD></TD><TD>Cet ordinateur est en état $etat</TD>\n";
	}
  $requestlocal = "SELECT timestamp,ip_distante FROM ordinateurs_en_consultation WHERE nom_dns=\"$nom_dns\""; 
  $resultlocal = mysql_query($requestlocal);
  if (mysql_num_rows($resultlocal) > 0 ) {
 		$linelocal=mysql_fetch_array($resultlocal);
    $ip=$linelocal["ip_distante"];
  	mysql_free_result($resultlocal);
    return "<TD><IMG SRC=\"ICONES/ordi_lock.png\"></TD><TD></TD><TD>$nom_dns</TD><TD></TD><TD>Cet ordinateur est en consultation depuis $ip </TD>\n";
	}
  $requestlocal = "SELECT num_disque,num_partition,linux_device,nom_partition FROM partitions WHERE nom_dns=\"$nom_dns\" AND systeme=\"oui\" AND ".typePartitionQuery($nom_os); 
  $resultlocal = mysql_query($requestlocal);
  if (mysql_num_rows($resultlocal) < 1 )  {
  	mysql_free_result($resultlocal);
		return "<TD><A HREF=\"modifier_machine.php?nom_dns=$nom_dns\" TARGET=\"new\"><IMG SRC=\"ICONES/ordi_confless.png\" BORDER=\"0\"></A></TD><TD></TD><TD>$nom_dns</TD><TD></TD><TD>Pas de partition adéquate sur cet ordinateur</TD>\n";
  } else {
    $select_os="";
    $select_non_os="";
    for ($i=0;$i<mysql_num_rows($resultlocal);$i++) {
    	$linelocal=mysql_fetch_array($resultlocal);
    	$requestlocal1 = "SELECT id_os,date_install,date_creation FROM idb_est_installe_sur AS a, images_de_base AS b  WHERE nom_dns=\"$nom_dns\" AND num_disque=\"".$linelocal["num_disque"]."\" AND num_partition=\"".$linelocal["num_partition"]."\" AND a.id_idb=b.id_idb";
    	$resultlocal1=mysql_query($requestlocal1);
    	$linelocal1=mysql_fetch_array($resultlocal1);
    	if (mysql_num_rows($resultlocal1) > 0 ) {
		if ($linelocal1["id_os"]==$id_os) { 
			if ($linelocal1["date_creation"]>$linelocal1["date_install"]) $ood="style=\"color:red;\"";
			else $ood="*";
			$select_os.="<OPTION $ood VALUE=\"".$linelocal["num_disque"].":".$linelocal["num_partition"]."\">*disk://".$linelocal["num_disque"].":".$linelocal["num_partition"]." ".$linelocal["nom_partition"]."</OPTION>";
		}
	} else  $select_non_os.="<OPTION VALUE=\"".$linelocal["num_disque"].":".$linelocal["num_partition"]."\">&nbsp;disk://".$linelocal["num_disque"].":".$linelocal["num_partition"]." ".$linelocal["nom_partition"]."</OPTION>";
    }
    mysql_free_result($resultlocal1);
		if ( $select_os!="" || $select_non_os!="" ) $select="<TD><SELECT NAME=\"partition[$indice]\">".$select_os.$select_non_os."</SELECT></TD>\n";
		else {
  		mysql_free_result($resultlocal);
			return "<TD><A HREF=\"modifier_machine.php?nom_dns=$nom_dns\" TARGET=\"new\"><IMG SRC=\"ICONES/ordi_confless.png\" BORDER=\"0\"></A></TD><TD></TD><TD>$nom_dns</TD><TD></TD><TD>Pas de partition adéquate libre sur cet ordinateur</TD>\n";
		}
  }
	$requestlocal="INSERT INTO ordinateurs_en_consultation (nom_dns,ip_distante,timestamp) VALUES(\"$nom_dns\",\"$mon_ip\",NOW())";
	mysql_query($requestlocal);
  return "<TD><IMG SRC=\"ICONES/ordi_ok.png\"></TD><TD ALIGN=CENTER><INPUT TYPE=\"checkbox\" NAME=\"check[".$indice++."]\" VALUE=\"$nom_dns\" CHECKED></TD><TD>$nom_dns</TD>".$select."<TD> OK </TD>";
}

entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Configuration Logicielle - Etape 1");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<CENTER><H1>Configuration Logicielle - Etape 1</H1></CENTER>\n");

$request = "SELECT nom_dns AS total FROM ord_appartient_a_gpe WHERE nom_groupe=\"$nom_groupe\" GROUP BY nom_dns";
$result = mysql_query($request);

if (mysql_num_rows($result)>0 || $nom_dns!="" ) {

  print("<CENTER>\n");
  print("<FORM METHOD=\"POST\" NAME=\"form\" ACTION=\"configuration_logicielle_2.php\">\n");
	
  $mon_ip=getenv('REMOTE_ADDR');
  $request = "DELETE FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\" OR NOW()-timestamp>500";
  mysql_query($request);
  
	if ($nom_groupe!="") {
    $request = "SELECT photo FROM groupes WHERE nom_groupe=\"$nom_groupe\"";
    $result = mysql_query($request);
    $line = mysql_fetch_array($result);
    $photo = $line["photo"];
    mysql_free_result($result);
	} else $photo="ordinateur.jpg";
  
  $request = "SELECT nom_os,nom_logiciel,version,icone FROM logiciels WHERE id_logiciel=\"$id_os\"";
  $result = mysql_query($request);
  $line = mysql_fetch_array($result);
  $nom_os=$line["nom_os"];
  $nom_logiciel=$line["nom_logiciel"];
  $version=$line["version"];
  $icone = $line["icone"];
  mysql_free_result($result);
  
  print("<TABLE>\n");
  print("<TR><TD><IMG SRC=\"PHOTOS/$photo\" WIDTH=\"200\" HEIGHT=\"120\"></TD>\n");
  if ($nom_groupe!="" ) print("<TD><TABLE><TR><TD COLSPAN=\"2\"><b>Groupe : </b> $nom_groupe</TD>");
  else print("<TD><TABLE><TR><TD COLSPAN=\"2\"><b>Ordinateur : </b> $nom_dns</TD>");
	print("</TR><TR><TD><IMG SRC=\"ICONES/$icone\" WIDTH=\"100\" HEIGHT=\"100\"></TD>");
  print("<TD ALIGN=\"left\"><TABLE><TR><TD><b>OS : </b> $nom_os</TD><TR><TD><b>Distribution : </b> $nom_logiciel $version</TD></TR></TABLE></TD>\n");
  print("</TR></TABLE>\n");
  print("</TR></TABLE>\n");
  
  $request = "SELECT nom_dns FROM ord_appartient_a_gpe WHERE nom_groupe=\"$nom_groupe\" ORDER BY nom_dns";
  $result = mysql_query($request);
  
  print("<TABLE>");
  print("<TR><TD></TD><TD ALIGN=\"center\"  BGCOLOR=\"#CC00AA\"><b>Sélection</b></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Nom DNS</b></TD><TD ALIGN=\"center\"  BGCOLOR=\"#CC00AA\"><b>Partitions</b></TD><TD ALIGN=\"left\"  BGCOLOR=\"#CC00AA\"><b>Status</b></TD></TR>\n");
	$indice=0;
	$i=0;
	if ($nom_groupe!="") {
    for (;$i<mysql_num_rows($result);$i++) {
      $line=mysql_fetch_array($result);
      print("<TR>\n".statusOrdinateur($id_os,$nom_os,$version,$line["nom_dns"],$mon_ip)."</TR>\n");
    }
	} else print("<TR>\n".statusOrdinateur($id_os,$nom_os,$version,$nom_dns,$mon_ip)."</TR>\n");
  print("</TABLE>\n");
  print("<INPUT TYPE=\"hidden\" NAME=\"nom_groupe\" VALUE=\"$nom_groupe\">\n");
  print("<INPUT TYPE=\"hidden\" NAME=\"id_os\" VALUE=\"$id_os\">\n");
  print("<INPUT TYPE=\"hidden\" NAME=\"nb_ordinateurs\" VALUE=\"$i\">\n");
  print("<INPUT TYPE=\"hidden\" NAME=\"nb_check\" VALUE=\"$indice\">\n");
  print("<INPUT TYPE=\"hidden\" NAME=\"photo\" VALUE=\"$photo\">\n");
	print("<BR>");
	print("<TABLE><TR>\n");
  print("<TD><INPUT TYPE=\"button\" VALUE=\"ACTUALISER\" onClick=\"javascript:location.reload()\"></TD>\n");
	if ($indice > 1) {
			print("<TD><INPUT TYPE=button VALUE=\"INVERSER SELECTION\" onClick=\"for (i=0;i<document.form.length-4;i+=2) document.form[i].checked=!document.form[i].checked\"></TD>\n");
			print("<TD><INPUT TYPE=button VALUE=\"TOUT SELECTIONNER\" onClick=\"for (i=0;i<document.form.length-4;i+=2) document.form[i].checked=true\"></TD>\n");
	}
	if ($indice > 0) print("<TD><INPUT TYPE=\"submit\" VALUE=\"VALIDER LA SELECTION\"></TD>\n");
	print("</TR></TABLE>\n");
  print("</FORM>\n</CENTER>\n");
}
else print("<P><CENTER>La sélection est vide.</CENTER></P>\n");

mysql_free_result($result);

DisconnectMySQL();

print("<BR><HR><P><CENTER><A HREF=accueil.php>Retour</A></CENTER></P>\n");

PiedPage();

?>
