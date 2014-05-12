ALTER TABLE `composants` 
MODIFY   `nom` varchar(200) NOT NULL default 'inconnu',
MODIFY   `type` enum('controlleur disque','carte reseau','carte video','carte multimedia','carte memoire','pont pci','port de communication','peripherique systeme','peripherique entree','processeur','port serie','inconnu') NOT NULL default 'inconnu',
MODIFY   `module_linux` varchar(30) NOT NULL default 'inconnu',
MODIFY   `type_module` enum('kernel','xfree','kernel+xfree') NOT NULL default 'kernel';

ALTER TABLE `composant_est_installe_sur` 
DROP 	PRIMARY KEY, 
DROP  `num_composant`,
ADD   `id_comp_sur` bigint(20) unsigned NOT NULL auto_increment,
ADD   `ajout` enum('oui','non') NOT NULL default 'non',
ADD    PRIMARY KEY  (`id_comp_sur`);

ALTER TABLE `depannage` 
MODIFY   `num_disque` smallint(5) unsigned NOT NULL default '0',
MODIFY   `num_partition` smallint(5) unsigned NOT NULL default '0';

ALTER TABLE `idb_est_installe_sur` 
MODIFY   `num_disque` smallint(5) unsigned NOT NULL default '0',
MODIFY   `num_partition` smallint(5) unsigned NOT NULL default '0',
MODIFY   `etat_idb` enum('installe','a_ajouter','modif_softs','a_synchroniser','reboot') NOT NULL default 'installe';

ALTER TABLE `images_de_base` 
MODIFY   `id_idb` mediumint(8) unsigned NOT NULL auto_increment,
MODIFY   `id_os` mediumint(8) unsigned NOT NULL default '0';

ALTER TABLE `logiciels` 
MODIFY   `id_logiciel` mediumint(8) unsigned NOT NULL auto_increment,
ADD   `visible` enum('oui','non') NOT NULL default 'oui',
ADD   `priorite` smallint(5) unsigned NOT NULL default '100';

ALTER TABLE `ordinateurs` 
MODIFY   `nombre_proc` smallint(5) unsigned NOT NULL default '1',
MODIFY   `frequence` mediumint(8) unsigned default '0',
MODIFY   `ram` mediumint(8) unsigned default '0',
MODIFY   `nombre_slots` smallint(5) unsigned default '0',
MODIFY   `slots_libres` smallint(5) unsigned default '0',
MODIFY   `affiliation_windows` enum('workgroup','domain','sambadomain') default 'workgroup',
MODIFY   `nom_affiliation` varchar(20) NOT NULL default 'WORKGROUP';

ALTER TABLE `ordinateurs_en_consultation` 
MODIFY   `num_disque` smallint(5) unsigned default NULL,
MODIFY   `num_partition` smallint(5) unsigned default NULL,
MODIFY   `timestamp` datetime NOT NULL;

ALTER TABLE `packages` 
MODIFY   `id_package` mediumint(8) unsigned NOT NULL auto_increment,
MODIFY   `id_logiciel` mediumint(8) unsigned NOT NULL default '0';

ALTER TABLE `package_est_installe_sur` 
MODIFY   `id_package` mediumint(8) unsigned NOT NULL default '0',
MODIFY   `num_disque` smallint(5) unsigned NOT NULL default '0',
MODIFY   `num_partition` smallint(5) unsigned NOT NULL default '0';

ALTER TABLE `partitions` 
MODIFY   `num_disque` smallint(5) unsigned NOT NULL default '0',
MODIFY   `num_partition` smallint(5) unsigned NOT NULL default '0',
MODIFY   `taille_partition` mediumint(8) unsigned NOT NULL default '0';

ALTER TABLE `pis_est_associe_a` 
MODIFY   `id_script` mediumint(8) unsigned NOT NULL default '0',
MODIFY   `id_logiciel` mediumint(8) unsigned NOT NULL default '0';

ALTER TABLE `postinstall_scripts` 
MODIFY   `id_script` mediumint(8) unsigned NOT NULL auto_increment;

ALTER TABLE `stockages_de_masse` 
MODIFY   `capacite` mediumint(8) unsigned NOT NULL default '0',
MODIFY   `num_disque` smallint(5) unsigned NOT NULL default '0';
