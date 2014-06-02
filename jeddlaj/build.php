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
include("UtilsJeDDLaJ.php");

entete("Frédéric Bloise & Gérard Milhaud & Arnaud Salvucci : dosicalu@univmed.fr", "CSS/g.css", "JeDDLaJ : setup");

if (isset($_POST['typeInstall'])) {

    if ($_POST['typeInstall'] === 'update') {

        $type = 'update';

        if ($_POST['version'] !== '') {

            $version = $_POST['version'];

        } else {

            //redirection
            header('Location:choixInstall.php');
        }

    } else if ($_POST['typeInstall'] === 'new') {

        $type = 'new';
        $version = '';
    }

 } else {

    //redirection
    header('Location:choixInstall.php');
 }


echo '<h1>JeDDLaJ Setup</h1>';

echo '<div>';

echo '<fieldset>';
echo '<legend>Identifiant de l\'administrateur de MySQL</legend>';
echo '<form action="build.php" method="post">';

echo '<p>';
echo '<label for="login">Login : </label>';
echo '<input type="text" id="login" name="login" />';
echo '</p>';

echo '<p>';
echo '<label for="password">Password : </label>';
echo '<input type="password" id="password" name="password" />';
echo '</p>';

echo '<p>';
echo '<input type="hidden" name="typeInstall" value="'.$type.'" />';
echo '<input type="hidden" name="version" value="'.$version.'" />';
echo '</p>';

echo '<p>';
echo '<input type="submit" name="install" value="Finaliser l\'installation" />';
echo '</p>';
echo '</form>';
echo '</fieldset>';
echo '</div>';


PiedPage();

$link = false;


if (isset($_POST['login']) && isset($_POST['password'])) {

    if ($_POST['login'] !== '' && $_POST['password'] !== '') {

        //initialisation des paramètres
        //on lit le fichier et on récupère les valeurs de connexion
        $file = file_get_contents('DBParDefaut.php');

        /*if (file_exists('.htaccess') && file_exists('.htpasswd')) {
            
            $password = $_SERVER['PHP_AUTH_PW'];

        } else {        */

            preg_match('#\$pwd = "(.*)"#', $file, $matches);
            $password = $matches[1];
        //}

        preg_match('#\$user = "(.*)"#', $file, $matches);
        $user = $matches[1];

        preg_match('#\$host = "(.*)"#', $file, $matches);
        $host = $matches[1];

        preg_match('#\$db = "(.*)"#', $file, $matches);
        $db = $matches[1];


        //on lit le fichier et on récupère les valeurs de connexion
        $fileConsult = file_get_contents('DBParDefaut.consult.php');

        preg_match('#\$user = "(.*)"#', $fileConsult, $matches);
        $userConsult = $matches[1];

        preg_match('#\$pwd = "(.*)"#', $fileConsult, $matches);
        $passwordConsult = $matches[1];

        //on lit le fichier et on récupère les valeurs de connexion
        //$fileRembo = file_get_contents('ExpectDefs.php');

        //preg_match('#\$rembo_server = "(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})"#', $fileRembo, $matches);
        //$hostRembo = $matches[1];

        //preg_match('#\$netclnt_program = "(.*)"#', $fileRembo, $matches);
        //$netclient = $matches[1];


        $link = mysql_connect($host, $_POST['login'], $_POST['password']) or die('Veuillez entrer des identifiants valides');

        //suite du traitement
        if ($_POST['typeInstall'] === 'update') {

            // sélection de la db
            mysql_select_db($db, $link);


            // On dump chaque fichier sql de mise à jour
            $cumulatif = false;

            foreach ($tab_maj as $ancienne_version => $maj_sql) {

                if ($ancienne_version == $_POST['version'] || $cumulatif) {

                    $cumulatif=true;
                    importe_dump("DB_DUMPS/$maj_sql");
                }
            }

        } else if ($_POST['typeInstall'] === 'new') {

            //creéation de la base "jeddlaj"

            $sql = 'CREATE DATABASE '.$db;

            if (mysql_query($sql, $link)) {

                echo 'Base de données créée correctement<br />';

            } else {

                echo 'Erreur lors de la création de la base de données : '.mysql_error().'<br />';
            }

            // sélection de la db
            mysql_select_db($db, $link);

            //On dump la base
            importe_dump("DB_DUMPS/pyddlaj.sql");

        }

        //on vérifie si getHostByAddr retourne un hote ou une adresse IP
        if (preg_match('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#', $host)) {

            $hostname = gethostbyaddr($host);

        }

        //if (preg_match('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#', $hostRembo)) {

//            $hostnameRembo = gethostbyaddr($hostRembo);
//        }

        //Création d'un tableau d'host qui contient le nom DNS de l'hote et l'adresse IP de l'host
        $arrayHost = array($host, $hostname, $hostRembo, $hostnameRembo);

        
        mysql_select_db('mysql', $link);
        //Création de l'utilisateur multi hote nécéssaire pour les connexions depuis les postes clients
        $query = "grant all privileges on $db.* to  '$user'@'%' identified by '$password')";
        echo "<br>La requete : $query<br>";
        mysql_query($query);
        
        
        /*foreach ($arrayHost as $host) {

            //On créer les users
            //On vérifie par rapport au fichier existant, l'existance des user rembo et rembo_consult
            mysql_select_db('mysql', $link);

            // gestion de l'utilisateur rembo
            $query = 'INSERT INTO `user` (Host, User, Password) '.
                     'VALUES ("'.$host.'", "'.$user.'", PASSWORD("'.$password.'")) '.
                     'ON DUPLICATE KEY UPDATE Password = PASSWORD("'.$password.'");';

            mysql_query($query);

            $query = 'INSERT INTO `db` (Host, Db, User, Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv) '.
                     'VALUES ("'.$host.'", "'.$db.'", "'.$user.'", "Y", "Y", "Y", "Y", "Y", "Y") '.
                     'ON DUPLICATE KEY UPDATE Host = "'.$host.'", Db = "'.$db.'", Select_priv = "Y", Insert_priv = "Y", Update_priv = "Y", Delete_priv = "Y", Create_priv = "Y", Drop_priv = "Y";';

            mysql_query($query);

            $query = 'FLUSH PRIVILEGES;';

            mysql_query($query);




            if ($host != $hostRembo) {

                //gestion de l'utilisateur consult
                $queryConsult = 'INSERT INTO `user` (Host, User, Password) '.
                                'VALUES ("'.$host.'", "'.$userConsult.'", PASSWORD("'.$passwordConsult.'")) '.
                                'ON DUPLICATE KEY UPDATE Password = PASSWORD("'.$passwordConsult.'");';

                mysql_query($queryConsult);

                $queryConsult = 'INSERT INTO `db` (Host, Db, User, Select_priv, Insert_priv, Update_priv, Delete_priv) '.
                                'VALUES ("'.$host.'", "'.$db.'", "'.$userConsult.'", "Y", "N", "N", "N") '.
                                'ON DUPLICATE KEY UPDATE Host = "'.$host.'", Db = "'.$db.'";';

                mysql_query($queryConsult);

                $queryConsult = 'FLUSH PRIVILEGES;';


                mysql_query($queryConsult);
            }
        }*/

        mysql_close($link);

        //suppression du fichier ExpectDefs.php si le netclient n'est pas utilisé
        //if ($netclient === '') {
		//
        //    unlink('ExpectDefs.php');
        //}


        //ajout du droits d'execution à l'utilisateur pour les fichiers *.expect
        foreach (glob("*.expect") as $filename) {

            chmod($filename, 0755);
        }



        //génération du htaccess et htpasswd
        /*if (file_exists('#protection#')) {

            unlink('#protection#');

            $htaccess = "AuthName \"JeDDLaJ\"\nAuthType Basic\nAuthUserFile \"".dirname(__FILE__)."/.htpasswd\"\nRequire valid-user";
            $htpasswd = $user.':'.crypt($password);

            file_put_contents('.htaccess', $htaccess);
            file_put_contents('.htpasswd', $htpasswd);

            //substitution dans pour DBParDefaut
            $file = file_get_contents('DBParDefaut.php');

            $file = preg_replace('#\$pwd = "(.*)"#', '$pwd = "$_SERVER[PHP_AUTH_PW]"', $file);

            file_put_contents('DBParDefaut.php', $file);

            //substitution dans ExpectDef.php
            if (file_exists('ExpectDefs.php')) {

                $fileExpect = file_get_contents('ExpectDefs.php');

                preg_match('#\$rembo_passwd = "(.*)"#', $fileExpect, $matches);
                $pwdRembo = $matches[1];

                //est-ce que ça ça ne peut pas être gérer dans le fichier initParamServer
                if ($pwdRembo === $password) {

                    $fileExpect = preg_replace('#\$rembo_passwd = "(.*)"#', '$rembo_passwd = "$_SERVER[PHP_AUTH_PW]"', $fileExpect);

                    file_put_contents('ExpectDefs.php', $fileExpect);
                }
            }

        }*/

        echo '<strong>Installation terminée</strong>';
    }
}

?>
