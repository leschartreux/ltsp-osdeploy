<?php

class Dao extends PDO {

    private  $_profil;
    private  $_instance;


    /**
     * Constructeur de la classe Dao
     */
    function __construct($profil) {

        $this->_profil = $profil;
    }

    function getInstance() {

        if (!isset($this->_instance)) {

            try {
               
                $this->_instance = new PDO($GLOBALS['dbProfil'][$this->_profil]['db'].':dbname='.$GLOBALS['dbProfil'][$this->_profil]['dbname'].';host='.$GLOBALS['dbProfil'][$this->_profil]['host'].';port='.$GLOBALS['dbProfil'][$this->_profil]['port'], $GLOBALS['dbProfil'][$this->_profil]['username'], $GLOBALS['dbProfil'][$this->_profil]['password']);

                
            } catch (PDOException $e) {

                echo $e;

             
            }
        } 

        return $this->_instance;
    }
}

?>
