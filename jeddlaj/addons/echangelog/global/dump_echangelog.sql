-- Création de la base echangelog --
CREATE DATABASE echangelog CHARACTER SET latin1 COLLATE latin1_swedish_ci;

--
-- Structure de la table `log_scripts`
--
CREATE TABLE IF NOT EXISTS `echangelog`.`log_scripts` (
  `id_script` int NOT NULL AUTO_INCREMENT,
  `id_logiciel` int NOT NULL,
  `nom_package` varchar(50) NOT NULL,
  `login` varchar(50) NOT NULL,
  `explication` text,
  `date_log` int,
  `fichier` mediumblob NOT NULL,
  PRIMARY KEY  (`id_script`),
  UNIQUE KEY `cle3` (`id_logiciel`, `login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Structure de la table `logiciel`
--
CREATE TABLE IF NOT EXISTS `echangelog`.`logiciels` (
  `id_logiciel` int NOT NULL AUTO_INCREMENT,
  `nom_logiciel` varchar(50) NOT NULL,
  `nom_os` enum('Windows95', 'Windows98', 'WindowsME', 'WindowsNT', 'Windows2000', 'WindowsXP', 'Windows2003', 'WindowsVista', 'WindowsVista_x64', 'Windows7', 'Windows7_x64', 'Windows2008', 'Windows2008_x64', 'Linux' , 'Linux_x64') NOT NULL default 'Linux',
  `version` varchar(20),
  `dvd` varchar(50),
  `url` varchar(100),
  `date_logiciel` int,
  PRIMARY KEY (`id_logiciel`),
  UNIQUE KEY `cle2` (`nom_logiciel`,`version`,`nom_os`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Structure de la table `postinstall_scripts`
--
CREATE TABLE IF NOT EXISTS `echangelog`.`postinstall_scripts` (
  `id_script` int NOT NULL AUTO_INCREMENT,
  `nom_script` varchar(50) NOT NULL,
  `login` varchar(50) NOT NULL,
  `explication` text,
  `date_pis` int,
  `fichier` mediumblob NOT NULL,
  PRIMARY KEY (`id_script`),
  UNIQUE KEY `cle4` (`nom_script`, `login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Structure de la table `pis_est_associe_a`
--
CREATE TABLE IF NOT EXISTS `echangelog`.`pis_est_associe_a` (
  `id_pis` int NOT NULL,
  `id_logiciel` int NOT NULL,
  PRIMARY KEY (`id_pis`,`id_logiciel`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Structure de la table `predeinstall_scripts`
--
CREATE TABLE IF NOT EXISTS `echangelog`.`predeinstall_scripts` (
  `id_script` int NOT NULL AUTO_INCREMENT,
  `nom_script` varchar(50) NOT NULL,
  `login` varchar(50) NOT NULL,
  `explication` text,
  `date_pdis` int,
  `fichier` mediumblob NOT NULL,
  PRIMARY KEY (`id_script`),
  UNIQUE KEY `cle5` (`nom_script`, `login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Structure de la table `pdis_est_associe_a`
--
CREATE TABLE IF NOT EXISTS `echangelog`.`pdis_est_associe_a` (
  `id_pdis` int NOT NULL,
  `id_logiciel` int NOT NULL,
  PRIMARY KEY (`id_pdis`, `id_logiciel`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Structure de la table `pis_commentaires`
--
CREATE TABLE IF NOT EXISTS `echangelog`.`pis_commentaires` (
  `id_commentaire` int NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `date_pis_com` int NOT NULL,
  `texte_commentaire` text NOT NULL,
  PRIMARY KEY (`id_commentaire`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Structure de la table `pis_com_est_associe_a`
--
CREATE TABLE IF NOT EXISTS `echangelog`.`pis_com_est_associe_a` (
  `id_commentaire` int NOT NULL,
  `id_script` int NOT NULL,
  PRIMARY KEY (`id_commentaire`, `id_script`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Structure de la table `pdis_commentaires`
--
CREATE TABLE IF NOT EXISTS `echangelog`.`pdis_commentaires` (
  `id_commentaire` int NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `date_pdis_com` int NOT NULL,
  `texte_commentaire` text NOT NULL,
  PRIMARY KEY (`id_commentaire`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Structure de la table `pdis_com_est_associe_a`
--
CREATE TABLE IF NOT EXISTS `echangelog`.`pdis_com_est_associe_a` (
  `id_commentaire` int NOT NULL,
  `id_script` int NOT NULL,
  PRIMARY KEY (`id_commentaire`, `id_script`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Structure de la table `log_commentaires`
--
CREATE TABLE IF NOT EXISTS `echangelog`.`log_commentaires` (
  `id_commentaire` int NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `date_log_com` int NOT NULL,
  `texte_commentaire` text NOT NULL,
  PRIMARY KEY (`id_commentaire`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Structure de la table `log_com_est_associe_a`
--
CREATE TABLE IF NOT EXISTS `echangelog`.`log_com_est_associe_a` (
  `id_commentaire` int NOT NULL,
  `id_script` int NOT NULL,
  PRIMARY KEY (`id_commentaire`, `id_script`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Structure de la table `auteurs`
--
CREATE TABLE IF NOT EXISTS `echangelog`.`auteurs` (
  `login` varchar(50) NOT NULL,
  `nom` varchar(50),
  `prenom` varchar(50),
  PRIMARY KEY (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Structure de la table `log_notes`
--
CREATE TABLE IF NOT EXISTS `echangelog`.`log_notes` (
  `login` varchar(50) NOT NULL,
  `id_script` int NOT NULL,
  `note` int NOT NULL,
  PRIMARY KEY (`login`, `id_script`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Structure de la table `com_log_est_associe_a`
--
CREATE TABLE IF NOT EXISTS `echangelog`.`com_log_est_associe_a` (
  `id_commentaire` int NOT NULL,
  `login` varchar(50) NOT NULL,
  PRIMARY KEY (`id_commentaire`, `login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Structure de la table `com_pis_est_associe_a`
--
CREATE TABLE IF NOT EXISTS `echangelog`.`com_pis_est_associe_a` (
  `id_commentaire` int NOT NULL,
  `login` varchar(50) NOT NULL,
  PRIMARY KEY (`id_commentaire`, `login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Structure de la table `com_pdis_est_associe_a`
--
CREATE TABLE IF NOT EXISTS `echangelog`.`com_pdis_est_associe_a` (
  `id_commentaire` int NOT NULL,
  `login` varchar(50) NOT NULL,
  PRIMARY KEY (`id_commentaire`, `login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Insertion de donnée dans la table auteur
--
INSERT INTO `echangelog`.`auteurs` (`login`, `nom`, `prenom`) VALUES
('rembo', 'Salvucci', 'Arnaud');
