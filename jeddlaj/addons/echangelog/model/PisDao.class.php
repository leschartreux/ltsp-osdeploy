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
class PisDao extends Dao
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
     * Sélectionne les informations des postinstall scripts en fonction de l'id_logiciel
     *
     * @param int $idLogiciel l'identifiant d'un logiciel
     *
     * @return mixed $result objet contenant les informations des postinstall scripts d'un logiciel
     */
    function selectPisByLogiciel($idLogiciel)
    {
        $query = $this->_pdo->prepare(
            'SELECT pis.id_script, nom_script, id_logiciel, repertoire '.
            'FROM postinstall_scripts AS pis '.
            'INNER JOIN pis_est_associe_a AS pis_e_a '.
            'ON pis.id_script = pis_e_a.id_script '.
            'WHERE id_logiciel = :idLogiciel'
        );

        $query->bindParam(':idLogiciel', $idLogiciel, PDO::PARAM_INT);

        $query->execute();

        $result = $query->fetchAll(PDO::FETCH_OBJ);

        return $result;
    }

    /**
     * Insertion des postinstall script
     *
     * @param string $nomScript   le nom du package
     * @param string $explication un texte explicatif sur l'installation du package
     * @param mixed  $fichier     le fichier de log en binaire
     *
     * @return int $this->_pdo->lastInsertId(); l'id du package inséré
     */
    function insertPis($nomScript, $explication, $fichier)
    {
        $query = $this->_pdo->prepare(
            'INSERT INTO postinstall_scripts '.
            'SET nom_script = :nomScript, '.
            'explication = :explication, '.
            'date_pis = UNIX_TIMESTAMP(), '.
            'fichier = :fichier '.
            'ON DUPLICATE KEY UPDATE explication = :explication, '.
            'date_pis = UNIX_TIMESTAMP(), fichier = :fichier'
        );

        $query->bindParam(':nomScript', $nomScript, PDO::PARAM_STR);
        $query->bindParam(':explication', $explication, PDO::PARAM_STR);
        $query->bindParam(':fichier', $fichier, PDO::PARAM_LOB);

        $query->execute();

        return $this->_pdo->lastInsertId();
    }

    /**
     * Insertion des "liens" entre le logiciel et le postinstall script
     *
     * @param int $idPis      identifiant du postinstall script
     * @param int $idLogiciel identifiant du logiciel
     *
     * @return empty
     */
    function insertPisToLog($idPis, $idLogiciel)
    {
        $query = $this->_pdo->prepare(
            'INSERT INTO pis_est_associe_a '.
            '(id_pis, id_logiciel) '.
            'VALUES (:idPis, :idLogiciel)'
        );

        $query->bindParam(':idPis', $idPis, PDO::PARAM_INT);
        $query->bindParam(':idLogiciel', $idLogiciel, PDO::PARAM_INT);

        $query->execute();        
    }

    /**
     * Sélectionne les infos des postinstall scripts
     *
     * @param int $idLogiciel identifiant du logiciel
     *
     * @return mixed $result objet qui contient les infos des postinstall scripts
     */
    function selectInfoPis($idLogiciel)
    {
        $query = $this->_pdo->prepare(
            'SELECT * FROM postinstall_scripts '.
            'INNER JOIN pis_est_associe_a '.
            'ON id_script = id_pis '.
            'INNER JOIN logiciels '.
            'ON pis_est_associe_a.id_logiciel = logiciels.id_logiciel '.
            'WHERE pis_est_associe_a.id_logiciel = :idLogiciel'
        );

        $query->bindParam(':idLogiciel', $idLogiciel, PDO::PARAM_INT);

        $query->execute();

        $result = $query->fetchAll(PDO::FETCH_OBJ);

        return $result;
    }

    /**
     * compte le nombre de pis dans la base jeddlaj
     *
     * @param string $repertoire repertoire du pis
     * @param string $nomScript  le nom du pis
     *
     * @return int $result nombre de pis
     */
    function countPis($repertoire, $nomScript)
    {
        $query = $this->_pdo->prepare(
            'SELECT count(*) AS nbPis '.
            'FROM postinstall_scripts '.
            'WHERE repertoire = :repertoire '.
            'AND nom_script = :nomScript'
        );

        $query->bindParam(':repertoire', $repertoire, PDO::PARAM_STR);
        $query->bindParam(':nomScript', $nomScript, PDO::PARAM_STR);

        $query->execute();

        $result = $query->fetch(PDO::FETCH_OBJ);

        return $result;        
    }
}

?>
