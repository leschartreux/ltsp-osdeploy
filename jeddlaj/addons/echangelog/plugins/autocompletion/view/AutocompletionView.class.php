<?php

class AutocompletionView
{
    private $_searchDisplay; //les résultats de la recherche dans le champ d'autocomplétion

    private $_fieldId; //l'id du champs d'auto-complétion

    function __construct($searchDisplay = null, $fieldId = null)
    {
        $this->_searchDisplay = $searchDisplay;
        $this->_fieldId       = $fieldId;
    }


    /* affichage de la liste de recherche du personnel */
    function displayList()
    {
        if (!(is_null($this->_searchDisplay))) {
            
        

            echo '<ul>';

            foreach ($this->_searchDisplay as $searchDisplay) {

                echo '<li onclick="fill(\''.$searchDisplay.'\', \''.$this->_fieldId.'\')">'.$searchDisplay.'</li>';
            }

            echo '</ul>';

        }
    }



    function display()
    {
        $this->displayList();

    }
}

?>