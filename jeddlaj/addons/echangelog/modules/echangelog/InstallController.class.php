<?php
/**
 * ******************************** GPL STUFF *************************
 * ************************** ENGLISH *********************************
 *
 * Copyright notice :
 *
 * Copyright 2003, 2004, 2005 Gérard Milhaud - Frédéric Bloise
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
 * *********** TRADUCTION FRANÇAISE PERSONNELLE SANS VALEUR LÉGALE ***********
 *
 *  Notice de Copyright :
 *
 * Copyright 2003, 2004, 2005 Gérard Milhaud - Frédéric Bloise
 *
 *
 *  Déclaration de permission de copie
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
 * ************************** END OF GPL STUFF *******************************
 *
 * @category Outils_Administration
 * @package  E-changelog 
 * @author   Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license  GPL v2
 */
require_once VIEW_PATH.'InstallView.class.php';

require_once MODEL_PATH.'LogicielDao.class.php';
require_once MODEL_PATH.'LogDao.class.php';
require_once MODEL_PATH.'PisDao.class.php';
require_once MODEL_PATH.'PdisDao.class.php';
require_once MODEL_PATH.'ComLogDao.class.php';
require_once MODEL_PATH.'ComPisDao.class.php';
require_once MODEL_PATH.'ComPdisDao.class.php';
require_once MODEL_PATH.'NoteDao.class.php';

require_once EXPECT_PATH.'ExpectDefs.php';

/**
 * Controller de l'installation de fichier de log, de script de postinstall et de script de désinstall
 *
 * @category   Outils_Administration
 * @package    E-changelog
 * @subpackage Controller
 * @author     Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license    GPL v2
 */
class InstallController
{
    /**
     * objet contenant des informations du logiciel dont dépend les scripts (log, pis, pdis)
     * @var mixed $_logicielInfo objet contenant des informations du logiciel dont dépend les scripts (log, pis, pdis)
     */
    private $_logicielInfo;

    /**
     * objet contenant les informations des fichiers de log relatfs au logiciel choisi
     * @var mixed $_logInfo objet contenant les informations des fichiers de log relatfs au logiciel choisi
     */
    private $_logInfo;

    /**
     * objet contenant les informations des pis relatf au logiciel choisi
     * @var mixed $_logInfo objet contenant les informations des pis relatfs au logiciel choisi
     */
    private $_pisInfo;

    /**
     * objet contenant les informations des pdis relatf au logiciel choisi
     * @var mixed $_logInfo objet contenant les informations des pdis relatfs au logiciel choisi
     */
    private $_pdisInfo;

    /**
     * objet contenant les commentaires d'un fichier de log
     * @var mixed $_logCom objet contenant les informations d'un fichier de log
     */
    private $_logCom;

    /**
     * objet contenant les commentaires d'un pis
     * @var mixed $_pisCom objet contenant les informations d'un pis
     */
    private $_pisCom;

    /**
     * objet contenant les commentaires d'un pdis
     * @var mixed $_pdisCom objet contenant les informations d'un pdis
     */
    private $_pdisCom;

    /**
     * objet contenant les informations des notes des fichiers de log
     * @var mixed $_noteInfo objet contenant les informations des notes des fichiers de log
     */
    private $_noteInfo;

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

        if ($this->_task !== 'displaynote') {

            $installView = new InstallView($this->_logicielInfo, $this->_logInfo, $this->_pisInfo, $this->_pdisInfo, $this->_logCom, $this->_pisCom, $this->_pdisCom, $this->_message);
            $installView->display();
        }
    }

    /**
     * initialise les informations liées au script (log, pis, pdis) du logiciel sélectionné
     *
     * @return empty
     */
    function initLogicielInfo()
    {
        $arrayParam = array();

        if (isset($_GET['logiciel'])) {

            $arrayParam['nomLogiciel'] = htmlspecialchars($_GET['logiciel'], ENT_QUOTES);
        }

        if (isset($_GET['os'])) {

            $arrayParam['os'] = htmlspecialchars($_GET['os'], ENT_QUOTES);
        }

        if (isset($_GET['version'])) {

            $arrayParam['version'] = htmlspecialchars($_GET['version'], ENT_QUOTES);
        }

        $logicielDao = new LogicielDao('rembo_echangelog');
        $this->_logicielInfo = $logicielDao->selectLogicielByNomOSVersion($arrayParam);
    }

    /**
     * Initialise l'objet qui contient les infos des fichiers de log
     *
     * @return empty
     */
    function initLogInfo()
    {
        $logDao = new LogDao('rembo_echangelog');
        $this->_logInfo = $logDao->selectInfoLog($this->_logicielInfo->id_logiciel);
    }

    /**
     * Initialise l'objet qui contient les infos des pis
     *
     * @return empty
     */
    function initPisInfo()
    {
        $pisDao = new PisDao('rembo_echangelog');
        $this->_pisInfo = $pisDao->selectInfoPis($this->_logicielInfo->id_logiciel);
    }

    /**
     * Initialise l'objet qui contient les infos des pdis
     *
     * @return empty
     */
    function initPdisInfo()
    {
        $pdisDao = new PdisDao('rembo_echangelog');
        $this->_pdisInfo = $pdisDao->selectInfoPdis($this->_logicielInfo->id_logiciel);
    }

    /**
     * Initialise un objet qui contient le nombre et la moyenne des votes
     *
     * @return empty
     */
    function initNote($idScript)
    {
        $noteDao = new NoteDao('rembo_echangelog');
        $note = $noteDao->selectNbNoteAndAvg($idScript);

        return $note;
    }

    /**
     * Initilise l'objet qui contient le nombre et la moyenne des votes de tous les fichiers de log
     *
     * @return empty
     */
    function initNoteInfo()
    {
        $cleanIdScript = intval($_POST['idScript']);
        $this->_noteInfo = $this->initNote($cleanIdScript);

        echo  number_format($this->_noteInfo->avg, 2, ',', ' ').':'.$this->_noteInfo->nbNote;
    }

    /**
     * Insert les notes dans la base
     *
     * @return empty
     */
    function insertNote()
    {
        $cleanIdScript = intval($_POST['idScript']);

        $note = intval($_POST['note']);

        $noteDao = new NoteDao('rembo_echangelog');
        $noteDao->insertNote($cleanIdScript, $note);
    }

    /**
     * Nettoie les données passées en post (chemin des répertoires du serveur rempbo où l'on veux placer nos fichiers)
     *
     * @param string $path chemin du répèrtoire sur le serveur rembo
     *
     * @return string $cleanPath le chemin "nettoyé"
     */
    function clearPath($path)
    {
        $cleanPath = htmlspecialchars($path, ENT_QUOTES);

        return $cleanPath;
    }

    /**
     * place les fichiers sur le serveur rembo
     *
     * @return empty
     */
    function putFile()
    {

        $netclnt_program = $GLOBALS['netclnt_program'];
        $server = $GLOBALS['rembo_server'];
        $passwd = $GLOBALS['rembo_passwd'];

        if (isset($_POST['pisPath'])) {

            $rep = $this->clearPath($_POST['pisPath']);

            $fileContent = bzdecompress($this->_pisInfo[0]->fichier);

            //on vérifie que le fichier n'existe pas dans la base
            $repertoire = substr($rep, 13);

            $pisDao = new PisDao('rembo');
            $pis = $pisDao->countPis($repertoire, $this->_pisInfo[0]->nom_script);


            if ($pis->nbPis == 0) {

                $file = '/tmp/'.$this->_pisInfo[0]->nom_script;

                file_put_contents($file, $fileContent);

                $cmd = EXPECT_PATH."./put.expect $netclnt_program $server $passwd $rep $file";

                exec($cmd);

                unlink($file);

            } else {

                $this->_message = 'Ce fichier existe déjà. Veuillez le renommer ou changer le répertoire de destination';
            }

        } else if (isset($_POST['pdisPath'])) {

            $rep = $this->clearPath($_POST['pdisPath']);

            $fileContent = bzdecompress($this->_pdisInfo[0]->fichier);

            //on vérifie que le fichier n'existe pas dans la base
            $repertoire = substr($rep, 14);

            $pdisDao = new PdisDao('rembo');
            $pdis = $pdisDao->countPdis($repertoire, $this->_pdisInfo[0]->nom_script);

            if ($pdis->nbPdis == 0) {

                $file = '/tmp/'.$this->_pdisInfo[0]->nom_script;

                file_put_contents($file, $fileContent);

                $cmd = EXPECT_PATH."./put.expect $netclnt_program $server $passwd $rep $file";
                exec($cmd);

                unlink($file);

            } else {

                $this->_message = 'Ce fichier existe déjà. Veuillez le renommer ou changer le répertoire de destination';
            }
        }
    }

    /**
     * Création du fichier de log
     *
     * @return empty
     */
    function createLog()
    {
        $fileContent = bzdecompress($this->_logInfo[0]->fichier);

        $file = 'tmp/'.gethostbyaddr($_SERVER['REMOTE_ADDR']).'.log';

        file_put_contents($file, $fileContent);

        header("Content-Disposition: attachment; filename=".gethostbyaddr($_SERVER['REMOTE_ADDR']).'.log');
        header("content-type: text/plain; charset=ISO-8859-15");
        flush(); 
        readfile($file); // téléchargement forcé /!\ ne marcherais pas sous IE
	      exit();
    }

    /**
     * Initialise l'objet qui contient les commentaires d'un fichier de log
     *
     * @return empty
     */
    function initLogCom()
    {
        $comLogDao = new ComLogDao('rembo_echangelog');
        $this->_logCom = $comLogDao->selectComLog($this->_logicielInfo->id_logiciel);
    }

    /**
     * Initialise l'objet qui contient les commentaires d'un pis
     *
     * @return empty
     */
    function initPisCom()
    {
        $comPisDao = new ComPisDao('rembo_echangelog');
        $this->_pisCom = $comPisDao->selectComPis($this->_logicielInfo->id_logiciel);
    }

    /**
     * Initialise l'objet qui contient les commentaires d'un pdis
     *
     * @return empty
     */
    function initPdisCom()
    {
        $comPdisDao = new ComPdisDao('rembo_echangelog');
        $this->_pdisCom = $comPdisDao->selectComPdis($this->_logicielInfo->id_logiciel);
    }

    /**
     * Ecriture des commentaires du fichier de log dans la base
     *
     * @return empty
     */
    function writeLogCom()
    {
        if (isset($_POST['commentaireLog'])) {

            $cleanCommentaireLog = htmlspecialchars($_POST['commentaireLog'], ENT_QUOTES);
        }

        if (isset($_POST['idScript'])) {

            $cleanIdScript = intval($_POST['idScript']);
        }

        $comLogDao = new ComLogDao('rembo_echangelog');

        $idCommentaire = $comLogDao->insertComLog($cleanCommentaireLog);

        $comLogDao->insertComLogToLog($idCommentaire, $cleanIdScript);
        $comLogDao->insertComLogToAuteur($idCommentaire);
    }

    /**
     * Ecriture des commentaires du pis dans la base
     *
     * @return empty
     */
    function writePisCom()
    {
        if (isset($_POST['commentairePis'])) {

            $cleanCommentairePis = htmlspecialchars($_POST['commentairePis'], ENT_QUOTES);
        }

        if (isset($_POST['idScript'])) {

            $cleanIdScript = intval($_POST['idScript']);
        }

        $comPisDao = new ComPisDao('rembo_echangelog');

        $idCommentaire = $comPisDao->insertComPis($cleanCommentairePis);

        $comPisDao->insertComPisToPis($idCommentaire, $cleanIdScript);
        $comPisDao->insertComPisToAuteur($idCommentaire);
    }

    /**
     * Ecriture des commentaires du pdis de log dans la base
     *
     * @return empty
     */
    function writePdisCom()
    {
        if (isset($_POST['commentairePdis'])) {

            $cleanCommentairePdis = htmlspecialchars($_POST['commentairePdis'], ENT_QUOTES);
        }

        if (isset($_POST['idScript'])) {

            $cleanIdScript = intval($_POST['idScript']);
        }

        $comPdisDao = new ComPdisDao('rembo_echangelog');

        $idCommentaire = $comPdisDao->insertComPdis($cleanCommentairePdis);

        $comPdisDao->insertComPdisToPdis($idCommentaire, $cleanIdScript);
        $comPdisDao->insertComPdisToAuteur($idCommentaire);
    }

    /**
     * Initialise les tâches pour les actions réalisé avec AJAX
     *
     * @return empty
     */
    function initTask()
    {

        if (isset($_GET['task'])) {

            if (in_array($_GET['task'], array('displaynote', 'rate'))) {

                $this->_task = $_GET['task'];

            } else {

                $this->_task = '';
            }
        } else {

            $this->_task = '';
        }
    }

    /**
     * Gestion des requêtes HTTP
     *
     * @return emtpy
     */
    function request()
    {
        $this->initLogicielInfo();
        $this->initLogInfo();
        $this->initPisInfo();
        $this->initPdisInfo();

        if (isset($_POST['commentaireLog'])) {

            $this->writeLogCom();

        } else if (isset($_POST['commentairePis'])) {

            $this->writePisCom();

        } else if (isset($_POST['commentairePdis'])) {

            $this->writePdisCom();
        }

        $this->initLogCom();
        $this->initPisCom();
        $this->initPdisCom();

        if (isset($_POST['telechargerLog'])) {

            $this->createLog();
        }

        if (isset($_POST['pisPath']) || isset($_POST['pdisPath'])) {

            $this->putFile();
        }

        $this->initTask();

        if ($this->_task === 'displaynote') {

            $this->initNoteInfo();
            
        } else if ($this->_task === 'rate') {

            $this->insertNote();
            $this->initNoteInfo();
        }
    }
}

?>
