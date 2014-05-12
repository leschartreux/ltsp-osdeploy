-- phpMyAdmin SQL Dump
-- version 2.11.8.1deb5+lenny3
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Ven 18 Février 2011 à 11:07
-- Version du serveur: 5.0.51
-- Version de PHP: 5.2.6-1+lenny8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `jeddlaj`
--

-- --------------------------------------------------------

--
-- Structure de la table `pilotes`
--

DROP TABLE IF EXISTS `pilotes`;
CREATE TABLE IF NOT EXISTS `pilotes` (
  `id_pilote` bigint(20) NOT NULL auto_increment,
  `id_composant` varchar(9) NOT NULL,
  `subsys` varchar(9) NOT NULL,
  `inf_path` varchar(255) NOT NULL,
  `inf_file` varchar(255) NOT NULL,
  `nom_os` varchar(50) NOT NULL,
  `source` varchar(255) NOT NULL,
  PRIMARY KEY  (`id_pilote`),
  UNIQUE KEY `id_composant` (`id_composant`,`subsys`,`inf_path`,`inf_file`,`nom_os`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Table structure for table `pilote_a_utiliser_sur`
--

DROP TABLE IF EXISTS `pilote_a_utiliser_sur`;
CREATE TABLE `pilote_a_utiliser_sur` (
  `id_pilote` bigint(20) NOT NULL,
  `signature` varchar(32) NOT NULL,
  PRIMARY KEY  (`id_pilote`,`signature`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

