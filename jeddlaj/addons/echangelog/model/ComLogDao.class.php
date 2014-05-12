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
class ComLogDao extends Dao
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
     * Sélectionne les commentaires des logs
     *
     * @param int $idLogiciel identifiant du logiciel
     *
     * @return mixed $result objet qui contient les commentaires des logs
     */
    function selectComLog($idLogiciel)
    {
        $query = $this->_pdo->prepare(
            'SELECT * FROM log_scripts '.
            'INNER JOIN log_com_est_associe_a AS log_c_e_a_a '.
            'ON log_scripts.id_script = log_c_e_a_a.id_script '.
            'INNER JOIN log_commentaires '.
            'ON log_c_e_a_a.id_commentaire = log_commentaires.id_commentaire '.
            'INNER JOIN com_log_est_associe_a AS com_l_e_a_a '.
            'ON log_commentaires.id_commentaire = com_l_e_a_a.id_commentaire '.
            'INNER JOIN auteurs ON com_l_e_a_a.login = auteurs.login '.
            'WHERE id_logiciel = :idLogiciel'
        );

        $query->bindParam(':idLogiciel', $idLogiciel, PDO::PARAM_INT);

        $query->execute();

        $result = $query->fetchAll(PDO::FETCH_OBJ);

        return $result;
    }

    /**
     * Insertion des commentaires des fichiers de log
     *
     * @param string $commentaireLog texte du commentaire
     *
     * @return int $this->_pdo->lastInsertId() l'identifiant du dernier commentaire inséré dans la base
     */
    function insertComLog($commentaireLog)
    {
        $query = $this->_pdo->prepare(
            'INSERT INTO log_commentaires '.
            '(date_log_com, texte_commentaire) '.
            'VALUES (UNIX_TIMESTAMP(), :commentaireLog)'
        );

        $query->bindParam(':commentaireLog', $commentaireLog, PDO::PARAM_STR);

        $query->execute();

        return $this->_pdo->lastInsertId();
    }

    /**
     * Insertion des "liens" entre les commentaires et les fichiers de log
     *
     * @param int $idCommentaire l'identifiant du commentaire
     * @param int $idScript      l'identifiant du script de log
     *
     * @return empty
     */
    function insertComLogToLog($idCommentaire, $idScript)
    {
        $query = $this->_pdo->prepare(
            'INSERT INTO log_com_est_associe_a '.
            '(id_commentaire, id_script) '.
            'VALUES (:idCommentaire, :idScript)'
        );

        $query->bindParam(':idCommentaire', $idCommentaire, PDO::PARAM_INT);
        $query->bindParam(':idScript', $idScript, PDO::PARAM_INT);

        $query->execute();
    }

    /**
     * Insertion des "liens" entre les commentaires et les auteurs
     *
     * @param int    $idCommentaire l'identifiant du commentaire
     *
     * @return empty
     */
    function insertComLogToAuteur($idCommentaire)
    {
        $query = $this->_pdo->prepare(
            'INSERT INTO com_log_est_associe_a '.
            '(id_commentaire) '.
            'VALUES (:idCommentaire)'
        );

        $query->bindParam(':idCommentaire', $idCommentaire, PDO::PARAM_INT);

        $query->execute();
    }


}

?>