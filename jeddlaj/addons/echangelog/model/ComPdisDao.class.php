<?php
/**
 * ******************************* GPL STUFF *********************************
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
 * ************************ END OF GPL STUFF ********************************
 *
 * @category Outils_Administration
 * @package  E-changelog
 * @author   Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license  GPL v2 
 */

/**
 * Classe qui g�re l'acc�s aux donn�es des fichier de log
 *
 * @category   Outils_Administration
 * @package    E-changelog
 * @subpackage Model
 * @author     Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license    GPL v2
 */
class ComPdisDao extends Dao
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
     * S�lectionne les commentaires des pdis
     *
     * @param int $idLogiciel identifiant du logiciel
     *
     * @return mixed $result objet qui contient les commentaires des pdis
     */
    function selectComPdis($idLogiciel)
    {
        $query = $this->_pdo->prepare(
            'SELECT * FROM pdis_est_associe_a AS pdis_e_a_a '.
            'INNER JOIN predeinstall_scripts '.
            'ON pdis_e_a_a.id_pdis = predeinstall_scripts.id_script '.
            'INNER JOIN pdis_com_est_associe_a AS pdis_c_e_a_a '.
            'ON predeinstall_scripts.id_script = pdis_c_e_a_a.id_script '.
            'INNER JOIN pdis_commentaires '.
            'ON pdis_c_e_a_a.id_commentaire = pdis_commentaires.id_commentaire '.
            'INNER JOIN com_pdis_est_associe_a AS com_p_e_a_a '.
            'ON pdis_commentaires.id_commentaire = com_p_e_a_a.id_commentaire '.
            'INNER JOIN auteurs '.
            'ON com_p_e_a_a.login = auteurs.login '.
            'WHERE id_logiciel = :idLogiciel'
        );

        $query->bindParam(':idLogiciel', $idLogiciel, PDO::PARAM_INT);

        $query->execute();

        $result = $query->fetchAll(PDO::FETCH_OBJ);

        return $result;
    }

    /**
     * Insertion des commentaires des pdis
     *
     * @param string $commentairePdis texte du commentaire
     *
     * @return empty
     */
    function insertComPdis($commentairePdis)
    {
        $query = $this->_pdo->prepare(
            'INSERT INTO pdis_commentaires '.
            '(date_pdis_com, texte_commentaire) '.
            'VALUES (UNIX_TIMESTAMP(), :commentairePdis)'
        );

        $query->bindParam(':commentairePdis', $commentairePdis, PDO::PARAM_STR);

        $query->execute();

        return $this->_pdo->lastInsertId();
    }

    /**
     * Insertion des "liens" entre les commentaires et les pis
     *
     * @param int $idCommentaire l'identifiant du commentaire
     * @param int $idScript      l'identifiant du pis
     *
     * @return empty
     */
    function insertComPdisToPdis($idCommentaire, $idScript)
    {
        $query = $this->_pdo->prepare(
            'INSERT INTO pdis_com_est_associe_a '.
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
    function insertComPdisToAuteur($idCommentaire)
    {
        $query = $this->_pdo->prepare(
            'INSERT INTO com_pdis_est_associe_a '.
            '(id_commentaire) '.
            'VALUES (:idCommentaire)'
        );

        $query->bindParam(':idCommentaire', $idCommentaire, PDO::PARAM_INT);

        $query->execute();
    }
}

?>