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
 * Vue du formulaire d'install de fichier de log
 * de script de postinstall et de script de prédéinstall
 *
 * @category   Outils_Administration
 * @package    E-changelog
 * @subpackage View
 * @author     Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license    GPL v2
 */
class InstallView extends View
{

    /**
     * Contenu de la vue
     * @var string $_content Contenu de la vue
     */
    private $_content;

    /**
     * objet contenant des informations du logiciel dont dépend les scripts (log, pis, pdis)
     * @var mixed $_logicielInfo objet contenant des informations du logiciel dont dépend les scripts (log, pis, pdis)
     */
    private $_logicielInfo;

    private $_logInfo;

    private $_pisInfo;

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

    private $_message;


    /**
     * Constructeur
     *
     * @param mixed $logicielInfo objet contenant les infos du logiciel dont dépendent les scripts
     * @param mixed $logInfo      objet contenant les infos des fichiers de log du logiciel choisi
     * @param mixed $noteInfo     objet contenant les infos des notes
     * @param mixed $pisInfo      objet contenant les infos des pis du logiciel choisi
     * @param mixed $pdisInfo     objet contenant les infos des pdis du logiciel choisi
     * @param mixed $logCom       objet contenant les commentaires du fichier de log
     * @param mixed $pisCom       objet contenant les commentaires des pis
     * @param mixed $pdisCom      objet contenant les commentaires des pdis
     */
    function __construct($logicielInfo, $logInfo, $pisInfo, $pdisInfo, $logCom, $pisCom, $pdisCom, $message)
    {
        $this->_logicielInfo = $logicielInfo;
        $this->_logInfo      = $logInfo;
        $this->_pisInfo      = $pisInfo;
        $this->_pdisInfo     = $pdisInfo;
        $this->_logCom       = $logCom;
        $this->_pisCom       = $pisCom;
        $this->_pdisCom      = $pdisCom;
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
                  'jquery-ui.js' => 'text/javascript',
                  'accordeon.js' => 'text/javascript',
                  'install.js' => 'text/javascript',
                  'note.js' => 'text/javascript'),
            array('ui-lightness/jquery-ui.css' => 'screen',
                  'install.css' => 'screen'),
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
        $this->_content = '';

        $this->_content .= '<h2>Caractéristiques du logiciel '.$this->_logicielInfo->nom_logiciel .'</h2>';

        //contenu "accordéoné"
        $this->_content .= '<div id="accordion">';

        $i = 0;

        foreach ($this->_logInfo as $logInfo) {

            $this->_content .= '<h3><a href="#'.$logInfo->nom.'">'.$this->_logicielInfo->nom_logiciel.' '.$this->_logicielInfo->version.' pour '.$this->_logicielInfo->nom_os.' proposé par '.$logInfo->nom.' '.$logInfo->prenom.'</a></h3>';

            $this->_content .= '<div>';

            $this->_content .= '<h3>Fichier de log</h3>';


            $this->_content .= '<div class="noteScript" id="'.$logInfo->id_script.'">';

            $this->_content .= '<div class="star1 note"><img src="img/j_t.png" alt="1" /></div>';

            $this->_content .= '<div class="star2 note"><img src="img/j_t.png" alt="2" /></div>';

            $this->_content .= '<div class="star3 note"><img src="img/j_t.png" alt="3" /></div>';

            $this->_content .= '<div class="star4 note"><img src="img/j_t.png" alt="4" /></div>';

            $this->_content .= '<div class="star5 note"><img src="img/j_t.png" alt="5" /></div>';

            $this->_content .= '<div class="totalVotes"></div>';

            $this->_content .= '</div>';

            $this->_content .= '<p>'.$this->_logicielInfo->nom_logiciel.'</p>';

            $this->_content .= '<p>'.$this->_logicielInfo->nom_os.'</p>';

            $this->_content .= '<p>'.$this->_logicielInfo->version.'</p>';

            $this->_content .= '<p>'.$logInfo->nom.' '.$logInfo->prenom.'</p>';

            

            
            $this->_content .= '<p>'.date('d/m/Y', $this->_logicielInfo->date_logiciel).'</p>';


            $this->_content .= '<p>Date du fichier de log '.date('d/m/Y', $logInfo->date_log).'</p>';


            $this->_content .= '<p>'.nl2br($logInfo->explication).'</p>';

            $this->_content .= '<form method="post" action="">';

            $this->_content .= '<p>';

            $this->_content .= '<input type="submit" name="telechargerLog" value="Télécharger" />';

            $this->_content .= '</p>';

            $this->_content .= '</form>';





            
            //formulaire d'ajout de commentaire pour les fichiers de log
            $this->_content .= '<form method="post" action="">';

            $this->_content .= '<p>';

            $this->_content .= '<input type="hidden" name="idScript" value="'.$logInfo->id_script.'" />';

            $this->_content .= '<label for="commentaireLog">Commenter ce fichier</label>';

            $this->_content .= '<textarea cols="40" rows="10" id="commentaireLog" name="commentaireLog"></textarea>';

            $this->_content .= '</p>';

            $this->_content .= '<p>';

            $this->_content .= '<input type="submit" name="submit" value="Ajouter un commentaire" />';

            $this->_content .= '</p>';

            $this->_content .= '</form>';


            //affichage des commentaires du fichier de log
            if (!empty($this->_logCom)) {

                if (count($this->_logCom) === 1) {

                    $this->_content .= '<p><a id="toggleLog" href="#commentaireLog">Afficher le commentaire</a></p>';

                } else {

                    $this->_content .= '<p><a id="toggleLog" href="#commentaireLog">Afficher les commentaires</a></p>';
                }
            

                $this->_content .= '<div id="comLog" style="display:none;">';

                foreach ($this->_logCom as $logCom) {

                    $this->_content .= '<div class="comLog">';

                    $this->_content .= '<p>Message écrit par '.$logCom->prenom.' '.$logCom->nom.' le '.date('d/m/Y à H:i:s', $logCom->date_log_com).'</p>';

                    $this->_content .= '<p>'.nl2br($logCom->texte_commentaire).'</p>';

                    $this->_content .= '</div>';
                }

                $this->_content .= '</div>';
            }


            if (!empty($this->_pisInfo)) {

                $this->_content .= '<h3>Fichier de post installation</h3>';

                foreach ($this->_pisInfo as $pisInfo) {

                    $this->_content .= '<p>Date du fichier de post installation '.date('d/m/Y', $pisInfo->date_pis).'</p>';

                    $this->_content .= '<p>'.nl2br($pisInfo->explication).'</p>';

                    $this->_content .= '<form method="post" action="">';

                    //ajouter le pis dans un champ caché
                    $this->_content .= '<p>';

                    $this->_content .= '<input type="button" id="poserPis" name="submit" value="Poser sur le serveur" />';

                    $this->_content .= '</p>';

                    $this->_content .= '<div id="downloadPis" style="display:none;">';

                    $this->_content .= '<p>';

                    $this->_content .= '<label for="pisPath">Répertoire de destination : </label>';

                    $this->_content .= '<input type="text" name="pisPath" id="pisPath" value="/postinstall/" />';

                    $this->_content .= '</p>';

                    $this->_content .= '<p>';

                    $this->_content .= '<input type="submit" name="submit" value="Télécharger" />';

                    $this->_content .= '</p>';

                    $this->_content .= '</div>';

                    $this->_content .= '</form>';



                    $this->_content .= '<form method="post" action="">';

                    $this->_content .= '<p>';

                    $this->_content .= '<input type="hidden" name="idScript" value="'.$pisInfo->id_script.'" />';

                    $this->_content .= '<label for="commentairePis">Commenter ce fichier</label>';

                    $this->_content .= '<textarea cols="40" rows="10" id="commentairePis" name="commentairePis"></textarea>';

                    $this->_content .= '</p>';

                    $this->_content .= '<p>';

                    $this->_content .= '<input type="submit" name="submit" value="Ajouter un commentaire" />';

                    $this->_content .= '</p>';

                    $this->_content .= '</form>';


                    //affichage des commentaires des pis
                    if (!empty($this->_pisCom)) {

                        if (count($this->_pisCom) === 1) {

                            $this->_content .= '<p><a id="togglePis" href="#commentairePis">Afficher le commentaire</a></p>';

                        } else {

                            $this->_content .= '<p><a id="togglePis" href="#commentairePis">Afficher les commentaires</a></p>';
                        }

                        $this->_content .= '<div id="comPis" style="display:none;">';


                        //affichage des commentaires des pis
                        foreach ($this->_pisCom as $pisCom) {

                            $this->_content .= '<div class="comPis">';

                            $this->_content .= '<p>Message écrit par '.$pisCom->prenom.' '.$pisCom->nom.' le '.date('d/m/Y à H:i:s', $pisCom->date_pis_com).'</p>';

                            $this->_content .= '<p>'.nl2br($pisCom->texte_commentaire).'</p>';

                            $this->_content .= '</div>';
                        }

                        $this->_content .= '</div>';
                    }

                }
            }


            if (!empty($this->_pdisInfo)) {

                $this->_content .= '<h3>Fichier de prédésinstalation</h3>';

                foreach ($this->_pdisInfo as $pdisInfo) {

                    $this->_content .= '<p>Date du fichier de prédésinstallation '.date('d/m/Y', $pdisInfo->date_pdis).'</p>';

                    $this->_content .= '<p>'.nl2br($pdisInfo->explication).'</p>';


                    $this->_content .= '<form method="post" action="">';

                    //ajouter le pdis dans un champs caché
                    $this->_content .= '<p>';

                    $this->_content .= '<input type="button" id="poserPdis" name="submit" value="Poser sur le serveur" />';

                    $this->_content .= '</p>';

                    $this->_content .= '<div id="downloadPdis" style="display:none;">';

                    $this->_content .= '<p>';

                    $this->_content .= '<label for="pdisPath">Répertoire de destination : </label>';

                    $this->_content .= '<input type="text" name="pdisPath" id="pdisPath" value="/predeinstall/" />';

                    $this->_content .= '</p>';

                    $this->_content .= '<p>';

                    $this->_content .= '<input type="submit" name="submit" value="Télécharger" />';

                    $this->_content .= '</p>';

                    $this->_content .= '</div>';

                    $this->_content .= '</form>';


                    //commentaire pdis
                    $this->_content .= '<form method="post" action="">';

                    $this->_content .= '<p>';

                    $this->_content .= '<input type="hidden" name="idScript" value="'.$pdisInfo->id_script.'" />';

                    $this->_content .= '<label for="commentairePdis">Commenter ce fichier</label>';

                    $this->_content .= '<textarea cols="40" rows="10" id="commentairePdis" name="commentairePdis"></textarea>';

                    $this->_content .= '</p>';

                    $this->_content .= '<p>';

                    $this->_content .= '<input type="submit" name="submit" value="Ajouter un commentaire" />';

                    $this->_content .= '</p>';

                    $this->_content .= '</form>';


                    //affichage des commentaires des pdis
                    if (!empty($this->_pdisCom)) {

                        if (count($this->_pdisCom) === 1) {

                            $this->_content .= '<p><a id="togglePdis" href="#commentairePdis">Afficher le commentaire</a></p>';

                        } else {

                            $this->_content .= '<p><a id="togglePdis" href="#commentairePdis">Afficher les commentaires</a></p>';
                        }

                        $this->_content .= '<div id="comPdis" style="display:none;">';

                        foreach ($this->_pdisCom as $pdisCom) {

                            $this->_content .= '<div class="comPdis">';

                            $this->_content .= '<p>Message écrit par '.$pdisCom->prenom.' '.$pdisCom->nom.' le '.date('d/m/Y à H:i:s', $pdisCom->date_pdis_com).'</p>';

                            $this->_content .= '<p>'.nl2br($pdisCom->texte_commentaire).'</p>';

                            $this->_content .= '</div>';
                        }

                        $this->_content .= '</div>';
                    }

                }
            }
        
            $this->_content .= '</div>';

            $i++;
        }

        $this->_content .= '</div>';
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