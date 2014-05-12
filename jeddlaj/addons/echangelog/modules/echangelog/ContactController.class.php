<?php
/**
 * @package  E-changelog 
 * @category Outils_Administration
 * @author   Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license  GPL v2
 *
 */
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
 */
require_once VIEW_PATH . 'ContactView.class.php';

/**
 * Controller de la demande d'identifiant lors de la premi�re utilisation d'echangelog
 *
 * @category   Outils_Administration
 * @package    E-changelog
 * @subpackage Controller
 * @author     Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license    GPL v2
 */
class ContactController
{
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
        $contactView = new ContactView($this->_message);
        $contactView->display();
    }

    /**
     * envoie du mail
     *
     */
    function sendMail()
    {

        if (!empty($_POST['login'])) {

            $cleanLogin = htmlspecialchars($_POST['login'], ENT_QUOTES);

        } else {
            $cleanLogin = '';
            $this->_message .= 'Veuillez saisir un login<br />';
        }

        if (!empty($_POST['nom'])) {

            $cleanNom = htmlspecialchars($_POST['nom'], ENT_QUOTES);

        } else {
            $cleanNom = '';
            $this->_message .= 'Veuillez saisir votre nom<br />';
        }

        if (!empty($_POST['prenom'])) {

            $cleanPrenom = htmlspecialchars($_POST['prenom'], ENT_QUOTES);

        } else {
            $cleanPrenom = '';
            $this->_message .= 'Veuillez saisir votre pr�nom<br />';
        }

        if (!empty($_POST['adresseIP'])) {

            if(preg_match('#^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$#', $_POST['adresseIP'])) {

                $cleanAdresseIP = $_POST['adresseIP'];

            } else {

                $cleanAdresseIP = '';

                $this->_message .= 'Veuillez saisir une adresse IP valide<br />';
            }

        } else {

            $cleanAdresseIP = '';

            $this->_message .= 'Veuillez saisir une adresse IP sortante<br />';

        }

        if (!empty($_POST['email'])) {

            if(preg_match('#^[[:alnum:]]([-_.]?[[:alnum:]])+_?@[[:alnum:]]([-.]?[[:alnum:]])+\.[a-z]{2,6}$#', $_POST['email'])) {

                $cleanEmail = $_POST['email'];                

            } else {

                $cleanEmail = '';

                $this->_message .= 'Veuillez saisir une adresse email valide<br />';
            }

        } else {
            
            $cleanEmail = '';
            
            $this->_message .= 'Veuillez saisir une adresse email<br />';

        }

        if (!empty($_POST['commentaire'])) {

            $cleanCommentaire = htmlspecialchars($_POST['commentaire'], ENT_QUOTES);

        } else {

            $cleanCommentaire = '';
        }

        if ($cleanLogin !== '' && $cleanNom !== '' && $cleanPrenom !== '' && $cleanAdresseIP !== '' && $cleanEmail !== '') {

            mail('jeddlaj@luminy.univmed.fr', '[JeDDLaJ] Demande de compte pour le module echangelog', 'Information de l\'utilisateur : '."\r\n".'Login : '.$cleanLogin."\r\n".'Nom : '.$cleanNom."\r\n".'Pr�nom : '.$cleanPrenom."\r\n".'Adresse IP sortante : '.$cleanAdresseIP."\r\n\r\n".'Commentaires additionnel : '."\r\n".$cleanCommentaire, 'From:" '.$cleanPrenom.' '.$cleanNom.' ('.$cleanLogin.') "<'.$cleanEmail.'>'."\r\n");

            $this->_message .= 'Votre email a �t� envoy�';
        }
    }


    /**
     * Gestion des requ�tes HTTP
     *
     * @return emtpy
     */
    function request()
    {
        if (isset($_POST['submit'])) {

            $this->sendMail();
        }
    }
}

?>
