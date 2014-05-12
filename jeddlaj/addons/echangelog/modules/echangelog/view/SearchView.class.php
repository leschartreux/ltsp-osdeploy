<?php
/**
 * ***************************** GPL STUFF ***********************************
 *
 * ********************************* ENGLISH *********************************
 *
 * Copyright notice :
 *
 * Copyright 2003, 2004, 2005 Gérard Milhaud - Frédéric Bloise
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
 * ************************* END OF GPL STUFF ***************************
 *
 * @category Outils_Administration
 * @package  E-changelog 
 * @author   Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license  GPL v2
 */
require_once 'global/View.class.php';

/**
 * Vue du formulaire de recherche de fichier de log,
 * de script de postinstall et de script de prédéinstall
 *
 * @category   Outils_Administration
 * @package    E-changelog
 * @subpackage View
 * @author     Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license    GPL v2
 */
class SearchView extends View
{
    /**
     * Contenu de la vue
     * @var string $_content Contenu de la vue
     */
    private $_content;

    /**
     * un objet contenant le résultat de la recherche
     * @var mixed $_searchResult le résultat de la recherche
     */
    private $_searchResult;

    private $_message;


    /**
     * Constructeur
     *
     * @param mixed $searchResult objet contenant la liste des OSle résultat de la recherche
     */
    function __construct($searchResult = null, $message = null)
    {        
        $this->_searchResult = $searchResult;
        $this->_message      = $message;
    }

    /**
     * Affiche le layout
     *
     * @return empty
     */
    function displayLayout()
    {
        parent::setLayout(
            'JeDDLaJ - Upload des fichiers', 
            $this->_content, 
            array('jquery.js' => 'text/javascript',
                  'autocompletion.js' => 'text/javascript',
                  'search.js' => 'text/javascript'),
            array('autocompletion.css' => 'screen',
                  'search.css' => 'screen'),
            $this->_message
        );
    }

    /**
     * Affiche le formulaire de recherche
     *
     * @return empty
     */
    function displaySearch()
    {
        $this->_content = '';

        $this->_content .= '<form method="post" action="index.php?module=echangelog&amp;action=search" autocomplete="off">';

        $this->_content .= '<p>';

        $this->_content .= '<label id="l_nomLogiciel" for="nomLogiciel">Nom du logiciel : </label>';

        $this->_content .= '<input type="text" name="nomLogiciel" id="nomLogiciel" onkeyup="lookup(\'nomLogiciel\', this.value, \'index.php?module=echangelog&amp;action=search&amp;view=listeLogiciel\');" />';

        $this->_content .= '<div class="suggestionsBox" id="suggestions_nomLogiciel" style="display:none;">';

        $this->_content .= '<div class="suggestionList" id="autoSuggestionsList_nomLogiciel"></div></div>';

        $this->_content .= '</p>';

        $this->_content .= '<p>';

        $this->_content .= '<label id="l_os" for="os">O.S. : </label>';

        $this->_content .= '<select id="os" name="os">';

        $this->_content .= '<option value=""> -- </option>';

        $this->_content .= '</select>';

        $this->_content .= '</p>';

        $this->_content .= '<p>';

        $this->_content .= '<label id="l_version" for="version">Version : </label>';

        $this->_content .= '<select id="version" name="version">';

        $this->_content .= '<option value=""> -- </option>';

        $this->_content .= '</select>';

        $this->_content .= '</p>';

        $this->_content .= '<p>';

        $this->_content .= '<input type="submit" name="submit" id="submit" value="Rechercher" />';

        $this->_content .= '</p>';

        $this->_content .= '</form>';

        $this->_content .= '<div class="clear"><hr /></div>';
    }

    /**
     * Affiche le résultat de la recherche (browse)
     *
     * @return empty
     */
    function displayBrowse()
    {
        $this->_content .= '<p>Liste des packages</p>';

        $this->_content .= '<table>';
        
        $this->_content .= '<tr>';

        $this->_content .= '<td>Package</td>';

        $this->_content .= '<td>O.S.</td>';

        $this->_content .= '<td>Version</td>';

        $this->_content .= '<td>Date de première publication</td>';

        $this->_content .= '</tr>';

        foreach ($this->_searchResult as $logiciel) {

            $this->_content .= '<tr>';

            $this->_content .= '<td><a href="index.php?module=echangelog&amp;action=install&amp;logiciel='.urlencode($logiciel->nom_logiciel).'&amp;os='.urlencode($logiciel->nom_os).'&amp;version='.urlencode($logiciel->version).'">'.$logiciel->nom_logiciel.'</a></td>';

            $this->_content .= '<td>'.$logiciel->nom_os.'</td>';

            $this->_content .= '<td>'.$logiciel->version.'</td>';

            $this->_content .= '<td>'.date('d/m/Y', $logiciel->date_logiciel).'</td>';

            $this->_content .= '</tr>';

        }

        $this->_content .= '</table>';
    }

    /**
     * Affiche le contenu de la vue
     *
     * @return string $_content le contenu de la vue
     */
    function displayContent()
    {
        $this->displaySearch();

        if ($this->_searchResult !== null) {

            $this->displayBrowse();
        }
    }

    /**
     * Affiche le contenu de la vue dans le layout
     *
     * @return empty
     */
    function display()
    {
        $this->displayContent();
        $this->displayLayout();
    }
}

?>