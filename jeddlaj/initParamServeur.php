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


$formExpectValid = '';
$formPathJeddlaj = '';
$formPrefValid   = '';


//traitement du fichier ExpectDefs.php
//récupération des paramètres si ils existent dans le fichier ExpectDefs.php
if (is_file('ExpectDefs.php')) {

    $fileExpect = file_get_contents('ExpectDefs.php');
        
    preg_match('#\$rembo_server = "(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})"#', $fileExpect, $matches);
    $adresseIP = $matches[1];
    

    preg_match('#\$rembo_passwd = "(.*)"#', $fileExpect, $matches);
    $pwdRembo = $matches[1];

    preg_match('#\$netclnt_program = "(.*)"#', $fileExpect, $matches);
    $path = $matches[1];

 } else {   

    $fileExpect = file_get_contents('ExpectDefs.php.dist');

    $adresseIP = '';
    $pwdRembo = '';
    $path = '';
 }

//remplissage du fichier ExpectDefs.php avec les données du formulaire
if (isset($_POST['adresseIP']) && isset($_POST['pwdRembo'])) {

    if ($_POST['adresseIP'] !== '' && $_POST['pwdRembo'] !== '') {

        $fileExpect = preg_replace('#\$rembo_server = "(.*)"#', '$rembo_server = "'.$_POST['adresseIP'].'"', $fileExpect);


        //on lit le fichier et on récupère les valeurs de connexion
        $file = file_get_contents('DBParDefaut.php');

        preg_match('#\$pwd = "(.*)"#', $file, $matches);
        $password = $matches[1];

        $fileExpect = preg_replace('#\$rembo_passwd = "(.*)"#', '$rembo_passwd = "'.$_POST['pwdRembo'].'"', $fileExpect);

        $fileExpect = preg_replace('#\$netclnt_program = "(.*)"#', '$netclnt_program = "'.$_POST['path'].'"', $fileExpect);

        file_put_contents('ExpectDefs.php', $fileExpect);

        $formExpectValid = true;

    } else {

        echo 'Veuille renseigner l\'adresse IP et le password du serveur rembo';
    }
 }


//traitement du fichier jeddlaj.shtml
//récupération des paramètres si ils existent dans le fichier jeddlaj.shtml
if (is_file('rembo/jeddlaj.shtml')) {

    $fileJeddlaj = file_get_contents('rembo/jeddlaj.shtml');

    preg_match('#str RemboJeDDLaJScriptsDir="cache:\/\/global\/(.*)"#', $fileJeddlaj, $matches);
    $pathJeddlaj = $matches[1];

 } else {   

    $fileJeddlaj = file_get_contents('rembo/jeddlaj.shtml.dist');

    $pathJeddlaj = '';
 }

//remplissage du fichier jeddlaj.shtml avec les données du formulaire
if (isset($_POST['pathJeddlaj'])) {

    if ($_POST['pathJeddlaj'] !== '') {

        $fileJeddlaj = preg_replace('#str RemboJeDDLaJScriptsDir="cache:\/\/global\/(.*)"#', 'str RemboJeDDLaJScriptsDir="cache://global/'.$_POST['pathJeddlaj'].'"', $fileJeddlaj);


        file_put_contents('rembo/jeddlaj.shtml', $fileJeddlaj);

        $formPathJeddlaj = true;

    } else {

        echo 'Veuillez renseigner le chemin vers vos script JeDDLaJ';
    }
 }


//traitement du fichier preferences.rbc
//récupération des paramètres si il existe dans le fichier preferences.rbc
if (is_file('rembo/preferences.rbc')) {

    $filePref = file_get_contents('rembo/preferences.rbc');

    preg_match('#str RemboImagesDir="cache://global/(.*)"#', $filePref, $matches);
    $imagesDir = $matches[1];

    preg_match('#str RemboSnapshotsDir="cache://global/(.*)"#', $filePref, $matches);
    $snapshotsDir = $matches[1];

    preg_match('#str RemboIDBDir="cache://global/(.*)"#', $filePref, $matches);
    $IDBDir = $matches[1];

    preg_match('#str RemboPackagesDir="cache://global/(.*)"#', $filePref, $matches);
    $packagesDir = $matches[1];

    preg_match('#str RemboPostInstScriptsDir="cache://global/(.*)"#', $filePref, $matches);
    $postInstScriptsDir = $matches[1];

    preg_match('#str RemboPreDeinstScriptsDir="cache://global/(.*)"#', $filePref, $matches);
    $preDeinstScriptsDir = $matches[1];

    if (preg_match('#bool SlavesReadOnly=true;#', $filePref)) {

            $checkedSlaves = 'checked="checked"';

    } else {

        $checkedSlaves = '';
    }

    preg_match('#str RemboServerNamePath=RemboJeDDLaJScriptsDir\+"(.*)"#', $filePref, $matches);
    $serverNamePath = $matches[1];

    if (file_exists('rembo/'.$serverNamePath)) {

        $nomServeur = file_get_contents('rembo/'.$serverNamePath);

    } else {

        $nomServeur = '';
    }

    preg_match('#str RemboMasterServerName="(.*)"#', $filePref, $matches);
    $masterServerName = $matches[1];

    preg_match('#str RemboMasterIP="(.*)"#', $filePref, $matches);
    $masterIP = $matches[1];

    if (preg_match('#bool compatibilite_rembo=true;#', $filePref)) {

        $checkedCompatibilite = 'checked="checked"';

    } else {

        $checkedCompatibilite = '';
    }

    if (preg_match('#bool linux_detection=true;#', $filePref)) {

        $checkedDetection = 'checked="checked"';

    } else {

        $checkedDetection = '';
    }

    if (preg_match('#bool detection_subsystem=true;#', $filePref)) {

        $checkedSubsystem = 'checked="checked"';

    } else {

        $checkedSubsystem = '';
    }

    preg_match('#str HardwareDetectionDir="(.*)"#', $filePref, $matches);
    $hardwareDetectionDir = $matches[1];

    if (preg_match('#bool RealDeinstallation=true;#', $filePref)) {

        $checkedDeinstallation = 'checked="checked"';

    } else {

        $checkedDeinstallation = '';
    }

    if (preg_match('#bool UseDHCPInfo=true;#', $filePref)) {

        $checkedDHCP = 'checked="checked"';

    } else {

        $checkedDHCP = '';
    }

    if (preg_match('#bool BootIfNoConnection=true;#', $filePref)) {

        $checkedNoConnection = 'checked="checked"';

    } else {

        $checkedNoConnection = '';
    }

    preg_match('#str EmailAdmin="(.*)"#', $filePref, $matches);
    $emailAdmin = $matches[1];

    preg_match('#str EmailFrom="(.*)"#', $filePref, $matches);
    $emailFrom = $matches[1];

    preg_match('#int ShutdownDelay=(.*);#', $filePref, $matches);
    $shutdownDelay = $matches[1];

 } else {   

    $filePref = file_get_contents('rembo/preferences.rbc.dist');

    preg_match('#str RemboImagesDir="cache://global/(.*)"#', $filePref, $matches);
    $imagesDir = $matches[1];

    preg_match('#str RemboSnapshotsDir="cache://global/(.*)"#', $filePref, $matches);
    $snapshotsDir = $matches[1];

    preg_match('#str RemboIDBDir="cache://global/(.*)"#', $filePref, $matches);
    $IDBDir = $matches[1];

    preg_match('#str RemboPackagesDir="cache://global/(.*)"#', $filePref, $matches);
    $packagesDir = $matches[1];

    preg_match('#str RemboPostInstScriptsDir="cache://global/(.*)"#', $filePref, $matches);
    $postInstScriptsDir = $matches[1];

    preg_match('#str RemboPreDeinstScriptsDir="cache://global/(.*)"#', $filePref, $matches);
    $preDeinstScriptsDir = $matches[1];

    $checkedSlaves         = '';
    $serverNamePath        = '';
    $nomServeur            = '';
    $masterServerName      = '';
    $masterIP              = '';
    $checkedCompatibilite  = '';
    $checkedDetection      = '';
    $checkedSubsystem      = '';

    preg_match('#str HardwareDetectionDir="(.*)"#', $filePref, $matches);
    $hardwareDetectionDir = $matches[1];

    $checkedDeinstallation = '';
    $checkedDHCP           = '';
    $checkedNoConnection   = '';
    $checkedDNS            = '';
    $emailAdmin            = '';
    $emailFrom             = '';

    preg_match('#int ShutdownDelay=(.*);#', $filePref, $matches);
    $shutdownDelay = $matches[1];

}



//remplissage du fichier preferences.rbc avec les données du formulaire
if (isset($_POST['imagesDir']) && isset($_POST['snapshotsDir']) && isset($_POST['IDBDir']) && isset($_POST['packagesDir']) && isset($_POST['postInstScriptsDir']) && isset($_POST['preDeinstScriptsDir']) && isset($_POST['emailAdmin']) && isset($_POST['emailFrom']) && isset($_POST['shutdownDelay'])) {

    if ($_POST['imagesDir'] !== '' && $_POST['snapshotsDir'] !== '' && $_POST['IDBDir'] !== '' && $_POST['packagesDir'] !== '' && $_POST['postInstScriptsDir'] !== '' && $_POST['preDeinstScriptsDir'] !== '' && $_POST['emailAdmin'] !== '' && $_POST['emailFrom'] !== '' && $_POST['shutdownDelay'] !== '') {

        $file = file_get_contents('DBParDefaut.php');

        preg_match('#\$user = "(.*)"#', $file, $matches);
        $user = $matches[1];

        preg_match('#\$pwd = "(.*)"#', $file, $matches);
        $password = $matches[1];
        
        preg_match('#\$host = "(.*)"#', $file, $matches);
        $host = $matches[1];

        preg_match('#\$db = "(.*)"#', $file, $matches);
        $db = $matches[1];

        if ($_POST['connecteur'] === 'odbc') {

            $filePref = preg_replace('#\/*str MySQLDB="\w*";#', 'str MySQLDB="'.$db.'";', $filePref);

            $filePref = preg_replace('#\/*str MySQLDB="mysql:\/\/.*\/.*";#', '//str MySQLDB="mysql://MOT_DE_PASSE_MYSQL_DE_L_UTILISATEUR_rembo/jeddlaj";', $filePref);

        } else if ($_POST['connecteur'] === 'jdbc') {

            $filePref = preg_replace('#\/*str MySQLDB="\w*";#', '//str MySQLDB="jeddlaj";', $filePref);

            $filePref = preg_replace('#\/*str MySQLDB="mysql:\/\/\w*\/\w*";#', 'str MySQLDB="mysql://'.$host.'/'.$db.'";', $filePref);

        }

        $filePref = preg_replace('#str MySQLUser="\w*"#', 'str MySQLUser="'.$user.'"', $filePref);

        $filePref = preg_replace('#str MySQLPassword="\w*"#', 'str MySQLPassword="'.$password.'"', $filePref);


        $filePref = preg_replace('#str RemboImagesDir="cache://global/(.*)"#', 'str RemboImagesDir="cache://global/'.$_POST['imagesDir'].'"', $filePref);

        $filePref = preg_replace('#str RemboSnapshotsDir="cache://global/(.*)"#', 'str RemboSnapshotsDir="cache://global/'.$_POST['snapshotsDir'].'"', $filePref);

        $filePref = preg_replace('#str RemboIDBDir="cache://global/(.*)"#', 'str RemboIDBDir="cache://global/'.$_POST['IDBDir'].'"', $filePref);

        $filePref = preg_replace('#str RemboPackagesDir="cache://global/(.*)"#', 'str RemboPackagesDir="cache://global/'.$_POST['packagesDir'].'"', $filePref);

        $filePref = preg_replace('#str RemboPostInstScriptsDir="cache://global/(.*)"#', 'str RemboPostInstScriptsDir="cache://global/'.$_POST['postInstScriptsDir'].'"', $filePref);

        $filePref = preg_replace('#str RemboPreDeinstScriptsDir="cache://global/(.*)"#', 'str RemboPreDeinstScriptsDir="cache://global/'.$_POST['preDeinstScriptsDir'].'"', $filePref);

        if (isset($_POST['slavesReadOnly'])) {

            $filePref = preg_replace('#bool SlavesReadOnly=\w*;#', 'bool SlavesReadOnly=true;', $filePref);

            if ($_POST['serverNamePath'] !== '' && $_POST['nomServeur'] !== '' && $_POST['masterServerName']) {

                $filePref = preg_replace('#str RemboServerNamePath=RemboJeDDLaJScriptsDir\+"(.*)"#', 'str RemboServerNamePath=RemboJeDDLaJScriptsDir+"'.$_POST['serverNamePath'].'"', $filePref);

                file_put_contents('rembo/'.$_POST['serverNamePath'], $_POST['nomServeur']);

                $filePref = preg_replace('#str RemboMasterServerName="(.*)"#', 'str RemboMasterServerName="'.$_POST['masterServerName'].'"', $filePref);

            } else {

                echo 'Veuillez remplir tous les champs de cette partie';
            }
            
        } else {

            $filePref = preg_replace('#bool SlavesReadOnly=\w*;#', 'bool SlavesReadOnly=false;', $filePref);
        }
        

        $filePref = preg_replace('#str RemboMasterIP="(.*)"#', 'str RemboMasterIP="'.$_POST['masterIP'].'"', $filePref);


        if (isset($_POST['compatibilite'])) {

            $filePref = preg_replace('#bool compatibilite_rembo=\w*;#', 'bool compatibilite_rembo=true;', $filePref);

        } else {

            $filePref = preg_replace('#bool compatibilite_rembo=\w*;#', 'bool compatibilite_rembo=false;', $filePref);
        }


        if (isset($_POST['linuxDetection'])) {

            $filePref = preg_replace('#bool linux_detection=\w*;#', 'bool linux_detection=true;', $filePref);

            if (isset($_POST['detectionSubSystem']) && $_POST['hardwareDetectionDir'] !== '') {

                $filePref = preg_replace('#bool detection_subsystem=\w*;#', 'bool detection_subsystem=true;', $filePref);

                $filePref = preg_replace('#str HardwareDetectionDir="(.*)"#', 'str HardwareDetectionDir="'.$_POST['hardwareDetectionDir'].'"', $filePref);

            } else {

                echo 'Veuillez sélectioner la détection des subsytems et renseigner le répertoire où seront stockés temporairement la liste des composants détectés';
            }

        } else {

            $filePref = preg_replace('#bool linux_detection=\w*;#', 'bool linux_detection=false;', $filePref);

            $filePref = preg_replace('#bool detection_subsystem=\w*;#', 'bool detection_subsystem=false;', $filePref);
        }


        if ($_POST['masterIP'] !== '' || isset($_POST['linuxDetection'])) {

            $filePref = preg_replace('#str netclnt_password=".*"#', 'str netclnt_password="'.$_POST['pwdRembo'].'"', $filePref);

        } else {

            $filePref = preg_replace('#str netclnt_password=".*"#', 'str netclnt_password="MOT_DE_PASSE_DE_L_ADMINISTRATEUR_REMBO"', $filePref);
        }


        if (isset($_POST['realDeinstallation'])) {

            $filePref = preg_replace('#bool RealDeinstallation=\w*;#', 'bool RealDeinstallation=true;', $filePref);

        } else {

            $filePref = preg_replace('#bool RealDeinstallation=\w*;#', 'bool RealDeinstallation=false;', $filePref);
        }


        if (isset($_POST['useDHCPInfo'])) {

            $filePref = preg_replace('#bool UseDHCPInfo=\w*;#', 'bool UseDHCPInfo=true;', $filePref);

        } else {

            $filePref = preg_replace('#bool UseDHCPInfo=\w*;#', 'bool UseDHCPInfo=false;', $filePref);
        }


        if (isset($_POST['bootIfNoConnection'])) {

            $filePref = preg_replace('#bool BootIfNoConnection=\w*;#', 'bool BootIfNoConnection=true;', $filePref);

        } else {

            $filePref = preg_replace('#bool BootIfNoConnection=\w*;#', 'bool BootIfNoConnection=false;', $filePref);
        }


        $filePref = preg_replace('#str EmailAdmin="(.*)"#', 'str EmailAdmin="'.$_POST['emailAdmin'].'"', $filePref);

        $filePref = preg_replace('#str EmailFrom="(.*)"#', 'str EmailFrom="'.$_POST['emailFrom'].'"', $filePref);

        $filePref = preg_replace('#int ShutdownDelay=(.*);#', 'int ShutdownDelay='.$_POST['shutdownDelay'].';', $filePref);



        file_put_contents('rembo/preferences.rbc', $filePref);

        $formPrefValid = true;

    } else {

        echo 'Veuille renseigner tous les champs du formulaire';
    }
 }

if ($formExpectValid && $formPathJeddlaj && $formPrefValid) {

    header('Location:choixInstall.php');
 }

entete("Frédéric Bloise & Gérard Milhaud & Arnaud Salvucci : dosicalu@univmed.fr", "CSS/g.css", "JeDDLaJ : setup");

echo '<h1>JeDDLaJ Setup</h1>';

echo '<div>';

echo '<p><span style="color:red;">*</span> Champs Obligatoire</p>';

echo '<fieldset>';
echo '<legend>Identifiant de connexion au serveur Rembo</legend>';

echo '<form action="initParamServeur.php" method="post">';

echo '<p>';
echo '<label for="adresseIP">Adresse IP du serveur Rembo <span style="color:red;">*</span> : </label>';
echo '<input type="text" name="adresseIP" id="adresseIP" value="'.$adresseIP.'" />';
echo '</p>';

echo '<p>';
echo '<label for="pwdRembo">Password du serveur Rembo <span style="color:red;">*</span> : </label>';
echo '<input type="password" name="pwdRembo" id="pwdRembo" value="'.$pwdRembo.'" />';
echo '</p>';

echo '<p>';
echo '<label for="path">Path vers le netClient : </label>';
echo '<input type="text" name="path" id="path" value="'.$path.'" />';
echo '<p>';

echo '</fieldset>';


echo '<fieldset>';
echo '<legend>Répertoire contenant les scripts JeDDLaJ</legentd>';

echo '<p>';
echo '<label for="pathJeddlaj">Chemin vers vos script <span style="color:red;">*</span> : cache://global/</label>';
echo '<input type="text" name="pathJeddlaj" id="pathJeddlaj" value="'.$pathJeddlaj.'" />';
echo '</p>';

echo '</fieldset>';


echo '<fieldset>';
echo '<legend>Preférence du serveur Rembo</legend>';

echo '<p>';
echo 'Type de connecteur à la base de données <span style="color:red;">*</span> : <br />';
echo '<input type="radio" value="jdbc" name="connecteur" id="jdbc" checked="checked" /><label for="jdbc">JDBC</label>';
echo '<input type="radio" value="odbc" name="connecteur" id="odbc" /><label for="odbc">ODBC</label>';
echo '</p>';

echo '<fieldset>';
echo '<legend>Répertoires où seront stockés les fichiers JeDDLaJ sur le serveur Rembo</legend>';

echo '<p>';
echo '<label for="imagesDir">Répertoire contenant les logos et icones du JeDDLaJ <span style="color:red;">*</span> : cache://global/</label>';
echo '<input type="text" name="imagesDir" id="imagesDir" value="'.$imagesDir.'" />';
echo '</p>';

echo '<p>';
echo '<label for="snapshotsDir">Répertoire où sont stockés temporairement les incrémentaux lors de la création de patckages <span style="color:red;">*</span> : cache://global/</label>';
echo '<input type="text" name="snapshotsDir" id="snapshotsDir" value="'.$snapshotsDir.'" />';
echo '</p>';

echo '<p>';
echo '<label for="IDBDir">Répertoire des images de base <span style="color:red;">*</span> : cache://global/</label>';
echo '<input type="text" name="IDBDir" id="IDBDir" value="'.$IDBDir.'" />';
echo '</p>';

echo '<p>';
echo '<label for="packagesDir">Répertoire des packages <span style="color:red;">*</span> : cache://global/</label>';
echo '<input type="text" name="packagesDir" id="packagesDir" value="'.$packagesDir.'" />';
echo '</p>';

echo '<p>';
echo '<label for="postInstScriptsDir">Répertoire des postinstall scripts <span style="color:red;">*</span> : cache://global/</label>';
echo '<input type="text" name="postInstScriptsDir" id="postInstScriptsDir" value="'.$postInstScriptsDir.'" />';
echo '</p>';

echo '<p>';
echo '<label for="preDeinstScriptsDir">Répertoire des pré deinstall scripts <span style="color:red;">*</span> : cache://global/</label>';
echo '<input type="text" name="preDeinstScriptsDir" id="preDeinstScriptsDir" value="'.$preDeinstScriptsDir.'" />';
echo '</p>';

echo '</fieldset>';


echo '<fieldset>';
echo '<legend>Variables concernant le ou les serveurs Rembo</legend>';

echo '<p>';
echo '<label for="slavesReadOnly">Slaves Read Only </label>';
echo '<input type="checkbox" name="slavesReadOnly" id="slaveReadOnly" value="true" '.$checkedSlaves.' />';
echo '</p>';

echo '<p>';
echo '<label for="serverNamePath">Nom du fichier contenant le nom du serveur Rembo </label>';
echo '<input type="text" name="serverNamePath" id="serverNamePath" value="'.$serverNamePath.'" />';
echo '</p>';

echo '<p>';
echo '<label for="nomServeur">Nom du serveur </label>';
echo '<input type="text" name="nomServeur" id="nomServeur" value="'.$nomServeur.'" />';
echo '</p>';

echo '<p>';
echo '<label for="masterServerName">Nom du serveur maître </label>';
echo '<input type="text" name="masterServerName" id="masterServerName" value="'.$masterServerName.'" />';
echo '</p>';

echo '<p>';
echo '<label for="masterIP">Adresse IP du serveur maître </label>';
echo '<input type="text" name="masterIP" id="masterIP" value="'.$masterIP.'" />';
echo '</p>';

echo '</fieldset>';


echo '<fieldset>';
echo '<legend>Variables concernant la detection</legend>';

echo '<p>';
echo '<label for="compatibilite">Compatibilité Rembo </label>';
echo '<input type="checkbox" name="compatibilite" id="compatibilite" value="true" '.$checkedCompatibilite.' />';
echo '</p>';

echo '<p>';
echo '<label for="linuxDetection">Détection linux </label>';
echo '<input type="checkbox" name="linuxDetection" id="linuxDetection" value="true" '.$checkedDetection.' />';
echo '</p>';

echo '<p>';
echo '<label for="detectionSubSystem">Détection des subsystem (sous linux)</label>';
echo '<input type="checkbox" name="detectionSubSystem" id="detectionSubSystem" value="true" '.$checkedSubsystem.' />';
echo '</p>';

echo '<p>';
echo '<label for="hardwareDetectionDir">Répertoire où seront stockés temporairement la liste des composants détectés </label>';
echo '<input type="text" name="hardwareDetectionDir" id="hardwareDetectionDir" value="'.$hardwareDetectionDir.'" />';
echo '</p>';

echo '</fieldset>';


echo '<fieldset>';
echo '<legend>Autres variables</legend>';

echo '<p>';
echo '<label for="realDeinstallation">Vraie désinstallation (pas par resynchronisation) : </label>';
echo '<input type="checkbox" name="realDeinstallation" id="realDeinstallation" value="true" '.$checkedDeinstallation.' />';
echo '</p>';

echo '<p>';
echo '<label for="useDHCPInfo">Utilisation de la fonction RequestDHCPInfo : </label>';
echo '<input type="checkbox" name="useDHCPInfo" id="useDHCPInfo" value="true" '.$checkedDHCP.' />';
echo '</p>';

echo '<p>';
echo '<label for="bootIfNoConnection">Faut-il faire booter la machine si la connexion à MySQL ne marche pas ? : </label>';
echo '<input type="checkbox" name="bootIfNoConnection" id="bootIfNoConnection" value="true" '.$checkedNoConnection.' />';
echo '</p>';

echo '<p>';
echo '<label for="emailAdmin">Email où seront envoyés les logs Rembo en cas d\'erreur <span style="color:red;">*</span> : </label>';
echo '<input type="text" name="emailAdmin" id="emailAdmin" value="'.$emailAdmin.'" />';
echo '</p>';

echo '<p>';
echo '<label for="emailFrom">Expéditeur des logs Rembo en cas d\'erreur <span style="color:red;">*</span> : </label>';
echo '<input type="text" name="emailFrom" id="emailFrom" value="'.$emailFrom.'" />';
echo '</p>';

echo '<p>';
echo '<label for="shutdownDelay">Temps au bout duquel le PC s\'éteint automatiquent en cas d\'attente d\'un multiboot <br /> Temps en centième de seconde, 0 pour désactiver l\'extinction automatique <span style="color:red;">*</span> : </label>';
echo '<input type="text" name="shutdownDelay" id="shutdownDelay" value="'.$shutdownDelay.'" />';
echo '</p>';

echo '</fieldset>';

echo '<p>';
echo '<input type="submit" name="initialiser" value="Initialiser les paramètres du serveur Rembo" />';
echo '</p>';

echo '</form>';
echo '</fieldset>';


echo '</div>';


PiedPage();
?>
