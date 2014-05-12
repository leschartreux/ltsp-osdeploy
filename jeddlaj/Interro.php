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



## TO DO
## Reste a tester que la requete commence par SELECT...

# On recupere les variables
if (isset ($_GET["db"])) {$db = $_GET["db"];}
if (isset ($_POST["db"])) {$db = $_POST["db"];}
if (isset ($_GET["Table"])) {$Table = $_GET["Table"];}
if (isset ($_POST["Table"])) {$Table = $_POST["Table"];}
if (isset ($_GET["requestfromform"])) {$requestfromform = $_GET["requestfromform"];}
if (isset ($_POST["requestfromform"])) {$requestfromform = $_POST["requestfromform"];}
if (isset ($_GET["request"])) {$request = $_GET["request"];}
if (isset ($_POST["request"])) {$request = $_POST["request"];}
# toutes les variables ont ete recuperees

include("UtilsMySQL.php");
include("UtilsHTML.php");

function ChoixDB($host, $user, $pwd){
	ConnectMySQL($host, $user, $pwd);
	$db_list = mysql_list_dbs();
	$i = 0;
	$cnt = mysql_num_rows($db_list);
	echo("<CENTER><FONT SIZE=-1>");
	while ($i < $cnt) {
		if ($i != 0) {
			echo("&nbsp;||&nbsp;");
		}
		echo ("<A HREF=\"Interro.php?db=".mysql_db_name ($db_list, $i)."\">".mysql_db_name ($db_list, $i)."</A>\n");
		$i++;
	}	
	echo("</FONT></CENTER>");
	echo("<HR><BR>");
}

function ChoixTables($db){
	SelectDb($db);
	$result = mysql_list_tables($db);
        //Erreur 1044 Mysql = access denied for $user@$host to $db
	if (mysql_errno() == 1044){
		echo("<CENTER><FONT COLOR=RED SIZE=+2>\n");
		//echo mysql_errno().": ".mysql_error()."<BR>";
		echo("Accès refusé à l'utilisateur $GLOBALS[user]@$GLOBALS[host] pour la base de données $db. <BR>");
		echo("</CENTER></FONT>\n");
		exit;
	}
        $i = 0;
	echo("<CENTER><FONT SIZE=-1>");
        for ($i=0;$i< mysql_num_rows($result);$i++){
		if ($i != 0) {
			echo("&nbsp;||&nbsp;");
		}
		echo ("<A HREF=\"Interro.php?db=$db&Table=".mysql_tablename ($result, $i)."\">".mysql_tablename ($result, $i)."</A>\n");
	}
	echo("</FONT></CENTER>");
	echo("<HR><BR>");
}

function AfficheFormRequeteRawSQL(){
	echo("<CENTER>");
	echo("<FORM METHOD=POST ACTION=\"Interro.php\">");
	//Pour passer db=$db et Table=$Table en argument de l'action du formulaire...
	echo("<INPUT TYPE=HIDDEN NAME=user VALUE=$GLOBALS[user]>");
	echo("<INPUT TYPE=HIDDEN NAME=pwd VALUE=$GLOBALS[pwd]>");
	echo("<INPUT TYPE=HIDDEN NAME=db VALUE=$GLOBALS[db]>");
	echo("<INPUT TYPE=HIDDEN NAME=Table VALUE=$GLOBALS[Table]>");
	// pour differencier le cas requete par formulaire/requete par arguments
	echo("<INPUT TYPE=HIDDEN NAME=requestfromform VALUE=1>");
	echo("Vous pouvez choisir d'entrer directement ici votre requête SQL (<B>SELECT seulement</B>) pour attaquer la base de données <FONT COLOR=RED>$GLOBALS[db]</FONT>...<BR>");
	$request='';
	if (isset($GLOBALS['request'])) {$request = stripslashes($GLOBALS['request']);}
	# On met des simples quotes à la place des doubles car sinon la 
	# requete est tronquee a la premiere double quote lorsqu'on la 
	# pose dans la zone texte INPUT...
	$request = str_replace("\"","'",$request);
	//echo("<INPUT TYPE=TEXT SIZE=70 NAME=request VALUE=\"stripslashes($GLOBALS[request])\">");
	echo("<INPUT TYPE=TEXT SIZE=70 NAME=request VALUE=\"$request\">");
	echo("<INPUT TYPE=SUBMIT VALUE=\"Evaluer la requête\">");
	echo("</FORM>");
	echo("</CENTER>");
	echo("<HR><BR>");
}

function AfficheTout($db, $Table){
	SelectDb($db);
	AfficheFormRequeteRawSQL();
	$request=CreeRequete($Table,mysql_query("select * from $Table where 1=0"));
	echo("<CENTER>... ou bien de générer une requête graphiquement portant sur la table <FONT COLOR=RED>$GLOBALS[Table]</FONT> de la base de données <FONT COLOR=RED>$GLOBALS[db]</FONT>  :</CENTER><BR><BR>");
	$result = mysql_query($request);
	if (isset($GLOBALS['mode']))
	{
		if( $GLOBALS['mode'] == "txt" ){
			AfficheResultatTexte( $result );
		} else {
			AfficheResultatHTML( $request, $result );
			AfficheFormEtFinTable($db, $Table, $result);
			PiedPage();
		}
	}
	else
	{
		AfficheResultatHTML( $request, $result );
		AfficheFormEtFinTable($db, $Table, $result);
		PiedPage();
	}
}

function AfficheResultatTexte( $result ){
	//EcritLigneTitre($result);
	echo "<PRE>\n";
        for ($i=0;$i<mysql_num_rows($result);$i++){
                $line = mysql_fetch_row($result);
                for ($j=0;$j<mysql_num_fields($result);$j++){
                        echo "$line[$j]\t";
                }
                echo "\n";
        }
}
	

function AfficheResultatHTML( $request, $result ){
	echo("<CENTER>Requête SQL générée et executée : <FONT COLOR=green> $request</FONT></CENTER><BR>\n");
	echo("<CENTER><FONT COLOR=red> Nombre d'enregistrements retournés : ".mysql_num_rows($result)."</FONT><CENTER><BR>\n");
	AfficheEnteteTableEtRequete($result, "BORDER=1 CELLPADDING=2 CELLSPACING=1");
}

function FinitTable(){
	echo "</TABLE>\n";
}

function CreeRequete($Table, $result){
	// Cas de l'appel direct du script avec une requête toute faite,
	// depuis une page html quelconque...
	if (isset($GLOBALS["request"])){
		return(stripslashes($GLOBALS["request"]));
	}
	if (!isset($GLOBALS["Order"])){
		$OrderBy = "";
	}
	else {
		$tmp = $GLOBALS["Order"];
		$OrderBy = "ORDER BY $tmp";
	}
	$Where = "where 1=1";
	$request = "SELECT * from $Table";
	//Detection de la premiere execution
	$NomVariable = "grep_".mysql_field_name($result,0);

	if (!isset($_POST["$NomVariable"])){
		return("$request where 1=0");
	}
	//On cree la requete
	for ($i=0;$i<mysql_num_fields($result);$i++){
		$NomChamp = "grep_".mysql_field_name($result,$i);
		if ($_POST["$NomChamp"] != ""){
			$pattern=$_POST["$NomChamp"];
			$Where .= " AND ".mysql_field_name($result,$i)." LIKE '%$pattern%'";
		}
	}
	return("$request $Where $OrderBy");
}


function AfficheEnteteTableEtRequete($result, $extra){
        echo "<TABLE $extra>\n";
	// On ecrit les noms de champs en haut du tableau
	EcritLigneTitre($result);
        echo "<TR>\n";
        for ($i=0;$i<mysql_num_rows($result);$i++){
                echo "<TR>\n";
                $line = mysql_fetch_row($result);
                for ($j=0;$j<mysql_num_fields($result);$j++){
			echo "<TD>$line[$j]</TD>";
                }
                echo "</TR>\n";
        }
}


function AfficheFormEtFinTable($db, $Table, $result){
	echo("<FORM METHOD=POST ACTION=\"Interro.php\">");
	//Pour passer db=$db et Table=$Table en argument de l'action du formulaire...
	echo("<INPUT TYPE=HIDDEN NAME=Table VALUE=$Table>");
	echo("<INPUT TYPE=HIDDEN NAME=db VALUE=$db>");

	// On ecrit les cases de formulaire pour les grep
	echo("<TR>\n");
	for ($i=0;$i<mysql_num_fields($result);$i++){
		echo("<TH>\n");
		$NomChamp = "grep_".mysql_field_name($result,$i);
		$ValeurChamp = '';
		if (isset($GLOBALS[$NomChamp])) {$ValeurChamp = $GLOBALS[$NomChamp];}
		echo("<INPUT TYPE=TEXT SIZE=6 VALUE=\"$ValeurChamp\" NAME=\"grep_".mysql_field_name($result,$i)."\">");
		echo("</TH>\n");
	}
	echo("</TR>\n");
	// On reecrit les noms de champs pour les avoir aussi en bas du tableau
	EcritLigneTitre($result);
	// On ecrit la ligne de boutons radio pour le ORDER BY
	echo("<TR>\n");
	for ($i=0;$i<mysql_num_fields($result);$i++){
		echo("<TH>\n");
		// On teste pour checker le bouton qu'on avait coché lors de l'appel
		if (isset($GLOBALS["Order"]) and $GLOBALS["Order"] == mysql_field_name($result,$i)){
			echo("<INPUT TYPE=RADIO NAME=\"Order\" CHECKED VALUE=\"".mysql_field_name($result,$i)."\">");
		}
		else {
			echo("<INPUT TYPE=RADIO NAME=\"Order\" VALUE=\"".mysql_field_name($result,$i)."\">");
		}
		echo("</TH>\n");
	}
	FinitTable();
	echo "<INPUT TYPE=\"radio\" NAME=\"mode\" VALUE=\"html\" CHECKED> Resultat en format HTML<BR>\n";
	echo "<INPUT TYPE=\"radio\" NAME=\"mode\" VALUE=\"txt\"> Resultat en format texte<BR>\n";
	echo("<INPUT TYPE=SUBMIT VALUE=\"Lancer la roquête...\">");
	echo("</FORM>");
}



//Main
entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "CSS/g.css", "JeDDLaJ : Consultation BD");
//if (!isset($request)){
	include ("DBParDefaut.consult.php");
//}
//if ( !isset($user) or !isset($pwd) ){
//	Identification();
//}
print("<CENTER><H1>Consultation BD</H1></CENTER>\n");
if( isset( $request ) && isset( $db ) && !isset( $requestfromform )){
        ConnectMySQL($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pwd']);
        SelectDb($GLOBALS['db']);
        $request = stripslashes( $request );
	$result = mysql_query($request);
	AfficheResultatHTML( $request, $result );
	FinitTable();
} else {
	#ChoixDB($host, $user, $pwd);
	ConnectMySQL($host, $user, $pwd);
	if (isset($db)) {
		ChoixTables($db);
	}
	if (isset($Table)){
		AfficheTout($db, $Table);	
	}
}
print("<BR><BR><HR><P><CENTER><A HREF=accueil.php>Retour</A></CENTER></P>\n");
PiedPage();

?>
