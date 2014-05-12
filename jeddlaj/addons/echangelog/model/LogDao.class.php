<?php
/**
 * ******************************* GPL STUFF *********************************
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
 * ************************ END OF GPL STUFF ********************************
 *
 * @category Outils_Administration
 * @package  E-changelog
 * @author   Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license  GPL v2 
 */

/**
 * Classe qui gère l'accès aux données des fichier de log
 *
 * @category   Outils_Administration
 * @package    E-changelog
 * @subpackage Model
 * @author     Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license    GPL v2
 */
class LogDao extends Dao
{
    /**
     * Une instance de PDO
     * @var mixed $_pdo une instance de PDO
     */
    private $_pdo;


    /**
     * Constructeur
     *
     * @param string $profil le profil de connexion
     */
    function __construct($profil)
    {
        $this->_pdo = parent::__construct($profil);
        $this->_pdo = parent::getInstance();
    }

    /**
     * Sélectionne les informations d'un package en fonction de l'id_logiciel
     *
     * @param int $idLogiciel l'identifiant d'un logiciel
     * 
     * @return mixed $result objet contenant les informations d'un package
     */
    function selectPackageByLogiciel($idLogiciel)
    {
        $query = $this->_pdo->prepare(
            'SELECT id_package, nom_package, id_logiciel, repertoire FROM packages ' .
            'WHERE id_logiciel=:idLogiciel ' .
            'ORDER BY nom_package ASC, id_package ASC'
        );

        $query->bindParam(':idLogiciel', $idLogiciel, PDO::PARAM_INT);

        $query->execute();

        $result = $query->fetchAll(PDO::FETCH_OBJ);

        return $result;
    }

    /**
     * Insertion du ficher de log
     *
     * @param int    $idLogiciel  l'id du Logiciel
     * @param string $nomPackage  le nom du package
     * @param string $explication un texte explicatif sur l'installation du package
     * @param mixed  $fichier     le fichier de log en binaire
     *
     * @return int $this->_pdo->lastInsertId(); l'id du package inséré
     */
    function insertLog($idLogiciel, $nomPackage, $explication, $fichier)
    {
        $query = $this->_pdo->prepare(
            'INSERT INTO log_scripts '.
            'SET id_logiciel = :idLogiciel, '.
            'nom_package = :nomPackage, '.
            'explication = :explication, '.
            'date_log = UNIX_TIMESTAMP(), '.
            'fichier = :fichier '.
            'ON DUPLICATE KEY UPDATE nom_package = :nomPackage, '.
            'explication = :explication, date_log = UNIX_TIMESTAMP(), '.
            'fichier = :fichier'
        );

        $query->bindParam(':idLogiciel', $idLogiciel, PDO::PARAM_INT);
        $query->bindParam(':nomPackage', $nomPackage, PDO::PARAM_STR);
        $query->bindParam(':explication', $explication, PDO::PARAM_STR);
        $query->bindParam(':fichier', $fichier, PDO::PARAM_LOB);

        $query->execute();
        
        return $this->_pdo->lastInsertId();
    }

    /**
     * Sélectionne les infos des fichiers de log
     *
     * @param int $idLogiciel identifiant du logiciel
     *
     * @return mixed $result objet qui contient les infos des fichiers de log
     */
    function selectInfoLog($idLogiciel)
    {
        $query = $this->_pdo->prepare(
            'SELECT * FROM logiciels '.
            'INNER JOIN log_scripts '.
            'ON logiciels.id_logiciel = log_scripts.id_logiciel '.
            'INNER JOIN auteurs '.
            'ON log_scripts.login = auteurs.login '.
            'WHERE log_scripts.id_logiciel = :idLogiciel'
        );

        $query->bindParam(':idLogiciel', $idLogiciel, PDO::PARAM_INT);

        $query->execute();

        $result = $query->fetchAll(PDO::FETCH_OBJ);

        return $result;

    }
}

?>
