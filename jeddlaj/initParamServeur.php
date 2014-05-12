<?php
/**
 * ************************** GPL STUFF **********************************
 *
 * ********************************* ENGLISH *********************************
 * 
 * --- Copyright notice :
 * 
 * Copyright 2003, 2004, 2005 G�rard Milhaud - Fr�d�ric Bloise
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
 * *********** TRADUCTION FRAN�AISE PERSONNELLE SANS VALEUR L�GALE ***********
 *
 * --- Notice de Copyright :
 * 
 * Copyright 2003, 2004, 2005 G�rard Milhaud - Fr�d�ric Bloise
 * Copyright 2010, 2011  Fr�d�ric Bloise - G�rard Milhaud - Arnaud Salvucci
 * 
 * 
 * --- D�claration de permission de copie
 * 
 * Ce fichier fait partie de JeDDLaJ.
 * 
 * JeDDLaJ est un logiciel libre : vous pouvez le redistribuer ou le modifier
 * selon les termes de la Licence Publique G�n�rale GNU telle qu'elle est
 * publi�e par la Free Software Foundation ; soit la version 2 de la Licence,
 * soit (� votre choix) une quelconque version ult�rieure.
 * 
 * JeDDLaJ est distribu� dans l'espoir qu'il soit utile, mais SANS AUCUNE
 * GARANTIE ; sans m�me la garantie implicite de COMMERCIALISATION ou 
 * d'ADAPTATION DANS UN BUT PARTICULIER. Voir la Licence publique G�n�rale GNU
 * pour plus de d�tails.
 * 
 * Vous devriez avoir re�u une copie de la Licence Publique G�n�rale GNU avec 
 * JeDDLaJ ; si �a n'�tait pas le cas, �crivez � la Free Software Foundation,
 * Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * 
 * ******************* END OF GPL STUFF ***************************************
 */
include("UtilsHTML.php");


$formExpectValid = '';
$formPathJeddlaj = '';
$formPrefValid   = '';


//traitement du fichier ExpectDefs.php
//r�cup�ration des param�tres si ils existent dans le fichier ExpectDefs.php
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

//remplissage du fichier ExpectDefs.php avec les donn�es du formulaire
if (isset($_POST['adresseIP']) && isset($_POST['pwdRembo'])) {

    if ($_POST['adresseIP'] !== '' && $_POST['pwdRembo'] !== '') {

        $fileExpect = preg_replace('#\$rembo_server = "(.*)"#', '$rembo_server = "'.$_POST['adresseIP'].'"', $fileExpect);


        //on lit le fichier et on r�cup�re les valeurs de connexion
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
//r�cup�ration des param�tres si ils existent dans le fichier jeddlaj.shtml
if (is_file('rembo/jeddlaj.shtml')) {

    $fileJeddlaj = file_get_contents('rembo/jeddlaj.shtml');

    preg_match('#str RemboJeDDLaJScriptsDir="cache:\/\/global\/(.*)"#', $fileJeddlaj, $matches);
    $pathJeddlaj = $matches[1];

 } else {   

    $fileJeddlaj = file_get_contents('rembo/jeddlaj.shtml.dist');

    $pathJeddlaj = '';
 }

//remplissage du fichier jeddlaj.shtml avec les donn�es du formulaire
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
//r�cup�ration des param�tres si il existe dans le fichier preferences.rbc
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



//remplissage du fichier preferences.rbc avec les donn�es du formulaire
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

                echo 'Veuillez s�lectioner la d�tection des subsytems et renseigner le r�pertoire o� seront stock�s temporairement la liste des composants d�tect�s';
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

entete("Fr�d�ric Bloise & G�rard Milhaud & Arnaud Salvucci : dosicalu@univmed.fr", "CSS/g.css", "JeDDLaJ : setup");

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
echo '<legend>R�pertoire contenant les scripts JeDDLaJ</legentd>';

echo '<p>';
echo '<label for="pathJeddlaj">Chemin vers vos script <span style="color:red;">*</span> : cache://global/</label>';
echo '<input type="text" name="pathJeddlaj" id="pathJeddlaj" value="'.$pathJeddlaj.'" />';
echo '</p>';

echo '</fieldset>';


echo '<fieldset>';
echo '<legend>Pref�rence du serveur Rembo</legend>';

echo '<p>';
echo 'Type de connecteur � la base de donn�es <span style="color:red;">*</span> : <br />';
echo '<input type="radio" value="jdbc" name="connecteur" id="jdbc" checked="checked" /><label for="jdbc">JDBC</label>';
echo '<input type="radio" value="odbc" name="connecteur" id="odbc" /><label for="odbc">ODBC</label>';
echo '</p>';

echo '<fieldset>';
echo '<legend>R�pertoires o� seront stock�s les fichiers JeDDLaJ sur le serveur Rembo</legend>';

echo '<p>';
echo '<label for="imagesDir">R�pertoire contenant les logos et icones du JeDDLaJ <span style="color:red;">*</span> : cache://global/</label>';
echo '<input type="text" name="imagesDir" id="imagesDir" value="'.$imagesDir.'" />';
echo '</p>';

echo '<p>';
echo '<label for="snapshotsDir">R�pertoire o� sont stock�s temporairement les incr�mentaux lors de la cr�ation de patckages <span style="color:red;">*</span> : cache://global/</label>';
echo '<input type="text" name="snapshotsDir" id="snapshotsDir" value="'.$snapshotsDir.'" />';
echo '</p>';

echo '<p>';
echo '<label for="IDBDir">R�pertoire des images de base <span style="color:red;">*</span> : cache://global/</label>';
echo '<input type="text" name="IDBDir" id="IDBDir" value="'.$IDBDir.'" />';
echo '</p>';

echo '<p>';
echo '<label for="packagesDir">R�pertoire des packages <span style="color:red;">*</span> : cache://global/</label>';
echo '<input type="text" name="packagesDir" id="packagesDir" value="'.$packagesDir.'" />';
echo '</p>';

echo '<p>';
echo '<label for="postInstScriptsDir">R�pertoire des postinstall scripts <span style="color:red;">*</span> : cache://global/</label>';
echo '<input type="text" name="postInstScriptsDir" id="postInstScriptsDir" value="'.$postInstScriptsDir.'" />';
echo '</p>';

echo '<p>';
echo '<label for="preDeinstScriptsDir">R�pertoire des pr� deinstall scripts <span style="color:red;">*</span> : cache://global/</label>';
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
echo '<label for="masterServerName">Nom du serveur ma�tre </label>';
echo '<input type="text" name="masterServerName" id="masterServerName" value="'.$masterServerName.'" />';
echo '</p>';

echo '<p>';
echo '<label for="masterIP">Adresse IP du serveur ma�tre </label>';
echo '<input type="text" name="masterIP" id="masterIP" value="'.$masterIP.'" />';
echo '</p>';

echo '</fieldset>';


echo '<fieldset>';
echo '<legend>Variables concernant la detection</legend>';

echo '<p>';
echo '<label for="compatibilite">Compatibilit� Rembo </label>';
echo '<input type="checkbox" name="compatibilite" id="compatibilite" value="true" '.$checkedCompatibilite.' />';
echo '</p>';

echo '<p>';
echo '<label for="linuxDetection">D�tection linux </label>';
echo '<input type="checkbox" name="linuxDetection" id="linuxDetection" value="true" '.$checkedDetection.' />';
echo '</p>';

echo '<p>';
echo '<label for="detectionSubSystem">D�tection des subsystem (sous linux)</label>';
echo '<input type="checkbox" name="detectionSubSystem" id="detectionSubSystem" value="true" '.$checkedSubsystem.' />';
echo '</p>';

echo '<p>';
echo '<label for="hardwareDetectionDir">R�pertoire o� seront stock�s temporairement la liste des composants d�tect�s </label>';
echo '<input type="text" name="hardwareDetectionDir" id="hardwareDetectionDir" value="'.$hardwareDetectionDir.'" />';
echo '</p>';

echo '</fieldset>';


echo '<fieldset>';
echo '<legend>Autres variables</legend>';

echo '<p>';
echo '<label for="realDeinstallation">Vraie d�sinstallation (pas par resynchronisation) : </label>';
echo '<input type="checkbox" name="realDeinstallation" id="realDeinstallation" value="true" '.$checkedDeinstallation.' />';
echo '</p>';

echo '<p>';
echo '<label for="useDHCPInfo">Utilisation de la fonction RequestDHCPInfo : </label>';
echo '<input type="checkbox" name="useDHCPInfo" id="useDHCPInfo" value="true" '.$checkedDHCP.' />';
echo '</p>';

echo '<p>';
echo '<label for="bootIfNoConnection">Faut-il faire booter la machine si la connexion � MySQL ne marche pas ? : </label>';
echo '<input type="checkbox" name="bootIfNoConnection" id="bootIfNoConnection" value="true" '.$checkedNoConnection.' />';
echo '</p>';

echo '<p>';
echo '<label for="emailAdmin">Email o� seront envoy�s les logs Rembo en cas d\'erreur <span style="color:red;">*</span> : </label>';
echo '<input type="text" name="emailAdmin" id="emailAdmin" value="'.$emailAdmin.'" />';
echo '</p>';

echo '<p>';
echo '<label for="emailFrom">Exp�diteur des logs Rembo en cas d\'erreur <span style="color:red;">*</span> : </label>';
echo '<input type="text" name="emailFrom" id="emailFrom" value="'.$emailFrom.'" />';
echo '</p>';

echo '<p>';
echo '<label for="shutdownDelay">Temps au bout duquel le PC s\'�teint automatiquent en cas d\'attente d\'un multiboot <br /> Temps en centi�me de seconde, 0 pour d�sactiver l\'extinction automatique <span style="color:red;">*</span> : </label>';
echo '<input type="text" name="shutdownDelay" id="shutdownDelay" value="'.$shutdownDelay.'" />';
echo '</p>';

echo '</fieldset>';

echo '<p>';
echo '<input type="submit" name="initialiser" value="Initialiser les param�tres du serveur Rembo" />';
echo '</p>';

echo '</form>';
echo '</fieldset>';


echo '</div>';


PiedPage();
?>
