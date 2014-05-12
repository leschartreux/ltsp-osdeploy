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

$check = $_POST["check"];
$nom_groupe = $_POST["nom_groupe"];
$id_os = $_POST["id_os"];

$nb_ordinateurs = $_POST["nb_ordinateurs"];
$nb_check = $_POST["nb_check"];
$photo = $_POST["photo"];
$partition = $_POST["partition"];

?>

<HTML>
<HEAD>
<META http-equiv="Content-Type" content="text/html; charset=iso-8859-15">
<LINK REL=STYLESHEET TYPE="text/css" HREF="CSS/g.css",TITLE="CSS/g.css">
<LINK REL=STYLESHEET TYPE="text/css" HREF="CSS/infobulles.css",TITLE="CSS/infobulles.css">
<TITLE> Configuration Logicielle - Etape 2 </TITLE>
<META NAME="Author", CONTENT=" Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr">
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

<CENTER><H1>Configuration Logicielle - Etape 2</H1></CENTER>

<CENTER>

<?php

include("UtilsMySQL.php"); 
include ("DBParDefaut.php");

ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
SelectDb($GLOBALS['db']);

$mon_ip=getenv('REMOTE_ADDR');

if (count($check) == 0) {
	print("Aucun ordinateur sélectionné.<BR>\n");
	$request = "DELETE FROM ordinateurs_en_consultation WHERE ip_distante=\"$mon_ip\"";
	mysql_query($request);
} else {
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
		print("La sélection a expiré.<BR>\n");
	}
	else {
	for ($i=0;$i<$nb_check;$i++) {
		if (isset($check[$i])) {
		  	$num=explode(":",$partition[$i]);
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
	print("<FORM METHOD=\"POST\" NAME=\"form\" ACTION=\"configuration_logicielle_3.php\">\n");
	print("<TABLE><TR><TD align=\"center\" BGCOLOR=\"#CC00AA\"></TD><TD align=\"center\" BGCOLOR=\"#CC00AA\"><b>Nom Logiciel</b></TD><TD align=\"center\" BGCOLOR=\"#CC00AA\"><b>Version</b></TD><TD align=\"center\" BGCOLOR=\"#CC00AA\"><b>Info</b></TD><TD align=\"center\" BGCOLOR=\"#CC00AA\"><b>Etat actuel</b></TD><TD align=\"center\" BGCOLOR=\"#CC00AA\"><b>Etat voulu</b></TD></TR>\n");
	
	# A partir de maintenant nb_ordinateurs est égal aux nombres d'ordinateurs choisis
	$nb_ordinateurs=count($check);	

	# On selectionne tous les logiciels de l'os ainsi que le nombre d'ordinateurs du groupe sur lesquels ils sont installables
	# !!! LE DISTINCT EST OBLIGATOIRE A CAUSE DE LA JOINTURE AVEC COMPOSANT_EST_INSTALLE_SUR !!!
	# 21-06-2006 : On ne montre maintenant que les logiciels visibles 
	#$request = "SELECT COUNT(DISTINCT c.nom_dns) AS total,a.id_logiciel,nom_logiciel,version,description,icone FROM logiciels AS a, packages AS b, ordinateurs AS c, ordinateurs_en_consultation AS d, composant_est_installe_sur AS e WHERE nom_os=\"$nom_os\" AND a.id_logiciel=b.id_logiciel AND c.nom_dns=d.nom_dns AND d.nom_dns=e.nom_dns AND ( specificite=\"aucune\" OR ( specificite=\"nom_dns\" AND valeur_specificite=c.nom_dns) OR ( specificite=\"signature\" AND valeur_specificite=c.signature) OR valeur_specificite=e.id_composant) AND visible=\"oui\" GROUP BY a.id_logiciel ORDER BY nom_logiciel,version";
	# 14-07-2012 : On éclate les OR de la condition ( specificite=\"aucune\" OR ( specificite=\"nom_dns\" AND valeur_specificite=c.nom_dns) OR ( specificite=\"signature\" AND valeur_specificite=c.signature) OR valeur_specificite=e.id_composant) en 4 requetes regroupees ensuite pas UNION pour optimiser le temps de calcul. Pour info, avec la base du 14-07-2012, lancee sur les ordinateurs et tous les logiciels de tous les OS, la requete initiale met plus de 11 minutes. Ainsi optimisee, la requete se termine en moins de 6 secondes !!!
	# 13-12-2012 : on recupere l'etat visible mais on n'exclu pas encore les logiciels non visible
	$request = "SELECT COUNT(DISTINCT a.nom_dns) AS total,c.id_logiciel,nom_logiciel,version,icone,description,visible FROM ordinateurs_en_consultation AS a,composant_est_installe_sur AS b,packages AS c,logiciels AS d WHERE a.ip_distante='$mon_ip' AND a.nom_dns=b.nom_dns AND specificite='id_composant' AND valeur_specificite=b.id_composant AND c.id_logiciel=d.id_logiciel AND nom_os='$nom_os' GROUP BY c.id_logiciel UNION SELECT COUNT(nom_dns) AS total,c.id_logiciel,nom_logiciel,version,icone,description,visible FROM ordinateurs_en_consultation AS a,packages AS c,logiciels AS d WHERE a.ip_distante='$mon_ip' AND specificite='nom_dns' AND valeur_specificite=nom_dns AND c.id_logiciel=d.id_logiciel AND nom_os='$nom_os' GROUP BY c.id_logiciel UNION SELECT COUNT(a.nom_dns) AS total,c.id_logiciel,nom_logiciel,version,icone,description,visible FROM ordinateurs_en_consultation AS a,ordinateurs AS e,packages AS c,logiciels AS d WHERE a.ip_distante='$mon_ip' AND a.nom_dns=e.nom_dns AND specificite='signature' AND valeur_specificite=signature AND c.id_logiciel=d.id_logiciel AND nom_os='$nom_os' GROUP BY c.id_logiciel UNION SELECT COUNT(nom_dns) AS total,c.id_logiciel,nom_logiciel,version,icone,description,visible FROM ordinateurs_en_consultation AS a,packages AS c,logiciels AS d WHERE a.ip_distante='$mon_ip' AND specificite='aucune' AND c.id_logiciel=d.id_logiciel AND nom_os='$nom_os' GROUP BY c.id_logiciel ORDER BY nom_logiciel,version";
	$result = mysql_query($request);
	# Pour chaque logiciel 
	$numero_ligne=0;
	$tab_etats= array ("installe","a_ajouter","a_supprimer");
	for ($i=0;$i<mysql_num_rows($result);$i++) {
		$tab_etats["installe"]=$tab_etats["a_ajouter"]=$tab_etats["a_supprimer"]=0;
		$line = mysql_fetch_array($result);
		# On compte le nombre d'ordinateurs pour les etat_package possibles sur la partition associée 
		$request2 = "SELECT etat_package,COUNT(*) AS total FROM package_est_installe_sur AS a, packages AS b, logiciels AS c, ordinateurs_en_consultation AS d WHERE a.id_package=b.id_package AND b.id_logiciel=c.id_logiciel AND c.id_logiciel=\"".$line["id_logiciel"]."\" AND d.ip_distante=\"$mon_ip\" AND a.nom_dns=d.nom_dns AND a.num_disque=d.num_disque AND a.num_partition=d.num_partition GROUP BY etat_package";
		$result2 = mysql_query($request2);
		for ($j=0;$j<mysql_num_rows($result2);$j++) {
			$line2 = mysql_fetch_array($result2);
			$tab_etats[$line2["etat_package"]]=$line2["total"];
		}
		mysql_free_result($result2);
		# Nombre d'ordinateurs où le logiciel est installé dans l'état actuel
		$nea = $tab_etats["installe"] + $tab_etats["a_supprimer"];
		# Nombre d'ordinateurs où le logiciel est installé dans l'état voulu
		$nev = $tab_etats["installe"] + $tab_etats["a_ajouter"];
		# Suivant le nombre d'ordinateurs où le logiciel est installable 
		# Si installe sur aucune ordinateur et a ete rendu non visible
		# Alors on passe au logiciel suivant
		if ( $nea+$nev==0 && $line["visible"]=="non" ) continue;
		# Si installable sous un sous-ensemble d'ordinateurs
		if ( $line["total"] < $nb_ordinateurs )  $coche_verte = $line["total"];
		# Sinon installable partout
		else $coche_verte = "coche_verte";
		switch ($nea) {
			case $nb_ordinateurs :
				$image_etat_actuel = "$coche_verte";
				switch ($nev) {
					case $nb_ordinateurs :
						$image_etat_voulu = "$coche_verte";
						$js_images="['$image_etat_voulu','coche_rouge']";
						$js_etats="['comme_voulu','supprimer_partout']";
						$js_nb_etats=2;
						break;
					case 0 : 
						$image_etat_voulu = "coche_rouge";
						$js_images="['$image_etat_voulu','$coche_verte']";
						$js_etats="['comme_voulu','comme_actuel']";
						$js_nb_etats=2;
						break;
					default : 
						$image_etat_voulu = "$nev";
						$js_images="['$image_etat_voulu','$coche_verte','coche_rouge']";
						$js_etats="['comme_voulu','comme_actuel','supprimer_partout']";
						$js_nb_etats=3;
				}				
				break;
			case 0:
				$image_etat_actuel = "coche_rouge";
				switch ($nev) {
					case $nb_ordinateurs :
						$image_etat_voulu = "$coche_verte";
						$js_images="['$image_etat_voulu','coche_rouge']";
						$js_etats="['comme_voulu','comme_actuel']";
						$js_nb_etats=2;
						break;
					case 0 : 
						$image_etat_voulu = "coche_rouge";
						$js_images="['$image_etat_voulu','$coche_verte']";
						$js_etats="['comme_voulu','installer_partout']";
						$js_nb_etats=2;
						break;
					default : 
						$image_etat_voulu = "$nev";
						$js_images="['$image_etat_voulu','$coche_verte','coche_rouge']";
						$js_etats="['comme_voulu','installer_partout','comme_actuel']";
						$js_nb_etats=3;
					}				
				break;
			default :
				$image_etat_actuel = "$nea";
				switch ($nev) {
					case $nb_ordinateurs :
						$image_etat_voulu = "$coche_verte";
						$js_images="['$image_etat_voulu','coche_rouge','$nea']";
						$js_etats="['comme_voulu','supprimer_partout','comme_actuel']";
						$js_nb_etats=3;
						break;
					case 0 : 
						$image_etat_voulu = "coche_rouge";
						$js_images="['$image_etat_voulu','$coche_verte','$nea']";
						$js_etats="['comme_voulu','installer_partout','comme_actuel']";
						$js_nb_etats=3;
						break;
					default : 
						if ( $nev == $nea ) {
							if ( $tab_etats["a_supprimer"] != 0 ) {
								$image_etat_voulu = "$nev"."+attention";
								$js_images="['$image_etat_voulu','$coche_verte','coche_rouge','$nea']";
								$js_etats="['comme_voulu','installer_partout','supprimer_partout','comme_actuel']";
								$js_nb_etats=4;
							} else {
								$image_etat_voulu = "$nea";
								$js_images="['$image_etat_voulu','$coche_verte','coche_rouge']";
								$js_etats="['comme_voulu','installer_partout','supprimer_partout']";
								$js_nb_etats=3;
							}
						} else {
							$image_etat_voulu = "$nev";
							$js_images="['$image_etat_voulu','$coche_verte','coche_rouge','$nea']";
							$js_etats="['comme_voulu','installer_partout','supprimer_partout','comme_actuel']";
							$js_nb_etats=4;
						}
				}
			}
			print("<TR>\n");
			print("<TD ALIGN=\"center\"> <IMG HEIGHT=\"40\" WIDTH=\"40\" SRC=\"ICONES/".$line["icone"]."\"></TD>\n");
			print("<TD ALIGN=\"center\"><b> ".$line["nom_logiciel"]." <b> </TD>\n");
			print("<TD ALIGN=\"center\"> ".$line["version"]." </TD>\n"); 
			if ($line["description"]!="")
				print("<TD ALIGN=\"center\"><a class=\"tooltip\"\" NAME=\"info\" HREF=\"javascript:rien()\"><IMG HEIGHT=\"40\" WIDTH=\"40\" BORDER=0 SRC=\"ICONES/Info.png\"><span class=\"info\">".nl2br($line["description"])."</span></a></TD>\n");
			else print("<TD><A NAME=\"info\" HREF=\"javascript:rien()\"><IMG SRC=\"ICONES/vide.png\" HEIGHT=0 BORDER=0></TD>\n");
			print("<TD ALIGN=\"center\"> <IMG SRC=\"ICONES/$image_etat_actuel.jpg\" HEIGHT=\"40\" WIDTH=\"40\"></TD>");
			print("<TD ALIGN=\"center\"> <A NAME=\"etat\" HREF=\"javascript:changeEtat($numero_ligne,$js_images,$js_etats,$js_nb_etats,'".$line["id_logiciel"]."')\"> <IMG SRC=\"ICONES/$image_etat_voulu.jpg\" HEIGHT=\"40\" BORDER=\"0\"> </A>\n");
			print("<INPUT TYPE=\"hidden\" NAME=\"indice[$numero_ligne]\" VALUE=\"0\">\n");
			print("<INPUT TYPE=\"hidden\" NAME=\"etat[$numero_ligne]\" VALUE=\"comme_voulu\">\n");
			print("<INPUT TYPE=\"hidden\" NAME=\"id_logiciel[$numero_ligne]\" VALUE=\"".$line["id_logiciel"]."\"></TD>\n");
			if ( $js_nb_etats>2 ) print("<TD ALIGN=\"center\"> <A NAME=\"details\" HREF=\"logiciel_sur_ordinateurs.php?id_logiciel=".$line["id_logiciel"]."\" TARGET=\"new\"> <IMG SRC=\"ICONES/eye.jpg\" HEIGHT=\"40\" BORDER=\"0\"></A></TD>\n");
			else print("<TD ALIGN=\"center\"><A NAME=\"details\" HREF=\"javascript:rien()\"><IMG SRC=\"ICONES/vide.png\" HEIGHT=0 BORDER=0></TD>\n");
			print("</TR>\n");
			$numero_ligne++;
		}
		print("</TR>\n</TABLE>\n<BR>\n");
		print("<INPUT TYPE=\"submit\" value=\"VALIDER LES MODIFICATIONS\">\n");
		print("<INPUT TYPE=\"button\" value=\"RESET\" onClick=\"resetAll()\">");
	}
	DisconnectMySQL();
}

print("<INPUT TYPE=\"hidden\" NAME=\"id_os\" VALUE=\"$id_os\">");
print("</FORM>\n");
?>

</CENTER>
<BR><HR><P><CENTER><A HREF=accueil.php>Retour</A></CENTER></P>
</BODY>
</HTML>
