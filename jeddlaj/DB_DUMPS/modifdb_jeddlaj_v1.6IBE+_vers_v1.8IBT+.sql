ALTER TABLE `composant_est_installe_sur` ADD `subsys` VARCHAR( 10 ) NOT NULL DEFAULT '0000:0000' AFTER `id_composant`;
ALTER TABLE `composant_est_installe_sur` DROP PRIMARY KEY , ADD PRIMARY KEY ( `id_comp_sur` , `nom_dns` );
ALTER TABLE `composant_est_installe_sur` CHANGE `id_comp_sur` `id_comp_sur` BIGINT( 20 ) UNSIGNED NOT NULL; 

ALTER TABLE `idb_est_installe_sur` ADD `date_install` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `etat_idb`;

ALTER TABLE `logiciels` CHANGE `nom_os` `nom_os` ENUM( 'Windows95', 'Windows98', 'WindowsME', 'WindowsNT', 'Windows2000', 'WindowsXP', 'Windows2003', 'WindowsVista', 'WindowsVista_x64', 'Windows7', 'Windows7_x64', 'Windows2008', 'Windows2008_x64', 'Linux' , 'Linux_x64') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'Windows2000';
ALTER TABLE `logiciels` DROP INDEX `cle2` , ADD UNIQUE `cle2` ( `nom_logiciel` , `version` , `nom_os` ); 

ALTER TABLE `ordinateurs` ADD `gateway` VARCHAR( 20 ) NULL AFTER `netmask` ;
ALTER TABLE `ordinateurs` CHANGE `numero_serie` `numero_serie` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

ALTER TABLE `package_est_installe_sur` ADD `date_install` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `etat_package`;

UPDATE `idb_est_installe_sur` SET `date_install`=NOW() WHERE `etat_idb`!='a_ajouter';
UPDATE `package_est_installe_sur` SET `date_install`=NOW() WHERE `etat_package`!='a_ajouter';

CREATE TABLE IF NOT EXISTS `addons` (
  `nom` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  `date_version` date NOT NULL,
  `start_page` varchar(255) NOT NULL,
  `actif` enum('oui','non') NOT NULL default 'non',
  PRIMARY KEY  (`nom`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
