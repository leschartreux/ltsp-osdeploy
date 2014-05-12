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

function statusOrdinateur($nom_dns,$num_disque,$num_partition,$erreur,$intervention_en_cours,$nom_script,$mon_ip) {
  global $ok;
  $requestlocal = "SELECT timestamp,ip_distante FROM ordinateurs_en_consultation WHERE nom_dns=\"$nom_dns\""; 
  $resultlocal = mysql_query($requestlocal);
	$checkbox="";
  if (mysql_num_rows($resultlocal) > 0 ) $icone="ordi_lock.png";
	else 
		if ($intervention_en_cours=="OUI") $icone="ordi_confless.png"; 
		else {
			$ok++;
			$icone="ordi_ok.png";
			$checkbox="<INPUT TYPE=\"checkbox\" NAME=\"nom_dns[]\" VALUE=\"$nom_dns\">";
			$requestlocal="INSERT INTO ordinateurs_en_consultation (nom_dns,ip_distante,timestamp) VALUES(\"$nom_dns\",\"$mon_ip\",NOW())";
			mysql_query($requestlocal);
		}
 	mysql_free_result($resultlocal);
  $requestlocal = "SELECT etat_idb,nom_logiciel,version FROM idb_est_installe_sur AS a, images_de_base AS b, logiciels AS c WHERE a.id_idb=b.id_idb AND b.id_os=id_logiciel AND nom_dns=\"$nom_dns\" AND num_disque=\"$num_disque\" AND num_partition=\"$num_partition\""; 
  $resultlocal = mysql_query($requestlocal);
	$linelocal=mysql_fetch_array($resultlocal);
	$erreur="<B><I>$erreur</I></B> ";
	switch($linelocal["etat_idb"]) {
		case "a_synchroniser" :
			$erreur.="durant la synchronisation de la distribution  $linelocal[nom_logiciel] $linelocal[version]";
			break;
		case "a_ajouter" :
			$erreur.="durant l'installation de la distribution $linelocal[nom_logiciel] $linelocal[version]";
			break;
		case "modif_softs" :
			$requestlocal2 = "SELECT a.id_package,etat_package,nom_logiciel,version FROM package_est_installe_sur AS a, packages AS b, logiciels AS c WHERE a.id_package=b.id_package AND b.id_logiciel=c.id_logiciel AND etat_package!=\"installe\"  AND nom_dns=\"$nom_dns\" AND num_disque=\"$num_disque\" AND num_partition=\"$num_partition\" ORDER BY priorite,a.id_package LIMIT 1";
  		$resultlocal2 = mysql_query($requestlocal2);
			$linelocal2=mysql_fetch_array($resultlocal2);
			$erreur.="durant ".($linelocal2["etat_package"]=="a_ajouter"?"l'installation":"la suppression")." du logiciel $linelocal2[nom_logiciel] $linelocal2[version]";
 			mysql_free_result($resultlocal2);
			break;	
		}
	$erreur.=" sur disk://$num_disque:$num_partition.";
	if ($intervention_en_cours=="OUI") $traitement="Script <b>$nom_script.rbx</b> en cours d'éxécution.\n";
	else 
		if ($nom_script!="") $traitement="Script <b>$nom_script.rbx</b> en attente d'éxécution.\n";
		else $traitement="Aucun";
 	mysql_free_result($resultlocal);
  return "<TD><IMG SRC=\"ICONES/$icone\"></TD><TD ALIGN=\"center\">$checkbox</TD><TD>$nom_dns</TD><TD>".$erreur."</TD><TD ALIGN=\"center\">$traitement</TD>";
}

entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Dépannage");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

print("<CENTER><H1>Dépannage</H1></CENTER>\n");

$request="DELETE depannage FROM depannage,ordinateurs AS b WHERE depannage.nom_dns=b.nom_dns AND etat_install!=\"depannage\"";
$result = mysql_query($request);

$request = "SELECT * FROM depannage ORDER BY nom_dns";
$result = mysql_query($request);

if (mysql_num_rows($result)>0) {

  print("<CENTER>\n");
  print("<FORM METHOD=\"POST\" NAME=\"form\" ACTION=\"depannage_2.php\">\n");
	
  $mon_ip=getenv('REMOTE_ADDR');
  $request = "DELETE FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\" OR NOW()-timestamp>500";
  mysql_query($request);
  
  print("<TABLE>");
  print("<TR><TD></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Sélection</b></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Nom DNS</b></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Erreur</b></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Traitement</TR>\n");
  for ($i=0,$ok=0;$i<mysql_num_rows($result);$i++) {
  	$line=mysql_fetch_array($result);
      print("<TR>".statusOrdinateur($line["nom_dns"],$line["num_disque"],$line["num_partition"],$line["erreur"],$line["intervention_en_cours"],$line["nom_script"],$mon_ip)."</TR>\n");
  }
  print("</TABLE>\n");
  print("<BR>\n");
  print("<TABLE>\n");
  print("<TR><TD></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Action</b></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Exemples de cas d'utilisation</b></TD></TR>\n");
	print("<TR><TD><INPUT TYPE=\"radio\" NAME=\"action\" VALUE=\"en_cours\" CHECKED></TD><TD>Reprendre le cours de l'installation</TD><TD><I>Si l'erreur est due à un mauvais script de post-installation et que celui-ci a été corrigé par vos soins.</I><TD></TD<TR>\n");
	print("<TR><TD><INPUT TYPE=\"radio\" NAME=\"action\" VALUE=\"modifie\"></TD><TD>Sortir de l'état dépannage</TD><TD><I>Si vous devez faire des mofications sur la machine pour que l'installation puisse se faire correctement ( ex. plus assez de place sur le disque : modifiez la configuration logicielle et retirez tout logiciel inutile ).</I><TD></TD<TR>\n");
	print("<TR><TD><INPUT TYPE=\"radio\" NAME=\"action\" VALUE=\"a_ajouter\"></TD><TD>Lancer une réinstallation</TD><TD><I>Si l'erreur est survenue à la suite d'une demande de synchronisation.</I><TD></TD<TR>\n");
	print("<TR><TD><INPUT TYPE=\"radio\" NAME=\"action\" VALUE=\"a_synchroniser\"></TD><TD>Lancer une synchronisation</TD><TD><I>Si l'erreur est survenue à la suite d'une demande de désinstallation.</I><TD></TD<TR>\n");
	print("<TR><TD><INPUT TYPE=\"radio\" NAME=\"action\" VALUE=\"vide_cache\"></TD><TD>Vider la partition de cache et lancer une réinstallation sur la partition en erreur</TD><TD><I>En cas de soupçon de corruption de la partition de cache.</I><TD></TD<TR>\n");
	print("<TR><TD><INPUT TYPE=\"radio\" NAME=\"action\" VALUE=\"chkdsk_ntfs\"></TD><TD>Lancer un CHKDSK sur une partition NTFS en erreur et reprendre l'installation</TD><TD><I>Si l'erreur est liée à l'inconsistance d'un filesystem NTFS.</I><TD></TD<TR>\n");
	print("<TR><TD><INPUT TYPE=\"radio\" NAME=\"action\" VALUE=\"depannage\"></TD><TD>Intervernir sur le poste en mode administrateur</TD><TD><I>Quand tout ce qui est au-dessus a échoué et que vous vous êtes résigné à mettre les mains dans le cambouis</I>.<TD></TD<TR>\n");
	print("<TR><TD><INPUT TYPE=\"radio\" NAME=\"action\" VALUE=\"script\"></TD><TD>Lancer un script personnel<BR>Nom du script : <INPUT TYPE=TEXT SIZE=30 NAME=nom_script></TD><TD><I>Quand tout ce qui est au-dessus a échoué mais que vous ne voulez pas vous déplacer jusqu'à l'ordinateur.</I>.<TD></TD<TR>\n");
  print("</TABLE>\n");
	print("<BR>\n");
    print("<TABLE><TR>\n");
  if ($ok>1) {
    print("<TD><INPUT TYPE=button VALUE=\"TOUT SELECTIONNER\" onClick=\"javascript:for (i=0;typeof(document.form['nom_dns[]'][i])!='undefined';i++) document.form['nom_dns[]'][i].checked=true\"></TD>\n");
    print("<TD><INPUT TYPE=button VALUE=\"INVERSER SELECTION\" onClick=\"javascript:for (i=0;typeof(document.form['nom_dns[]'][i])!='undefined';i++) document.form['nom_dns[]'][i].checked=!document.form['nom_dns[]'][i].checked\"></TD>\n");
  }
  if ($ok>0) 
	print("<TD><INPUT TYPE=\"submit\" VALUE=\"VALIDER\"></TD>\n");
  print("<TD><INPUT TYPE=\"button\" VALUE=\"ACTUALISER\" onClick=\"javascript:location.reload()\"></TD>\n");
  print("</TR></TABLE>\n");
  print("</FORM>\n</CENTER>\n");
}
else print("<P><CENTER>Aucun ordinateur en état dépannage.</CENTER></P>\n");

mysql_free_result($result);

DisconnectMySQL();

print("<BR><HR><P><CENTER><A HREF=accueil.php>Retour</A></CENTER></P>\n");

PiedPage();

?>
