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

$check = $_POST["check"];
$nom_groupe = $_POST["nom_groupe"];
if (isset( $_POST["id_logiciel"]))
	$id_os = $_POST["id_logiciel"];

if (isset( $_POST["nb_ordinateurs"]))
	$nb_ordinateurs = $_POST["nb_ordinateurs"];
	
if (isset ($_POST["nb_check"]))
	$nb_check = $_POST["nb_check"];

if (isset( $_POST["photo"]))
	$photo = $_POST["photo"];
#$partition = $_POST["partition"];

$partition = "0:1";
if (isset( $_POST['link_speed']))
	$speed = $_POST['link_speed'];

if (isset ( $_POST['use_nfs']))
	$use_nfs = $_POST['use_nfs'];

if (isset ( $_POST['joindom']))
	$joindom = $_POST['joindom'];

print_r ($_POST);
?>

<HTML>
<HEAD>
<META http-equiv="Content-Type" content="text/html; charset=iso-8859-15">
<LINK REL=STYLESHEET TYPE="text/css" HREF="CSS/g.css",TITLE="CSS/g.css">
<LINK REL=STYLESHEET TYPE="text/css" HREF="CSS/infobulles.css",TITLE="CSS/infobulles.css">
<TITLE> Configuration Logicielle - Etape 2 </TITLE>
<META NAME="Author", CONTENT=" Rapha�l RIGNIER - Les Chartreux - inforeseau@leschartreux.net">
<SCRIPT Language="JavaScript">

	var nombre_images_avant_softs=5;
	var nombre_images_par_ligne=5;
	var nombre_champs_caches_par_ligne=3;
	var nombre_href_par_ligne=3;
	var repertoire_icones="ICONES/";

	function rien() {}

	function changeEtat(numero_ligne,images,etats,nb_etats,id_logiciel) {
		indice=document.form[numero_ligne*nombre_champs_caches_par_ligne].value;
		coche=document.images[numero_ligne*nombre_images_par_ligne+nombre_images_avant_softs];
		oeil=document.images[numero_ligne*nombre_images_par_ligne+nombre_images_avant_softs+1];
		document.form[numero_ligne*nombre_champs_caches_par_ligne].value=(indice*1+1)%nb_etats;
		document.form[numero_ligne*nombre_champs_caches_par_ligne+1].value=etats[(indice*1+1)%nb_etats];
		coche.src=repertoire_icones+images[(indice*1+1)%nb_etats]+".jpg";
		if (nb_etats>2) {
			if (document.form[numero_ligne*nombre_champs_caches_par_ligne+1].value!="comme_voulu") {
				oeil.src=repertoire_icones+"vide.png";
				oeil.height=0;
				document.anchors[numero_ligne*nombre_href_par_ligne+2].href="javascript:rien()";
				document.anchors[numero_ligne*nombre_href_par_ligne+2].target="";
			} else {
				oeil.src=repertoire_icones+"eye.jpg";
				oeil.height=40;
				document.anchors[numero_ligne*nombre_href_par_ligne+2].href="logiciel_sur_ordinateurs.php?id_logiciel="+id_logiciel;
				document.anchors[numero_ligne*nombre_href_par_ligne+2].target="new";
				}
			}
	}

	function resetAll() {
		for (i=0;i<document.form.length-3;i+=nombre_champs_caches_par_ligne) document.form[i+1].value="comme_voulu";
		location.reload();	
	}

</SCRIPT>
</HEAD>

<BODY BGCOLOR=#FFFFFF>

<CENTER><H1>Lancement t�che - Etape 2</H1></CENTER>

<CENTER>

<?php

include("UtilsMySQL.php"); 
include ("DBParDefaut.php");
include ("UtilsPyDDLaJ.php");


ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

$mon_ip=getenv('REMOTE_ADDR');

if (count($check) == 0) {
	print("Aucun ordinateur s�lectionn�.<BR>\n");
	$request = "DELETE FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\"";
	mysql_query($request);
} else {
	if ( isset($_POST['type_etat']))
	{
		foreach ( $check as $nomdns) //On change l'�tat du poste et on supprime son fichier de boot local
		{
			$request = "UPDATE ordinateurs SET etat_install='" . $_POST['type_etat'] . "' WHERE nom_dns='$nomdns'";
			mysql_query($request);
			print "<BR>Mise � jour du poste $nomdns en �tat '" . $_POST['type_etat'] ."<BR>\n";
			supprime_boot_local($nomdns);
		}
			
	}
	else
	{
		switch(count($check)) {
			case 1 :
				$groupement="Ordinateur";
				for ($i=0;!isset($check[$i]);$i++) ;
				$nom_groupe=$check[$i];
				$photo="ordinateur.jpg";
				break;
			case $nb_ordinateurs :
				$groupement="Groupe";
				break;
			default :
				$groupement="Sous-groupe de";
		}
		$request="SELECT * FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\" AND NOW()-timestamp<=500";
		$result=mysql_query($request);
		$expired=(mysql_num_rows($result)==0);
		mysql_free_result($result);
		$request = "DELETE FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\"";
		mysql_query($request);
		if ($expired) {
			print("La s�lection a expir�.<BR>\n");
		}
		else {
			for ($i=0;$i<$nb_check;$i++) {
				if (isset($check[$i])) {
					$num=explode(":",$partition);
					$request="INSERT INTO ordinateurs_en_consultation (nom_dns,ip_distante,timestamp,num_disque,num_partition) VALUES(\"$check[$i]\",\"$mon_ip\",NOW(),\"$num[0]\",\"$num[1]\")";
						mysql_query($request);
				}
			}  
			$request = "SELECT nom_os,nom_logiciel,version,icone FROM logiciels WHERE id_logiciel=\"$id_os\"";
			$result = mysql_query($request);
			$line = mysql_fetch_array($result);
			$nom_os=$line["nom_os"];
			$nom_logiciel_version=$line["nom_logiciel"]." ".$line["version"];
			$icone = $line["icone"];
			mysql_free_result($result);
			print("<TABLE>\n");
			print("<TR><TD><IMG SRC=\"PHOTOS/$photo\" WIDTH=\"200\" HEIGHT=\"120\"></TD>\n");
			print("<TD><TABLE><TR><TD COLSPAN=\"2\"><b>$groupement : </b> ");
			print("$nom_groupe</TD></TR><TR><TD><IMG SRC=\"ICONES/$icone\" WIDTH=\"100\" HEIGHT=\"100\"></TD>");
			print("<TD ALIGN=\"left\"><TABLE><TR><TD><b>OS : </b> $nom_os</TD><TR><TD><b>Distribution : </b>$nom_logiciel_version</TD></TR></TABLE></TD>\n");
			print("</TR></TABLE>\n");
			print("</TR></TABLE>\n");
			print("\n");
			
			#On stocke les infos sur les images de base de la distrib
			$request = "Select * from images_de_base where id_os=$id_os";
			$result = mysql_query($request);
			$images = array();
			for ($i=0;$i<mysql_num_rows($result);$i++)
			{
				$row = mysql_fetch_array($result);
				$images[$row['num_part'] ] = array();
				$images[$row['num_part'] ]['id_idb'] = $row['id_idb']; #Le partitionnement doit correspondre � la dsitribution. C'est pas tr�s souple.
				$images[$row['num_part'] ]['nom_idb'] = $row['nom_idb'];
			}
			mysql_free_result($result);
			
			//print_r($images);
			print "<P>";
			# On cr�e une t�che pour le d�ploiement des idb
			$request = "Insert into tache (type_tache,speed,faire_jointure,utilise_nfs) values('deploieidb',$speed,$joindom,$use_nfs)";
			mysql_query($request);
			# On stocke le num�ro de la t�che cr��e
			$request = "Select LAST_INSERT_ID()";
			$result = mysql_query($request);
			while ( $row = mysql_fetch_array($result) )
				$id_tache= $row[0];
			mysql_free_result($result);
			print ("* La t�che de d�ploiement $id_tache a �t� cr��e pour accueillir les postes.<BR><BR>\n");
			
			#Pour chaque ordinateur coch� Nous passons � la mise � jour de la base avec le nouveau partitionnement et les idb associ�es
			$request = "Select * FROM ordinateurs_en_consultation oc WHERE oc.ip_distante='$mon_ip'  and NOW()-timestamp<=500";
			$result = mysql_query($request);
			$listeordis = "";
			while ( $row = mysql_fetch_array($result) )
			{
				$listeordis .= $row['nom_dns'] .", ";
				$dns = $row['nom_dns'];
				$request2 = "Delete from idb_est_installe_sur WHERE nom_dns='" . $row['nom_dns'] . "'";
				mysql_query($request2);
				print ("* J'ai supprim� la/les " . mysql_affected_rows() . " idb(s) Actuellement d�finies sur $dns pour coller avec la distrib<BR>\n");
				supprime_boot_local($dns);
				foreach ( $images as $k => $v)
				{ # On r�ins�re les idb correspondantes
					$request3 = "Insert into idb_est_installe_sur ( id_idb,nom_dns,num_disque,num_partition,etat_idb,idb_active) "
						  . " values ("
						  . $v['id_idb'] .","
						  . "'". $row['nom_dns'] . "',"
						  . "0,$k," #Toujours disque 0....
						  . "'a_ajouter','oui'"
						  .")";
					mysql_query($request3);
					Print ("* J'ai ajout� la partition $k ". $v['nom_idb']." au poste $dns<BR>\n");
					
				}
				#il ne peut y avoir qu'une t�che en cours par ordinateur
				#On supprime l'assignation des t�che pr�c�dentes qui se seraient mal d�roul�e
				$request3 = "delete from tache_est_assignee_a where nom_dns='" .$row['nom_dns'] . "' and id_tache in (select id_tache from tache where dte_fin is null)"; 
				mysql_query($request3);
				print "* J'ai supprim� " . mysql_affected_rows() . " t�che(s) non termin�e(s) pour ".$row['nom_dns']."<br>\n";
				
				$request3 = "Insert into tache_est_assignee_a (nom_dns,id_tache) values ('" .$row['nom_dns'] . "',$id_tache)";
				mysql_query($request3);
				print "* J'ai ajout� ".$row['nom_dns']." a la t�che $id_tache<BR>\n";
				
				$request3 = "Update stockages_de_masse set dd_a_partitionner='oui' where num_disque=0 and nom_dns='" . $row['nom_dns'] . "'";
				mysql_query($request3);
				Print ("* J'ai chang� l'�tat du disque 0 " .mysql_affected_rows(). "� partitionner de $dns<BR>\n");
				
				$request3 = "UPDATE ordinateurs set etat_install='modifie' where nom_dns='$dns'";
				mysql_query($request3);
				Print ("* J'ai chang� l'�tat de l'ordinateur $dns'");
				
				Print ("<BR><BR>\n");

			}
			print "</P><P>Les postes <FONT color='red'>$listeordis</FONT> sont pr�ts � �tre d�ploy�s.<BR>\n";
			print "Il ne vous reste plus qu'� rebooter/allumer les postes et laisser pyddlaj faire son travail... Dans la joie !</p>\n";
		}

	}
	DisconnectMySQL();
}

//print("<INPUT TYPE=\"hidden\" NAME=\"id_os\" VALUE=\"$id_os\">");
//print("</FORM>\n");
?>

</CENTER>
<BR><HR><P><CENTER><A HREF=accueil.php>Retour</A></CENTER></P>
</BODY>
</HTML>
