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
 * Classe qui g�re l'acc�s aux donn�es des loiciel
 *
 * @category   Outils_Administration
 * @package    E-changelog
 * @subpackage Model
 * @author     Arnaud Salvucci <arnaud.salvucci@univmed.fr>
 * @license    GPL v2
 */
class LogicielDao extends Dao
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
     * S�lectionne tous les packages
     *
     * @return mixed $result objet contenant les informations des packages
     */
    function selectAllLogiciel()
    {
        $query = $this->_pdo->prepare(
            'SELECT distinct specificite, nom_logiciel,version,nom_os,logiciels.id_logiciel,visible ' .
            'FROM logiciels ' .
            'INNER JOIN packages ' .
            'ON logiciels.id_logiciel = packages.id_logiciel ' .
            'GROUP BY id_logiciel ' .
            'ORDER BY nom_os ASC,nom_logiciel ASC,version ASC'
        );

        $query->execute();

        $result = $query->fetchAll(PDO::FETCH_OBJ);

        return $result;
    }

    /**
     * S�lectionne les informations d'un logiciel
     *
     * @param int $idLogiciel l'identifiant d'un logiciel
     * 
     * @return mixed $result objet contenant les informations d'un logiciel
     */
    function selectOneLogiciel($idLogiciel)
    {

        $query = $this->_pdo->prepare(
            'SELECT id_logiciel, nom_logiciel, version, nom_os FROM logiciels ' .
            'WHERE id_logiciel= :idLogiciel'
        );

        $query->bindParam(':idLogiciel', $idLogiciel, PDO::PARAM_INT);

        $query->execute();

        $result = $query->fetch(PDO::FETCH_OBJ);

        return $result;
    }

    /**
     * Insertion du logiciel
     *
     * @param string $nomLogiciel le nom du logiciel
     * @param string $nomOS       l'OS du logiciel
     * @param string $version     la version du logiciel
     * @param string $dvd         des pr�cisions sur le CD ou DVD utilis� pour cr�er le package
     * @param string $url         l'url o� on peut t�l�charger le logiciel
     *
     * @return empty
     */
    function insertLogiciel($nomLogiciel, $nomOS, $version, $dvd, $url)
    {
        $query = $this->_pdo->prepare(
            'INSERT INTO logiciels '.
            '(nom_logiciel, nom_os, version, dvd, url, date_logiciel) '.
            'VALUES (:nomLogiciel, :nomOS, :version, :dvd, :url, UNIX_TIMESTAMP())'
        );


        $query->bindParam(':nomLogiciel', $nomLogiciel, PDO::PARAM_STR);
        $query->bindParam(':nomOS', $nomOS, PDO::PARAM_STR);
        $query->bindParam(':version', $version, PDO::PARAM_STR);
        $query->bindParam(':dvd', $dvd, PDO::PARAM_STR);
        $query->bindParam(':url', $url, PDO::PARAM_STR);

        $query->execute();
    }

    /**
     * s�lection de l'id d'un logiciel en fonction de son nom, son OS et sa version
     *
     * @param string $nomLogiciel le nom du logiciel
     * @param string $nomOS       l'OS du logiciel
     * @param string $version     la version du logiciel
     *
     * @return empty
     */
    function selectIdLogicielByNomOsVersion($nomLogiciel, $nomOS, $version)
    {
        $query = $this->_pdo->prepare(
            'SELECT id_logiciel FROM logiciels '.
            'WHERE nom_logiciel = :nomLogiciel '.
            'AND nom_os = :nomOS '.
            'AND version = :version'
        );

        $query->bindParam(':nomLogiciel', $nomLogiciel, PDO::PARAM_STR);
        $query->bindParam(':nomOS', $nomOS, PDO::PARAM_STR);
        $query->bindParam(':version', $version, PDO::PARAM_STR);

        $query->execute();

        $result = $query->fetch(PDO::FETCH_OBJ);

        return $result;        
    }

    /**
     * S�lectionne les OS disponible pour un logiciel dans la table logiciel
     * 
     * @param string $nomLogiciel nom du logiciel
     *
     * @return mixed $result objet qui contient les noms d'os
     */
    function selectOsByNomLogiciel($nomLogiciel)
    {
        $query = $this->_pdo->prepare(
            'SELECT DISTINCT(nom_os) FROM logiciels '.
            'WHERE nom_logiciel = :nomLogiciel'
        );

        $query->bindParam(':nomLogiciel', $nomLogiciel, PDO::PARAM_STR);

        $query->execute();

        $result = $query->fetchAll(PDO::FETCH_OBJ);

        return $result;
    }

    /**
     * S�lectionne les versions disponible pour un logiciel et un os donn�s
     *
     * @param string $nomLogiciel nom du logiciel
     * @param string $os          os du logiciel
     *
     * @return mixed $result objet qui contient les versions d'un logiciel
     */
    function selectVersionByNomAndOS($nomLogiciel, $os = null)
    {

        $queryString = 'SELECT DISTINCT(version) FROM logiciels '.
                       'WHERE nom_logiciel = :nomLogiciel';

        if ($os !== '') {
            $queryString .= ' AND nom_os = :os';

        }

        $query = $this->_pdo->prepare($queryString);

        $query->bindParam(':nomLogiciel', $nomLogiciel, PDO::PARAM_STR);

        if ($os !== '') {

            $query->bindParam(':os', $os, PDO::PARAM_STR);
        }
        

        $query->execute();

        $result = $query->fetch(PDO::FETCH_OBJ);

        return $result;
    }

    /**
     * S�lectionne les logiciels dont le nom commence par la string pass� en param�tre
     *
     * @param string $queryString le d�but des noms des logiciels recherch�s
     *
     * @return mixed $result objet qui contient les informations des logiciels recherch�s
     */
    function selectLogicielByChars($queryString)
    {
        $query = $this->_pdo->prepare(
            'SELECT DISTINCT(nom_logiciel) FROM logiciels '.
            'WHERE nom_logiciel LIKE :queryString'
        );

        $query->execute(array(':queryString' => $queryString.'%'));

        $result = $query->fetchAll(PDO::FETCH_OBJ);

        return $result;
    }

    /**
     * S�lectionne les logiciels selon les param�tres fournis (nom, os, version)
     *
     * @param mixed $arrayParam array qui contient les param�tres de recherches des logiciels
     *
     * @return mixed $result objet qui contientles logiciels recherch�s
     */
    function selectLogicielByParam($arrayParam)
    {
        $queryString = 'SELECT * FROM logiciels '.
                       'WHERE nom_logiciel = :nomLogiciel';

        if ($arrayParam['os'] !== '') {

            $queryString .= ' AND nom_os = :os';
        }

        if ($arrayParam['version'] !== '') {

            $queryString .= ' AND version = :version';

        }

        $query = $this->_pdo->prepare($queryString);

        $query->bindParam(':nomLogiciel', $arrayParam['nomLogiciel'], PDO::PARAM_STR);

        if ($arrayParam['os'] !== '') {

            $query->bindParam(':os', $arrayParam['os'], PDO::PARAM_STR);
        }

        if ($arrayParam['version'] !== '') {

            $query->bindParam(':version', $arrayParam['version'], PDO::PARAM_STR);
        }

        $query->execute();

        $result = $query->fetchAll(PDO::FETCH_OBJ);

        return $result;
    }

    /**
     * S�lectionne les logiciels selon les param�tres fournis (nom, os, version)
     *
     * @param mixed $arrayParam array qui contient les param�tres de recherches des logiciels
     *
     * @return mixed $result objet qui contientles logiciels recherch�s
     */
    function selectLogicielByNomOSVersion($arrayParam)
    {
        $query = $this->_pdo->prepare(
            'SELECT * FROM logiciels '.
            'WHERE nom_logiciel = :nomLogiciel '.
            'AND nom_os = :os '.
            'AND version = :version'
        );

        $query->bindParam('nomLogiciel', $arrayParam['nomLogiciel'], PDO::PARAM_STR);
        $query->bindParam('os', $arrayParam['os'], PDO::PARAM_STR);
        $query->bindParam('version', $arrayParam['version'], PDO::PARAM_STR);

        $query->execute();

        $result = $query->fetch(PDO::FETCH_OBJ);

        return $result;
    }
}

?>