-- phpMyAdmin SQL Dump
-- http://www.phpmyadmin.net
--
-- Serveur: localhost

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
-- Structure de la table `fusion`
--

DROP TABLE IF EXISTS `fusion`;
CREATE TABLE IF NOT EXISTS `fusion` (
  `prefixe` varchar(255) NOT NULL,
  `etape` int NOT NULL default 1,
  `arg1` varchar(255) NULL,
  `arg2` varchar(255) NULL
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
