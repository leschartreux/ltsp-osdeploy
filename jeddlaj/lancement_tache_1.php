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


$nom_groupe = $_GET["nom_groupe"];
$nom_dns = $_GET["nom_dns"];
$type_tache = $_GET["id_typetache"];
if (isset( $_GET['link_speed'] ))
	$link_speed = $_GET['link_speed'];
else $link_speed = 100;

if ( isset ($_GET['cb_join'] ))
	$cb_join=1;
else
	$cb_join=0;

if ( isset ($_GET['use_nfs']))
	$use_nfs=1;
else
	$use_nfs=0;

//print_r($_GET);
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
      return "type_partition=\"NTFS\"";
      break;
    case "Windows2000" :
      return "type_partition=\"NTFS\"";
      break;
    case "WindowsXP" :
      return "type_partition=\"NTFS\"";
      break;
    case "Windows2003" :
      return "type_partition=\"NTFS\"";
      break;
    case "WindowsVista" :
      return "type_partition=\"NTFS\"";
      break;
    case "Windows7" :
      return "type_partition=\"NTFS\"";
      break;
    case "Windows7_x64" :
      return "type_partition=\"NTFS\"";
      break;
   case "Linux" :
      return "(nom_partition=\"/\" AND (type_partition=\"EXT2\" OR type_partition=\"EXT3\"))";
      break;
   case "Linux_x64" :
      return "(nom_partition=\"/\" AND (type_partition=\"EXT2\" OR type_partition=\"EXT3\"))";
      break;
  }
}

function statusOrdinateur($total,$nom_dns,$mon_ip) {
  global $indice;
  $requestlocal = "SELECT etat_install FROM ordinateurs WHERE nom_dns=\"$nom_dns\" AND etat_install NOT IN (\"installe\",\"modifie\")"; 
  $resultlocal = mysql_query($requestlocal);
  if (mysql_num_rows($resultlocal) > 0 ) {
 		$linelocal=mysql_fetch_array($resultlocal);
		$etat=$linelocal["etat_install"];
  	mysql_free_result($resultlocal);
		return "<TD><IMG SRC=\"ICONES/ordi_lock.png\"></TD><TD></TD><TD>$nom_dns</TD><TD></TD><TD>Cet ordinateur est en �tat $etat</TD>\n";
  }
  $requestlocal = "SELECT timestamp,ip_distante FROM ordinateurs_en_consultation WHERE nom_dns=\"$nom_dns\""; 
  $resultlocal = mysql_query($requestlocal);
  if (mysql_num_rows($resultlocal) > 0 ) {
 		$linelocal=mysql_fetch_array($resultlocal);
    $ip=$linelocal["ip_distante"];
  	mysql_free_result($resultlocal);
    return "<TD><IMG SRC=\"ICONES/ordi_lock.png\"></TD><TD></TD><TD>$nom_dns</TD><TD></TD><TD>Cet ordinateur est en consultation depuis $ip </TD>\n";
  }
  $requestlocal = "SELECT nom_dns,capacite from stockages_de_masse WHERE nom_dns=\"$nom_dns\" AND num_disque=0"; 
  $resultlocal = mysql_query($requestlocal);
  if (mysql_num_rows($resultlocal) < 1 )  {
  	mysql_free_result($resultlocal);
		return "<TD><A HREF=\"modifier_machine.php?nom_dns=$nom_dns\" TARGET=\"new\"><IMG SRC=\"ICONES/ordi_confless.png\" BORDER=\"0\"></A></TD><TD></TD><TD>$nom_dns</TD><TD></TD><TD>Pas de disque dur d�fini pour cet ordinateur</TD>\n";
  } else {
	  
    for ($i=0;$i<mysql_num_rows($resultlocal);$i++) {
    	$linelocal=mysql_fetch_array($resultlocal);
    	$capa = $linelocal['capacite'];
    	//print "la taille : $total, la capacit� : $capa\n";
    }
    mysql_free_result($resultlocal);
	if ( $total > $capa ){
  		//mysql_free_result($resultlocal);
		return "<TD><A HREF=\"modifier_machine.php?nom_dns=$nom_dns\" TARGET=\"new\"><IMG SRC=\"ICONES/ordi_confless.png\" BORDER=\"0\"></A></TD><TD></TD><TD>$nom_dns</TD><TD>$capa</TD><TD>Le disque est trop petit pour installer les partitions</TD>\n";
	}

	$requestlocal="INSERT INTO ordinateurs_en_consultation (nom_dns,ip_distante,timestamp) VALUES(\"$nom_dns\",\"$mon_ip\",NOW())";
	mysql_query($requestlocal);
    return "<TD><IMG SRC=\"ICONES/ordi_ok.png\"></TD><TD ALIGN=CENTER><INPUT TYPE=\"checkbox\" NAME=\"check[".$indice++."]\" VALUE=\"$nom_dns\" ></TD><TD>$nom_dns</TD><TD>".$capa."</TD><TD> OK Avec repartitionnement</TD>";
	}
}

///
/// Affiche la ligne de tableau correspondant au groupe. V�rifie sil l'op�ration est possible par rapport � son �tat actuel
///
function tableau_choix_poste($nom_dns,$nom_groupe)
{
	global $indice;
	
	if ($nom_groupe !="")
		$request = "SELECT o.nom_dns,etat_install FROM ord_appartient_a_gpe ogrp,ordinateurs o WHERE nom_groupe=\"$nom_groupe\"  and ogrp.nom_dns=o.nom_dns ORDER BY nom_dns";
	else
		$request = "SELECT o.nom_dns,etat_install FROM ordinateurs o WHERE nom_dns=\"$nom_dns\" ";
		$nom_groupe = "tous les ordinateurs";
	$result = mysql_query($request);
	print("<TABLE>");
	print("<TR><TD></TD><TD ALIGN=\"center\"  BGCOLOR=\"#CC00AA\"><b>S�lection</b></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Nom DNS</b></TD><TD ALIGN=\"left\"  BGCOLOR=\"#CC00AA\"><b>Status</b></TD></TR>\n");
	$indice=0;
	$i=0;
	if ($nom_groupe!="") {
		for (;$i<mysql_num_rows($result);$i++) {
			$line=mysql_fetch_array($result);
			$nd = $line['nom_dns'];
			$etat = $line['etat_install'];
			if ( $etat != 'modifie' &&  $etat != 'installe'  &&  $etat != 'depannage' && $etat != 'idb' && $etat  !='reboot') # Dans ces �tats, le changement est possible
				print ("<TR><TD><IMG SRC=\"ICONES/ordi_lock.png\"></TD><TD></TD><TD>$nd</TD><TD></TD><TD>Cet ordinateur est en �tat $etat</TD></TR>\n");
			else
				print ("<TR><TD><IMG SRC=\"ICONES/ordi_ok.png\"></TD><TD ALIGN=CENTER><INPUT TYPE=\"checkbox\" NAME=\"check[".$indice++."]\" VALUE=\"$nd\" ></TD><TD>$nd</TD><TD>$etat</TD></TR>\n");
		}
	}
		
	print("</TABLE>\n");
}

entete("Rapha�l RIGNIER - Les Chartreux : inforeseau@leschartreux.net", "CSS/g.css", "JeDDLaJ : Lancement de t�che - Etape 1");
include ("DBParDefaut.php");
ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);
?>
<Script type='text/javascript'>
	//Mise � jour dynamique du tableau par rapport � l'idb s�l�ectionn�
	function affiche_idb(selobject, arrvalue)
	{
		var item = selobject.options[selobject.selectedIndex];
		var val = item.value;
		
		var objidb = arrvalue[val];
		
		var element = document.getElementsByName("nom_logiciel")[0];
		element.textContent = objidb["nom_logiciel"];
		element = document.getElementsByName("nom_os")[0];
		element.textContent = objidb["nom_os"];
		
		//parcours des partitions pour mis � jours des valeurs du tableau
		for ( var i=0; i< 5; i++)
		{
			var id_idb=""
			var nom_idb=""
			var taille=""
			if ( typeof objidb[i] != 'undefined' ) //extraction des nouvelles valeurs
			{
				id_idb = objidb[i]["id_idb"];
				nom_idb = objidb[i]["nom_idb"];
				taille = objidb[i]["taille"];
			}
			//mise � jour des �l�ments du tableau
			var element = document.getElementsByName("id_idb" + i)[0];
			element.textContent = id_idb;
			element = document.getElementsByName("nom_idb" + i)[0];
			element.textContent = nom_idb;
			element = document.getElementsByName("taille" + i)[0];
			element.textContent = taille;
			
		}
		
		return;
		
	}
</Script>
<?
#Affichage du type de t�che dans le titre
$request = "SELECT * FROM type_tache WHERE idtype_tache=$type_tache";
$result = mysql_query($request);
if (mysql_num_rows($result) == 0){
	print "<P>Pas de t�che de ce type : $type_tache</P>";
	exit;
}
$enr = mysql_fetch_assoc($result);
mysql_free_result($result);
print("<CENTER><H1>Lancement de t�che -<u>".$enr['desc']."</u>- Etape 1</H1></CENTER>\n");

#On stocke les partitions li�es aux images de base
$request = "SELECT * FROM logiciels,images_de_base idb WHERE id_logiciel=id_os order by id_logiciel,id_idb";
$result = mysql_query($request);
$images = array();
$idlog_cur = 0;
$j = 0;$tot=0;
for ($i=0;$i<mysql_num_rows($result);$i++) {
  $line = mysql_fetch_array($result);
  if ( $idlog_cur != $line['id_logiciel']) {
	  $j =0;
	  $idlog_cur = $line['id_logiciel'];
	  $images[$idlog_cur]['nom_logiciel'] = $line['nom_logiciel'];
	  $images[$idlog_cur]['nom_os'] = $line['nom_os'];
	  $images[$idlog_cur][$j] = array();
  }
  $images[$idlog_cur][$j]['id_idb'] = $line['id_idb'];
  $images[$idlog_cur][$j]['nom_idb'] = $line['nom_idb'];
  $images[$idlog_cur][$j]['taille'] = $line['taille'];
 
  $j++;
}

$etat = 'installe';
switch ($type_tache)
{
	case 1:
		if (isset($_GET['id_logiciel']))
			$idlog = $_GET['id_logiciel'];
		else
			$idlog = 1;
		#Affichage du combo avec l'affichage dynamique des infos
		print("<CENTER><FORM Method='GET'><BR>S�lectionnez une installation : ");
		print("<input type='hidden' name='nom_groupe' value='$nom_groupe'>");
		print("<input type='hidden' name='nom_dns' value='$nom_dns'>");
		print("<input type='hidden' name='id_typetache' value='$type_tache'>");

		print("<SELECT name=\"id_logiciel\" onChange='affiche_idb(this," .json_encode($images) .")'>\n");
		foreach ( $images as  $k => $v)
		{
		  print ("<OPTION value='".$k ."'");
		  if ( $k == $idlog)
			print(" selected ");
			
		  print ( ">". $v['nom_logiciel'] ."</OPTION>\n");
		}
		print("</SELECT>\n");


		print ("<BR><B>Nom  : </B> <span name='nom_logiciel'>". $images[$idlog]['nom_logiciel'] ."</span><BR>\n");
		print ("<B>Nom OS : </B> <span name='nom_os'>". $images[$idlog]['nom_os'] ."</span>n");
		print "<BR>Les partitions associ�es : <BR><TABLE>";
		print ("<TR align='center'><TD>ID</TD><TD>nom</TD><TD>Taille</TD></TR>\n");


			
		//On cr�e une table de 5 lignes maxi (5 images de base devrait suffire)
		$i=0;
		$tot=0;
		for ($i=0;$i<5;$i++)
		{
			$ididb = "";
			$nom_idb = "";
			$taille = "";

			if ( isset($images[$idlog][$i]))
			{
				$ididb = $images[$idlog][$i]['id_idb'];
				$nom_idb = $images[$idlog][$i]['nom_idb'];
				$taille = $images[$idlog][$i]['taille'];
				#en plus de l'affichage, on calcule la taille totale utilis�e par l'image de base
				$ttot= split(' ',$taille);
				if ($ttot[1] == 'GB') $tot+=intval($ttot[0]);
				if ($ttot[1] == 'MB') $tot+=round(intval($ttot[0]) / 1024,2);
			}
			print("<TR><TD name='id_idb$i'>$ididb</TD><TD name='nom_idb$i'>$nom_idb</TD><TD name='taille$i'>$taille</TD></TR>\n");
		}
		print "</TABLE></P>\n";
		print "<BR><B>Taille totale :</B> $tot";
		print ("<BR>Les postes sopnt connect�s � : ");
		print("<SELECT name='link_speed'><OPTION value='100'");
		if ($link_speed == "100") print " selected ";
		print ">100mb/s</OPTION><OPTION value='1000'";
		if ($link_speed == "1000") print " selected ";
		print ">1gb/s</OPTION></SELECT><BR>\n";
		if ( $cb_join ==1)
			$cbjoinval='Checked';
		else
			$cbjoinval='';
			
		print ("<INPUT Type='checkbox' Name='cb_join' Value ='JoinDom' $cbjoinval />Jointure au domaine<BR>\n");
		
		$usenfs='';
		if ($use_nfs) $usenfs='Checked';
		print ("<INPUT Type='checkbox' Name='use_nfs' Value ='UseNfs' $usenfs />Utiliser NFS<BR>\n");

		print "<input type='submit' value='Valider'>\n";
		 
		print "<BR></FORM></CENTER>";



		//print "<pre>"; print_r($images); print "</pre>";

		$request = "SELECT nom_dns AS total FROM ord_appartient_a_gpe WHERE nom_groupe=\"$nom_groupe\" GROUP BY nom_dns";
		$result = mysql_query($request);


		if (mysql_num_rows($result)>0 || $nom_dns!="" ) {

		  print("<CENTER>\n");
		  print("<FORM METHOD=\"POST\" NAME=\"form\" ACTION=\"lancement_tache_2.php\">\n");
			
		  $mon_ip=getenv('REMOTE_ADDR');
		  $request = "DELETE FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\" OR NOW()-timestamp>500";
		  mysql_query($request);
		  
		  if ($nom_groupe!="") {
			$request = "SELECT photo FROM groupes WHERE nom_groupe=\"$nom_groupe\"";
			$result = mysql_query($request);
			$line = mysql_fetch_array($result);
			$photo = $line["photo"];
			mysql_free_result($result);
		  }
		  else $photo="ordinateur.jpg";
		  
		  
		  print("<TABLE>\n");
		  print("<TR><TD><IMG SRC=\"PHOTOS/$photo\" WIDTH=\"200\" HEIGHT=\"120\"></TD>\n");
		  if ($nom_groupe!="" )
			print("<TD><TABLE><TR><TD COLSPAN=\"2\"><b>Groupe : </b> $nom_groupe</TD>");
		  else
			print("<TD><TABLE><TR><TD COLSPAN=\"2\"><b>Ordinateur : </b> $nom_dns</TD>");
		  
		  print("</TR><TR>");
		  print("</TR></TABLE></TD>\n");
		  print("</TR></TABLE>\n");
		  print("</TR></TABLE>\n");
		  
		  $request = "SELECT nom_dns FROM ord_appartient_a_gpe WHERE nom_groupe=\"$nom_groupe\" ORDER BY nom_dns";
		  $result = mysql_query($request);
		  
		  print("<TABLE>");
		  print("<TR><TD></TD><TD ALIGN=\"center\"  BGCOLOR=\"#CC00AA\"><b>S�lection</b></TD><TD ALIGN=\"center\" BGCOLOR=\"#CC00AA\"><b>Nom DNS</b></TD><TD ALIGN=\"center\"  BGCOLOR=\"#CC00AA\"><b>Taille disque 0</b></TD><TD ALIGN=\"left\"  BGCOLOR=\"#CC00AA\"><b>Status</b></TD></TR>\n");
			$indice=0;
			$i=0;
			if ($nom_groupe!="") {
				for (;$i<mysql_num_rows($result);$i++) {
				$line=mysql_fetch_array($result);
				print("<TR>\n".statusOrdinateur($tot,$line["nom_dns"],$mon_ip)."</TR>\n");
				}
			} else print("<TR>\n".statusOrdinateur($tot,$nom_dns,$mon_ip)."</TR>\n");
			print("</TABLE>\n");
			print("<INPUT TYPE=\"hidden\" NAME=\"nom_groupe\" VALUE=\"$nom_groupe\">\n");
			print("<INPUT TYPE=\"hidden\" NAME=\"id_logiciel\" VALUE=\"$idlog\">\n");
			print("<INPUT TYPE=\"hidden\" NAME=\"nb_ordinateurs\" VALUE=\"$i\">\n");
			print("<INPUT TYPE=\"hidden\" NAME=\"nb_check\" VALUE=\"$indice\">\n");
			print("<INPUT TYPE=\"hidden\" NAME=\"photo\" VALUE=\"$photo\">\n");
			print("<INPUT TYPE=\"hidden\" NAME=\"link_speed\" VALUE=\"$link_speed\">\n");
			print("<INPUT TYPE=\"hidden\" NAME=\"use_nfs\" VALUE=\"$use_nfs\">\n");
			print("<INPUT TYPE=\"hidden\" NAME=\"joindom\" VALUE=\"$cb_join\">\n");
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
		else print("<P><CENTER>La s�lection est vide.</CENTER></P>\n");
		break;
		
	case 2:
		if ($etat=='installe') $etat = "renomme";
	case 3:
		if ($etat=='installe') $etat = "reboot";
	case 4:
		if ($etat == 'installe') $etat = "depannage";
		print "<CENTER>";
		print "\n<BR><FORM name='frmChangeState' action='lancement_tache_2.php' method='POST'>\n";
		tableau_choix_poste($nom_dns,$nom_groupe);
		print "<INPUT type='hidden' value='$etat' name='type_etat'> \n";
		print "<INPUT type='hidden' value='$nom_groupe' name='nom_groupe'> \n";
		print "<INPUT type='submit' value ='valider'>";
		print "</CENTER>";
		break;
		
}

mysql_free_result($result);

DisconnectMySQL();

print("<BR><HR><P><CENTER><A HREF=accueil.php>Retour</A></CENTER></P>\n");

PiedPage();

?>
