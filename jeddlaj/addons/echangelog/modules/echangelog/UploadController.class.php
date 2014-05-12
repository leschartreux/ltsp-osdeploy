<?php
/**
 * ******************************* GPL STUFF ***********************************
 * 
 * ********************************* ENGLISH *********************************
 *
 * Copyright notice :
 *
 * Copyright 2003, 2004, 2005 G�rard Milhaud - Fr�d�ric Bloise
 *
 *
 *  Statement of copying permission
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
 *  Notice de Copyright :
 *
 * Copyright 2003, 2004, 2005 G�rard Milhaud - Fr�d�ric Bloise
 *
 *
 *  D�claration de permission de copie
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
 * *************************** END OF GPL STUFF *******************************
 *
 * @category Outils_Administration
 * @package  E-changelog 
 * @author   Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license  GPL v2
 *
 */
require_once VIEW_PATH.'UploadView.class.php';

require_once MODEL_PATH.'LogicielDao.class.php';
require_once MODEL_PATH.'LogDao.class.php';
require_once MODEL_PATH.'PisDao.class.php';
require_once MODEL_PATH.'PdisDao.class.php';

require_once EXPECT_PATH.'ExpectDefs.php';

/**
 * Controller de l'upload de fichier de log, de script de postinstall et de script de deinstall
 *
 * @category   Outils_Administration
 * @package    E-changelog
 * @subpackage Controller
 * @author     Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license    GPL v2
 */
class UploadController
{
    /**
     * Object qui contient les informations de tous les logiciels pr�sents sur la base locale
     * @var mixed $_listeLogiciels Object qui contient les informations de tous les logiciels pr�sents sur la base locale
     */
    private $_listeLogiciel;

    /**
     * Objet qui contient les informations du logiciel s�lectionn� dans le liste d�roulante
     * @var mixed $_infoLogiciel Objet qui contient les informations du logiciel s�lectionn� dans le liste d�roulante
     */
    private $_infoLogiciel;

    /**
     * Objet contenant toutes les informations des packages relatifs � un logiciel
     * @var mixed $_infoPackage les informations de tous les packages li�s � un logiciel
     */
    private $_infoPackage;

    /**
     * Objet contenant toutes les informations des postinstall scripts relatifs � un logiciel
     * @var mixed $_infoPis les informations de tous les postinstall script li�s � un logiciel
     */
    private $_infoPis;

    /**
     * Objet contenant toutes les informations des predinstall scripts relatifs � un logiciel
     * @var mixed $_infoPdis les informations de tous les predeinstall script li�s � un logiciel
     */
    private $_infoPdis;

    private $_view;

    /**
     * Identifiant du logiciel s�lectionn� dans le liste d�roulante
     * @var int $_idLogiciel id du logiciel s�lectionn�
     */
    private $_idLogiciel;


    /**
     * array des donn�es du formulaire d'upload
     * @var mixed $_arrayUploadData array des donn�es du formulaire d'upload
     */
    private $_arrayUploadData;

    private $_arrayFiles;

    private $_message;


    /**
     * Constructeur
     */
    function __construct()
    {
    }

    /**
     * Gestion des vues
     * 
     * @return empty
     */
    function viewsManagement()
    {
        $uploadView = new UploadView($this->_listeLogiciel, $this->_view, $this->_infoLogiciel, $this->_infoPackage, $this->_infoPis, $this->_infoPdis, $this->_message);
        $uploadView->display();
    }

    /**
     * Initialise le contenu de la liste des logiciels
     *
     * @return empty
     */
    function initListeLogiciels()
    {
        $logicielDao = new LogicielDao('rembo');
        $this->_listeLogiciel = $logicielDao->selectAllLogiciel();
    }

    /**
     * Initialise le param�tre view qui permet la s�lection des vues � afficher en fonction du contexte
     *
     * @return empty
     */
    function initView()
    {
        if (isset($_GET['view']) && $_GET['view'] === 'uploadform') {

            $this->_view = $_GET['view'];

        } else {

            $this->_view = '';
        }
    }

    /**
     * Initialise l'identifiant du logiciel choisi dans le liste d�roulante
     *
     * @return empty
     */
    function initIdLogiciel()
    {
        if (isset($_POST['idLogiciel'])) {

            $this->_idLogiciel = intval($_POST['idLogiciel']);

        } else {

            $this->_idLogiciel = 0;
        }
    }

    /**
     * Initialise les informations du logiciel choisi
     *
     * @return empty
     */
    function initInfoLogiciel()
    {
        $logicielDao = new LogicielDao('rembo');
        $this->_infoLogiciel = $logicielDao->selectOneLogiciel($this->_idLogiciel);
    }

    /**
     * Initialise les informations du package en fonction du logiciel choisi
     *
     * @return empty
     */
    function initInfoPackage()
    {
        $logDao = new LogDao('rembo');
        $this->_infoPackage = $logDao->selectPackageByLogiciel($this->_idLogiciel);
    }

    /**
     * Initialise les informations du postinstall script du logiciel choisi
     *
     * @return empty
     */
    function initInfoPis()
    {
        $pisDao = new PisDao('rembo');
        $this->_infoPis = $pisDao->selectPisByLogiciel($this->_idLogiciel);
    }

    /**
     * Initialise les informations du desintall script du logiciel choisi
     *
     * @return empty
     */
    function initInfoPdis()
    {
        $pdisDao = new PdisDao('rembo');
        $this->_infoPdis = $pdisDao->selectPdisByLogiciel($this->_idLogiciel);
    }


    /**
     * formate les donn�es en provenance du formulaire d'upload
     *
     * @return empty
     */
    function formateData()
    {
        $this->_arrayUploadData['nomLogiciel'] = htmlspecialchars($_POST['nomLogiciel'], ENT_QUOTES);
        $this->_arrayUploadData['nomOS']       = htmlspecialchars($_POST['nomOS'], ENT_QUOTES);
        $this->_arrayUploadData['version']     = htmlspecialchars($_POST['version'], ENT_QUOTES);

        $arrayMedia = array('cd', 'url');

        if (isset($_POST['media'])) {

            if (in_array($_POST['media'], $arrayMedia)) {

                $this->_arrayUploadData['media'] = $_POST['media'];

                if ($this->_arrayUploadData['media'] === 'cd') {

                    $this->_arrayUploadData['mediaCD']  = htmlspecialchars($_POST['mediaCD'], ENT_QUOTES);
                    $this->_arrayUploadData['mediaUrl'] = '';

                } else if ($this->_arrayUploadData['media'] === 'url') {

                    $this->_arrayUploadData['mediaCD']  = '';
                    $this->_arrayUploadData['mediaUrl'] = htmlspecialchars($_POST['mediaUrl'], ENT_QUOTES);
                }
            }

        } else {

            $this->_arrayUploadData['media'] = '';
            $this->_arrayUploadData['mediaCD'] = '';
            $this->_arrayUploadData['mediaUrl'] = '';
        }

        if (isset($_POST['package'])) {

            foreach ($_POST['package'] as $key => $value) {

                $this->_arrayUploadData['package'][$key] = intval($value);
            }
        }

        if (isset($_POST['nomPackage'])) {

            foreach ($_POST['nomPackage'] as $key => $value) {

                $this->_arrayUploadData['nomPackage'][$key] = htmlspecialchars($value, ENT_QUOTES);

            }
        }

        if (isset($_POST['packageCommentaire'])) {

            foreach ($_POST['packageCommentaire'] as $key => $value) {

                $this->_arrayUploadData['packageCommentaire'][$key] = htmlspecialchars($value, ENT_QUOTES);
            }
        }

        if (isset($_POST['pis'])) {

            foreach ($_POST['pis'] as $key => $value) {

                $this->_arrayUploadData['pis'][$key] = intval($value);
            }
        }

        if (isset($_POST['nomPis'])) {

            foreach ($_POST['nomPis'] as $key => $value) {

                $this->_arrayUploadData['nomPis'][$key] = htmlspecialchars($value, ENT_QUOTES);
            }
        }

        if (isset($_POST['pisCommentaire'])) {

            foreach ($_POST['pisCommentaire'] as $key => $value) {

                $this->_arrayUploadData['pisCommentaire'][$key] = htmlspecialchars($value, ENT_QUOTES);
            }
        }

        if (isset($_POST['pdis'])) {

            foreach ($_POST['pdis'] as $key => $value) {

                $this->_arrayUploadData['pdis'][$key] = intval($value);
            }
        }

        if (isset($_POST['nomPdis'])) {

            foreach ($_POST['nomPdis'] as $key => $value) {

                $this->_arrayUploadData['nomPdis'][$key] = htmlspecialchars($value, ENT_QUOTES);
            }
        }

        if (isset($_POST['pdisCommentaire'])) {

            foreach ($_POST['pdisCommentaire'] as $key => $value) {

                $this->_arrayUploadData['pdisCommentaire'][$key] = htmlspecialchars($value, ENT_QUOTES);
            }
        }
    }


    function retrieveFiles()
    {
        $netclnt_program = $GLOBALS['netclnt_program'];
        $server = $GLOBALS['rembo_server'];
        $passwd = $GLOBALS['rembo_passwd'];

        $i = 0;

        foreach ($this->_infoPackage as $infoPackage) {

            if (isset($this->_arrayUploadData['package'][$i])) {

                if ($infoPackage->id_package == $this->_arrayUploadData['package'][$i]) {

                    $rep = LOG_PATH.$infoPackage->repertoire;
                    $file = $infoPackage->nom_package.'.log';
                    
                    $cmd = EXPECT_PATH."get.expect $netclnt_program $server $passwd $rep $file";
                    exec($cmd);

                    $log[$i] = file_get_contents('/tmp/'.$infoPackage->nom_package.'.log');

                    $this->_arrayFiles['bzLog'][$i] = bzcompress($log[$i]);

                    unlink('/tmp/'.$file);
                }
            }

            $i++;
        }

        //R�cup�ration des fichiers de script de postinstall

        $i = 0;

        foreach ($this->_infoPis as $infoPis) {

            if (isset($this->_arrayUploadData['pis'][$i])) {

                if ($infoPis->id_script == $this->_arrayUploadData['pis'][$i]) {

                    $rep = PIS_PATH.$infoPis->repertoire;
                    $file = $infoPis->nom_script;

                    $cmd = EXPECT_PATH."get.expect $netclnt_program $server $passwd $rep $file";
                    exec($cmd);

                    $pisFile[$i] = file_get_contents('/tmp/'.$infoPis->nom_script);

                    $this->_arrayFiles['bzPisFile'][$i] = bzcompress($pisFile[$i]);

                    unlink('/tmp/'.$file);
                }
            }

            $i++;
        }

        //R�cup�ration des fichiers de script de predeinstall

        $i = 0;

        foreach ($this->_infoPdis as $infoPdis) {

            if (isset($this->_arrayUploadData['pdis'][$i])) {

                if ($infoPdis->id_script == $this->_arrayUploadData['pdis'][$i]) {

                    $rep = PDIS_PATH.$infoPdis->repertoire;
                    $file = $infoPdis->nom_script;

                    $cmd = EXPECT_PATH."get.expect $netclnt_program $server $passwd $rep $file";
                    exec($cmd);

                    $pdisFile[$i] = file_get_contents('/tmp/'.$infoPdis->nom_script);

                    $this->_arrayFiles['bzPdisFile'][$i] = bzcompress($pdisFile[$i]);
                }
            }

            $i++;
        }
    }


    /**
     * le nom me plait pas pour l'instant
     * de plus il faudra s�rement d�couper la m�thode en trois m�thode plus petite
     *
     * @return empty
     */
    function writeData()
    {
        //insertion du logiciel dans la bdd
        $logicielDao = new LogicielDao('rembo_echangelog');
        $logicielDao->insertLogiciel($this->_arrayUploadData['nomLogiciel'], $this->_arrayUploadData['nomOS'], $this->_arrayUploadData['version'], $this->_arrayUploadData['mediaCD'], $this->_arrayUploadData['mediaUrl']);
        $idLogiciel = $logicielDao->selectIdLogicielByNomOsVersion($this->_arrayUploadData['nomLogiciel'], $this->_arrayUploadData['nomOS'], $this->_arrayUploadData['version']);

        //insertion des donn�es du logiciel et des packages dans la bdd
        $i = 0;

        if (isset($this->_arrayUploadData['package'])) {

            foreach ($this->_arrayUploadData['package'] as $package) {

                if (isset($package)) {

                    $logDao = new LogDao('rembo_echangelog');
                    $idPackage = $logDao->insertLog($idLogiciel->id_logiciel, $this->_arrayUploadData['nomPackage'][$i], $this->_arrayUploadData['packageCommentaire'][$i], $this->_arrayFiles['bzLog'][$i]);
                }
            }
        }

        //insertion des donn�es du pis et de la table de liaison dans la bdd
        $i = 0;
        
        if (isset($this->_arrayUploadData['pis'])) {

            foreach ($this->_arrayUploadData['pis'] as $pis) {

                if (isset($pis)) {

                    $pisDao = new PisDao('rembo_echangelog');
                    $idPis = $pisDao->insertPis($this->_arrayUploadData['nomPis'][$i], $this->_arrayUploadData['pisCommentaire'][$i], $this->_arrayFiles['bzPisFile'][$i]);
                    $pisDao->insertPisToLog($idPis, $idLogiciel->id_logiciel);
                }
            }
        }

        //insertion des donn�es du pdis et de la table de liaison dans la bdd
        $i = 0;

        if (isset($this->_arrayUploadData['pdis'])) {

            foreach ($this->_arrayUploadData['pdis'] as $pdis) {

                if (isset($pdis)) {

                    $pdisDao = new PdisDao('rembo_echangelog');
                    $idPdis = $pdisDao->insertPdis($this->_arrayUploadData['nomPdis'][$i], $this->_arrayUploadData['pdisCommentaire'][$i], $this->_arrayFiles['bzPdisFile'][$i]);
                    $pdisDao->insertPdisToLog($idPdis, $idLogiciel->id_logiciel);
                }
            }
        }
    }

    /**
     * Gestion des requ�tes HTTP
     *
     * @return emtpy
     */
    function request()
    {
 
        $this->initView();
        $this->initListeLogiciels();

        $this->initIdLogiciel();

        if ($this->_idLogiciel !== 0) {

            $this->initInfoLogiciel();
            $this->initInfoPackage();
            $this->initInfoPis();
            $this->initInfoPdis();

            if (isset($_POST['submit'])) {

                if ($_POST['mediaCD'] !== '' || $_POST['mediaUrl'] !== '') {
                    $this->formateData();
                    $this->retrieveFiles();
                    $this->writeData();
                } else {

                    $this->_message = 'Veuillez remplire le champs CD ou le champs URL';

                }
            }
        }
    }
}

?>
