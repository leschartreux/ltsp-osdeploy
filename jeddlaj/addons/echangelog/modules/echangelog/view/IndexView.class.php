<?php
/**
 * ******************************** GPL STUFF ********************************
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
 * ********************** END OF GPL STUFF *******************************
 *
 * @category Outils_Administration
 * @package  E-changelog
 * @author   Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license  GPL v2
 */
require_once 'global/View.class.php';

/**
 * Vue par d�faut du module echangelog
 *
 * @category   Outils_Administration
 * @package    E-changelog
 * @subpackage View
 * @author     Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license    GPL v2
 */
class IndexView extends View
{
    /**
     * Contenu de la vue
     * @var string $_content Contenu de la vue
     */
    private $_content;

    private $_message;

    /**
     * Constructeur
     */
    function __construct($message = null)
    {
        $this->_message = $message;
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
            array('jquery.js' => 'text/javascript'),
            array('uploadlog.css' => 'screen'),
            $this->_message
        );
    }

    /**
     * Affiche le contenu de la vue
     *
     * @return string $_content le contenu de la vue
     */
    function displayContent()
    {
        $this->_content .= '<p><a href="index.php?module=echangelog&amp;action=upload">Uploader un fichier de log, un script de postinstall ou un script de pr�d�install</a></p>';

        $this->_content .= '<p><a href="index.php?module=echangelog&amp;action=search">Rechercher un fichier de log, un script de postinstall ou un script de pr�d�install</a></p>';

        $this->_content .= '<p><a href="../..">Retour</a></p>';
        
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
