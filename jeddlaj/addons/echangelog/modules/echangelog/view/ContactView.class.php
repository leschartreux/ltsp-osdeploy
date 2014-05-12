<?php
/**
 * ******************************** GPL STUFF ********************************
 *
 * ********************************* ENGLISH *********************************
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
 * ********************** END OF GPL STUFF *******************************
 *
 * @category Outils_Administration
 * @package  E-changelog
 * @author   Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license  GPL v2
 */
require_once 'global/View.class.php';

/**
 * Vue du formulaire d'upload de fichier de log
 * de script de postinstall et de script de prédéinstall
 *
 * @category   Outils_Administration
 * @package    E-changelog
 * @subpackage View
 * @author     Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license    GPL v2
 */
class ContactView extends View
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
            array('jquery.js' => 'text/javascript', 
                  'contact.js' => 'text/javascript'), 
            array('contact.css' => 'screen'),
            $this->_message
        );
    }

    /**
     * Affiche la liste des logiciels dont on peut saisir des informations sur leur(s) fichier(s) de log
     *
     * @return empty
     */
    function displayForm()
    {
        $this->_content = '';

        $this->_content .= '<form method="post" action="index.php?module=echangelog&amp;action=contact">';


        $this->_content .= '<p id="consigne">Veuillez remplir le formulaire suivant afin d\'obtenir vos identifiant à la base de données d\'Echangelog<br />Tous les champs sont obligatoires sauf les commentaires</p>';


        $this->_content .= '<div class="inputWrapper">';

        $this->_content .= '<p>';

        $this->_content .= '<label for="login" id="l_login">Login : </label>';

        $this->_content .= '<input type="text" name="login" id="login" />';

        $this->_content .= '</p>';


        $this->_content .= '<p>';

        $this->_content .= '<label for="nom" id="l_nom">Nom : </label>';

        $this->_content .= '<input type="text" name="nom" id="nom" />';

        $this->_content .= '</p>';


        $this->_content .= '<p>';

        $this->_content .= '<label for="prenom" id="l_prenom">Prénom : </label>';

        $this->_content .= '<input type="text" name="prenom" id="prenom" />';

        $this->_content .= '</p>';


        $this->_content .= '<p>';

        $this->_content .= '<label for="email" id="l_email">Email : </label>';

        $this->_content .= '<input type="text" name="email" id="email" />';

        $this->_content .= '</p>';


        $this->_content .= '<p>';

        $this->_content .= '<label for="adresseIP" id="l_adresseIP">Adresse IP du serveur : </label>';

        $this->_content .= '<input type="text" name="adresseIP" id="adresseIP" />';

        $this->_content .= '</p>';


        $this->_content .= '<p>';

        $this->_content .= '<label for="commentaire" id="l_commentaire">Commentaire : </label>';

        $this->_content .= '<textarea name="commentaire" cols="10" rows="10" id="commentaire"></textarea>';

        $this->_content .= '</p>';


        $this->_content .= '<p>';

        $this->_content .= '<input type="submit" id="btn_send" name="submit" value="envoyer" />';

        $this->_content .= '</p>';

        $this->_content .= '</div>';


        $this->_content .= '</form>';

        $this->_content .= '<div class="clear"><hr /></div>';

        $this->_content .= '<p><a href="index.php?module=echangelog">Retour</a></p>';
    }

    /**
     * Affiche le contenu de la vue
     *
     * @return string $_content le contenu de la vue
     */
    function displayContent()
    {
        $this->displayForm();
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
