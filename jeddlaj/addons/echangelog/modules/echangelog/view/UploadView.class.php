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
class UploadView extends View
{
    /**
     * Contenu de la vue
     * @var string $_content Contenu de la vue
     */
    private $_content;

    /**
     * Object qui contient les informations de tous les logiciels présents sur la base locale
     * @var mixed $_listeLogiciel Object qui contient les informations de tous les logiciels présents sur la base locale
     */
    private $_listeLogiciel;

    /**
     * Objet qui contient les informations du logiciel sélectionné dans le liste déroulante
     * @var mixed $_infoLogiciel Objet qui contient les informations du logiciel sélectionné dans le liste déroulante
     */
    private $_infoLogiciel;

    /**
     * Objet contenant toutes les informations des packages relatif à un logiciel
     * @var mixed $_infoPackage les informations de tous les packages lié à un logiciel
     */
    private $_infoPackage;

    /**
     * Objet contenant toutes les informations des postinstall scripts relatifs à un logiciel
     * @var mixed $_infoPis les informations de tous les postinstall script liés à un logiciel
     */
    private $_infoPis;

    /**
     * Objet contenant toutes les informations des predeinstall scripts relatifs à un logiciel
     * @var mixed $_infoPis les informations de tous les predeinstall scripts liés à un logiciel
     */
    private $_infoPdis;

    /**
     * Détermine les parties de la vue à afficher selon le contexte
     * @var string $_view détermine les parties de la vue à afficher
     */
    private $_view;

    private $_message;

    /**
     * Constructeur
     *
     * @param mixed  $listeLogiciel Object qui contient les informations de tous les logiciels présents sur la base locale
     * @param string $view          Détermine les parties de la vue à afficher selon le contexte
     * @param mixed  $infoLogiciel  Les informations de tous les packages lié à un logiciel
     * @param mixed  $infoPackage   Les informations de tous les packages lié à un logiciel
     * @param mixed  $infoPis       Les informations de tous les postinstall script liés à un logiciel
     * @param mixed  $infoPdis      Les informations de tous les predeinstall scripts liés à un logiciel
     */
    function __construct($listeLogiciel, $view, $infoLogiciel, $infoPackage, $infoPis, $infoPdis, $message = null)
    {
        $this->_listeLogiciel = $listeLogiciel;
        $this->_view          = $view;
        $this->_infoLogiciel  = $infoLogiciel;
        $this->_infoPackage   = $infoPackage;
        $this->_infoPis       = $infoPis;
        $this->_infoPdis      = $infoPdis;
        $this->_message       = $message;
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
     * Affiche la liste des logiciels dont on peut saisir des informations sur leur(s) fichier(s) de log
     *
     * @return empty
     */
    function displayListeLogiciel()
    {
        $this->_content = '';

        $this->_content .= '<form method="post" action="index.php?module=echangelog&amp;action=upload&amp;view=uploadform">';

        $this->_content .= '<p>';

        $this->_content .= '<label id="l_idLogiciel" for="idLogiciel">Nom logiciel : </label>';

        $this->_content .= '<select name="idLogiciel" id="idLogiciel" onchange="submit();" >';

        $this->_content .= '<option></option>';

        $lastOS = '';

        foreach ($this->_listeLogiciel as $logiciel) {

            if ($lastOS !== $logiciel->nom_os) {

                if ($lastOS !== '') {

                    $this->_content .= '</optgroup>';
                }

                $this->_content .= '<optgroup label="'.$logiciel->nom_os.'">';
            }

            $this->_content .= '<option value="'.$logiciel->id_logiciel.'">'.$logiciel->nom_logiciel.' '.$logiciel->version.'</option>';

            $lastOS = $logiciel->nom_os;
        }

        $this->_content .= '</optgroup>';

        $this->_content .= '</select>';

        $this->_content .= '</p>';

        $this->_content .= '</form>';

        $this->_content .= '<p><a href="index.php?module=echangelog">Retour</a></p>';
    }

    /**
     * Affiche le formulaire d'upload
     *
     * @return empty
     */
    function displayUploadForm()
    {
        $this->_content .= '<h2>Logiciel</h2>';

        $this->_content .= '<form action="index.php?module=echangelog&amp;action=upload" method="post">';

        $this->_content .= '<div class="inputWrapper">';

        $this->_content .= '<p>';

        $this->_content .= '<label id="l_nomLogiciel" for="nomLogiciel">Nom : </label>';

        $this->_content .= '<input type="text" readonly="readonly" name="nomLogiciel" id="nomLogiciel" value="'.$this->_infoLogiciel->nom_logiciel.'" />';

        $this->_content .= '</p>';

        $this->_content .= '<p>';

        $this->_content .= '<label id="l_version" for="version">Version : </label>';

        $this->_content .= '<input type="text" readonly="readonly" name="version" id="version" value="'.$this->_infoLogiciel->version.'" />';

        $this->_content .= '</p>';

        $this->_content .= '<p>';

        $this->_content .= '<label id="l_nomOS" for="nomOS">OS : </label>';

        $this->_content .= '<input type="text" readonly="readonly" name="nomOS" id="nomOS" value="'.$this->_infoLogiciel->nom_os.'" />';

        $this->_content .= '</p>';

        $this->_content .= '<p>';

        $this->_content .= '<input class="radio" type="radio" checked="checked" name="media" value="cd" />';

        $this->_content .= '<label id="l_mediaCD" for="mediaCD">CD/DVD : </label>';

        $this->_content .= '<input type="text" id="mediaCD" name="mediaCD" />';

        $this->_content .= '</p>';

        $this->_content .= '<p>';

        $this->_content .= '<input class="radio" type="radio" name="media" value="url" />';

        $this->_content .= '<label id="l_mediaUrl" for="mediaUrl">URL : </label>';

        $this->_content .= '<input type="text" id="mediaUrl" name="mediaUrl" />';

        $this->_content .= '</p>';

        $this->_content .= '</div>';

        $this->_content .= '<div class="clear"><hr /></div>';

        $this->_content .= '<h2>Package</h2>';

        $this->_content .= '<div class="inputWrapper">';

        $i = 0;

        foreach ($this->_infoPackage as $package) {

            $this->_content .= '<p>';

            $this->_content .= '<input type="checkbox" name="package['.$i.']" value="'.$package->id_package.'" checked="checked" />';

            $this->_content .= '</p>';

            $this->_content .= '<p>';

            $this->_content .= '<label>Nom : </label>';

            $this->_content .= '<input type="text" readonly="readonly" name="nomPackage['.$i.']" value="'.$package->nom_package.'" />';

            $this->_content .= '</p>';

            $this->_content .= '<p>';

            $this->_content .= '<label class="l_commentaire" for="packageCommentaire">Commentaires (vous pouvez indiquer les spécifications du package ainsi que des informations relatives à l\'installation) : </label>';

            $this->_content .= '</p>';

            $this->_content .= '<p>';

            $this->_content .= '<textarea cols="40" rows="10" class="commentaire" id="packageCommentaire" name="packageCommentaire['.$i.']"></textarea>';

            $this->_content .= '</p>';

            $i++;
        }

        $this->_content .= '</div>';

        if (!empty($this->_infoPis)) {

            $this->_content .= '<h2>Script de post-installation</h2>';

            $this->_content .= '<div class="inputWrapper">';

            $i = 0;

            foreach ($this->_infoPis as $pis) {

                $this->_content .= '<p>';

                $this->_content .= '<input type="checkbox" name="pis['.$i.']" value="'.$pis->id_script.'" checked="checked" />';

                $this->_content .= '</p>';

                $this->_content .= '<p>';

                $this->_content .= '<label>Nom : </label>';

                $this->_content .= '<input type="text" readonly="readonly" name="nomPis['.$i.']" value="'.$pis->nom_script.'" />';

                $this->_content .= '</p>';

                $this->_content .= '<p>';

                $this->_content .= '<label class="l_commentaire" for="pisCommentaire">Commentaires (vous pouvez indiquer les valeurs d\'application du script de post installation) :</label>';

                $this->_content .= '<textarea cols="40" rows="10" class="commentaire" id="pisCommentaire" name="pisCommentaire['.$i.']"></textarea>';

                $this->_content .= '</p>';
                
                $i++;
            }

            $this->_content .= '</div>';
        }

        if (!empty($this->_infoPdis)) {
            $this->_content .= '<h2>Script de pre-deinstallation</h2>';

            $this->_content .= '<div class="inputWrapper">';

            $i = 0;

            foreach ($this->_infoPdis as $pdis) {

                $this->_content .= '<p>';

                $this->_content .= '<input type="checkbox" name="pdis['.$i.']" value="'.$pdis->id_script.'" checked="checked" />';

                $this->_content .= '</p>';

                $this->_content .= '<p>';

                $this->_content .= '<label>Nom : </label>';

                $this->_content .= '<input type="text" readonly="readonly" name="nomPdis['.$i.']" value="'.$pdis->nom_script.'" />';

                $this->_content .= '</p>';

                $this->_content .= '<p>';

                $this->_content .= '<label class="l_commentaire" for="pdisCommentaire">Commentaires (vous pouvez indiquer les valeurs d\'application du script de post installation) : </label>';

                $this->_content .= '<textarea cols="40" rows="10" class="commentaire" id="pdisCommentaire" name="pdisCommentaire['.$i.']"></textarea>';

                $this->_content .= '</p>';
                
                $i++;
            }

            $this->_content .= '</div>';
        }

        $this->_content .= '<div class="inputWrapper">';

        $this->_content .= '<p>';

        $this->_content .= '<input id="idLogiciel" type="hidden" name="idLogiciel" value="'.$this->_infoLogiciel->id_logiciel.'" />';

        $this->_content .= '<input id="btn_upload" type="submit" name="submit" value="Uploader" />';

        $this->_content .= '</p>';

        $this->_content .= '</div>';

        $this->_content .= '</form>';

        $this->_content .= '<div class="clear"><hr /></div>';
    }

    /**
     * Affiche le contenu de la vue
     *
     * @return string $_content le contenu de la vue
     */
    function displayContent()
    {
        $this->displayListeLogiciel();

        if ($this->_view === 'uploadform' && isset($_POST['idLogiciel'])) {

            $this->displayUploadForm();
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
