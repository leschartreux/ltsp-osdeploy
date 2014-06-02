<?php
/**
 * ************************** GPL STUFF **********************************
 *
 * ********************************* ENGLISH *********************************
 * 
 * --- Copyright notice :
 * 
 * Copyright 2003, 2004, 2005 Gérard Milhaud - Frédéric Bloise
 * 
 * 
 * --- Statement of copying permission
 * 
 * This file is part of JeDDLaJ.
 * 
 * JeDDLaJ is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * JeDDLaJ is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with JeDDLaJ; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * 
 * *********** TRADUCTION FRANÇAISE PERSONNELLE SANS VALEUR LÉGALE ***********
 *
 * --- Notice de Copyright :
 * 
 * Copyright 2003, 2004, 2005 Gérard Milhaud - Frédéric Bloise
 * Copyright 2010, 2011  Frédéric Bloise - Gérard Milhaud - Arnaud Salvucci
 * 
 * 
 * --- Déclaration de permission de copie
 * 
 * Ce fichier fait partie de JeDDLaJ.
 * 
 * JeDDLaJ est un logiciel libre : vous pouvez le redistribuer ou le modifier
 * selon les termes de la Licence Publique Générale GNU telle qu'elle est
 * publiée par la Free Software Foundation ; soit la version 2 de la Licence,
 * soit (à votre choix) une quelconque version ultérieure.
 * 
 * JeDDLaJ est distribué dans l'espoir qu'il soit utile, mais SANS AUCUNE
 * GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou 
 * d'ADAPTATION DANS UN BUT PARTICULIER. Voir la Licence publique Générale GNU
 * pour plus de détails.
 * 
 * Vous devriez avoir reçu une copie de la Licence Publique Générale GNU avec 
 * JeDDLaJ ; si ça n'était pas le cas, écrivez à la Free Software Foundation,
 * Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * 
 * ******************* END OF GPL STUFF ***************************************
 */
include("UtilsHTML.php");


if (is_file('DBParDefaut.php')) {

    //on lit le fichier et on récupère les valeurs de connexion
    $file = file_get_contents('DBParDefaut.php');


    preg_match('#\$user = "(.*)"#', $file, $matches);
    $user = $matches[1];

    preg_match('#\$pwd = "(.*)"#', $file, $matches);
    $password = $matches[1];
    

    preg_match('#\$host = "(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})"#', $file, $matches);
    $host = $matches[1];

    preg_match('#\$db = "(.*)"#', $file, $matches);
    $db = $matches[1];

 } else {   

    $file = file_get_contents('DBParDefaut.php.dist');

    $user = '';
    $password = '';
    $host = '';
    $db = '';
 }

if (isset($_POST['user']) && isset($_POST['password']) && isset($_POST['host']) && isset($_POST['db'])) {

    if ($_POST['user'] !== '' && $_POST['password'] !== '' && $_POST['host'] !== '' && $_POST['db'] !== '') {

        if ($_POST['protection']) {

            file_put_contents('#protection#', '');

        }

        $file = preg_replace('#\$user = "(.*)"#', '$user = "'.$_POST['user'].'"', $file);

        $file = preg_replace('#\$pwd = "(.*)"#', '$pwd = "'.$_POST['password'].'"', $file);

        $file = preg_replace('#\$host = "(.*)"#', '$host = "'.$_POST['host'].'"', $file);

        $file = preg_replace('#\$db = "(.*)"#', '$db = "'.$_POST['db'].'"', $file);

        file_put_contents('DBParDefaut.php', $file);

        header('Location:choixInstall.php');

    } else {

        echo 'Veuillez remplir tous les champs';
    }

 }

entete("Frédéric Bloise & Gérard Milhaud & Arnaud Salvucci : dosicalu@univmed.fr", "CSS/g.css", "JeDDLaJ : setup");

echo '<h1>JeDDLaJ Setup</h1>';


echo '<div>';
echo '<fieldset>';
echo '<legend>Identifiant de connexion à la base MySQL de l\'utilisateur rembo</legend>';

echo '<form action="setup.php" method="post">';

echo '<p>';
echo '<label for="user">User : </label>';
echo '<input type="text" name="user" id="user" value="'.$user.'" />';
echo '</p>';

echo '<p>';
echo '<label for="password">Password : </label>';
echo '<input type="password" name="password" id="password" value="'.$password.'" />';
echo '</p>';

echo '<p>';
echo '<label for="host">Host (adresse IP du serveur): </label>';
echo '<input type="text" name="host" id="host" value="'.$host.'" />';
echo '<p>';

echo '<p>';
echo '<label for="db">Base de données : </label>';
echo '<input type="text" name="db" id="db" value="'.$db.'" />';
echo '</p>';

echo '<p>';
#echo '<label for="protection">Voulez-vous protéger votre installation de JeDDLaJ par htaccess ?</label>';
echo '<input type="checkbox" value="false" name="protection" id="protection" /><br />';
#echo '<em>Cette fonctionnalité nécessite que vous remplaciez la valeur de la propriété <strong>AllowOverride</strong> par <strong>AuthConfig</strong> dans votre virtual host</em>';
echo '</p>';

echo '<p>';
echo '<input type="submit" name="creerDB" value="Creer le fichier DBParDefaut" />';
echo '</p>';

echo '</form>';
echo '</fieldset>';

echo '</div>';


PiedPage();
?>
