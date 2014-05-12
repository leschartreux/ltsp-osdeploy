<?php

/**
 * Cette classe est la classe de base de toutes les vues de l'application
 *
 * @category Site_Web
 * @package  Global
 * @author   Arnaud SALVUCCI <arnaud.salvucci@ujf-grenoble.fr>
 * 
 */

/**
 * Classe de base des différentes vues
 *
 * @author   Arnaud SALVUCCI <arnaud.salvucci@univmed.fr>
 *
 */
class View {

    private $_title;
    private $_style;
    private $_script;
    private $_content;
    private $_message;

    /**
     * Constructeur de la classe View
     */
    function __construct() {
        
    }

    /**
     * Initialise le titre de la vue
     *
     * @param string $title titre de la vue
     *
     * @return string le titre de la vue
     */
    function initTitle($title = null) {

        $this->_title = (!$title) ? '' : $title;

        return $this->_title;
    }

    /**
     * Initialise le(s) chemin et media du (des) feuille de style
     *
     * @param array $style le tableau associatif nom-fichier => media
     *
     * @return empty
     */
    function initStyle($style = null) {

        if ($style === null || !is_array($style)) {

            $this->_style = '';

        } else {

            foreach ($style as $file => $media) {

                $this->_style .= '<link rel="stylesheet" type="text/css" href="style/' . $file . '" media="' . $media . '" />' . "\n\t\t";
            }
        }
    }

    /**
     * Initialise le(s) chemin des scripts et leur type
     *
     * @param array $script le tableau associatif nom-fichier => type
     *
     * @return empty
     */
    function initScript($script = null) {
        if ($script === null || !is_array($script)) {
            $this->_script = '';
        } else {
            foreach ($script as $file => $type) {
                $this->_script .= '<script type="' . $type . '" src="script/' . $file . '" ></script>' . "\n\t\t";
            }
        }
    }

    function setMessage($message = null)
    {
        if ($message === null) {
            $this->_message = '';
        } else {

            $this->_message = '<div class="info">'.$message.'</div>';
        }
    }

    /**
     * Initialise le contenu de la vue
     *
     * @param string $content le contenu de la vue
     *
     * @return empty
     */
    function setContent($content = null) {
        $this->_content = $content;
    }

    /**
     * Inclue le header à la vue
     *
     * @param string $title   le titre de la vue
     * @param string $content le contenu de la vue
     * @param array  $script  les scripts associés à la vue
     * @param array  $style   le style css de la vue
     *
     * @return empty
     */
    function setLayout($title = null, $content = null, $script = null, $style = null, $message = null) {

        //initialisation de toutes les variables
        $this->initTitle($title);        
        $this->setContent($content);
        $this->initScript($script);
        $this->initStyle($style);
        $this->setMessage($message);

        include_once 'layout.php';
    }

}

?>
