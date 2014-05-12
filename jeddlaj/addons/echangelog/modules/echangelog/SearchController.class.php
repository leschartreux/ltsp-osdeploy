<?php
/**
 * ***************************** GPL STUFF ***********************************
 *
 * ********************************* ENGLISH *********************************
 *
 * Copyright notice :
 *
 * Copyright 2003, 2004, 2005 G�rard Milhaud - Fr�d�ric Bloise
 *
 *
 * Statement of copying permission
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
 * ************************* END OF GPL STUFF ***************************
 *
 * @category Outils_Administration
 * @package  E-changelog 
 * @author   Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license  GPL v2
 */
require_once VIEW_PATH.'SearchView.class.php';
require_once VIEW_PATH.'ListeOsView.class.php';
require_once VIEW_PATH.'ListeVersionView.class.php';

require_once MODEL_PATH.'LogicielDao.class.php';

require_once 'plugins/autocompletion/view/AutocompletionView.class.php';

/**
 * Controller de la recherche de fichier de log, de script de postinstall et de script de d�sinstall
 *
 * @category   Outils_Administration
 * @package    E-changelog
 * @subpackage Controller
 * @author     Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license    GPL v2
 */
class SearchController
{
    /**
     * la liste des OS
     * @var mixed $_listeOS objet contenant la liste des OS des logiciels disponible
     */
    private $_listeOS;

    private $_listeVersion;

    private $_view;

    /**
     * un tableau contenant les noms des logiciels � afficher sous le champs d'autocompl�tion
     * @var mixed $_searchDisplay tableau contenant les noms des logiciels
     */
    private $_searchDisplay;

    /**
     * un objet contenant le r�sultat de la recherche
     * @var mixed $_searchResult le r�sultat de la recherche
     */
    private $_searchResult;


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
        if ($this->_view === 'listeLogiciel') {

            $autocompletionView = new AutocompletionView($this->_searchDisplay, 'nomLogiciel');
            $autocompletionView->display();

        } else if ($this->_view === 'listeOS') {

            $listeOsView = new ListeOsView($this->_listeOS);
            $listeOsView->display();

        } else if ($this->_view === 'listeVersion') {

            $listeVersionView = new ListeVersionView($this->_listeVersion);
            $listeVersionView->display();

        } else {

            $searchViews = new SearchView($this->_searchResult);
            $searchViews->display();
        }
    }

    /**
     * Initialise la liste d�roulante des OS
     *
     * @return empty
     */
    function initListeOS()
    {
        $cleanNomLogiciel = htmlspecialchars($_POST['nomLogiciel']);

        $logicielDao = new LogicielDao('rembo_echangelog');
        $this->_listeOS = $logicielDao->selectOSByNomLogiciel($cleanNomLogiciel);
    }

    /**
     * Initialise la liste d�roulante des version
     *
     * @return empty
     */
    function initListeVersion()
    {
        $cleanNomLogiciel = htmlspecialchars($_POST['nomLogiciel']);

        if (isset($_POST['os']) && $_POST['os'] !== '') {

            $cleanOS = htmlspecialchars($_POST['os'], ENT_QUOTES);

        } else {

            $cleanOS = '';
        }

        $logicielDao = new LogicielDao('rembo_echangelog');
        $this->_listeVersion = $logicielDao->selectVersionByNomAndOS($cleanNomLogiciel, $cleanOS);
    }

    /**
     * Initialise la vue � afficher
     *
     * @return empty
     */
    function initView()
    {
        if (isset($_GET['view'])) {

            $arrayValidView = array('listeLogiciel', 'listeOS', 'listeVersion');

            $cleanView = htmlspecialchars($_GET['view'], ENT_QUOTES);
            
            if (in_array($cleanView, $arrayValidView)) {
             
                $this->_view = $cleanView;

            } else {
                
                $this->_view = '';
            }
        } else {
            $this->_view = '';
        }
    }


    /**
     * Initialise la liste des logiciels pour le champ d'autocompl�tion
     *
     * @return empty
     */
    function initListeLogiciel()
    {
        if (isset($_POST['queryString'])) {

            $cleanQueryString = htmlspecialchars($_POST['queryString'], ENT_QUOTES);

            if (strlen($cleanQueryString) > 0) {

                $logicielDao = new LogicielDao('rembo_echangelog');
                $logicielSearch = $logicielDao->selectLogicielByChars($cleanQueryString);
                
                foreach ($logicielSearch as $search) {
                    $this->_searchDisplay[]  = $search->nom_logiciel;
                }
            }
        }
    }

    /**
     * initialise les donn�es pour le resultat de la recherche
     *
     * @return empty
     */
    function initSearchResult()
    {
        $arrayParam = array();

        if (isset($_POST['nomLogiciel'])) {

            $arrayParam['nomLogiciel'] = htmlspecialchars($_POST['nomLogiciel'], ENT_QUOTES);
        }

        if (isset($_POST['os'])) {

            $arrayParam['os'] = htmlspecialchars($_POST['os'], ENT_QUOTES);
        }

        if (isset($_POST['version'])) {

            $arrayParam['version'] = htmlspecialchars($_POST['version'], ENT_QUOTES);
        }

        $logicielDao = new LogicielDao('rembo_echangelog');
        $this->_searchResult = $logicielDao->selectLogicielByParam($arrayParam);
    }

    /**
     * Gestion des requ�tes HTTP
     *
     * @return emtpy
     */
    function request()
    {
        $this->initView();

        if ($this->_view === 'listeLogiciel') {
         
            $this->initListeLogiciel();


        } else if ($this->_view === 'listeOS') {

            $this->initListeOS();

        } else if ($this->_view === 'listeVersion') {

            $this->initListeVersion();
        }

        if (isset($_POST['submit'])) {

            $this->initSearchResult();

        }
    }
}

?>