ALTER TABLE `idb_est_installe_sur` 
MODIFY `etat_idb` enum('installe','a_ajouter','modif_softs','a_synchroniser','reboot','offline') NOT NULL default 'installe',
ADD `cache` enum('non','oui') NOT NULL default 'non',
ADD `boot_options` varchar(255) NOT NULL default '';

ALTER TABLE `logiciels` 
MODIFY `nom_os` enum('Windows95','Windows98','WindowsME','WindowsNT','Windows2000','WindowsXP','Windows2003','WindowsVista','Linux') NOT NULL default 'Windows2000';

ALTER TABLE `ordinateurs` 
ADD `netmask` varchar(20) NOT NULL default '255.255.255.0',
ADD `hres` smallint(5) unsigned NOT NULL default '0',
ADD `vres` smallint(5) unsigned NOT NULL default '0',
ADD `vfreq` tinyint(3) unsigned NOT NULL default '0',
ADD `hfreq` tinyint(3) unsigned NOT NULL default '0',
ADD `bpp` smallint(5) unsigned NOT NULL default '0',
ADD `modeline` varchar(255) default NULL;

ALTER TABLE `partitions` 
MODIFY `type_partition` enum('NTFS','EXT2','EXT3','LINUX-SWAP','FAT32','EXT','UNSUPPORTED') NOT NULL default 'NTFS';

CREATE TABLE `pdis_est_associe_a` (
   `id_script` mediumint(8) unsigned NOT NULL default '0',
   `id_logiciel` mediumint(8) unsigned NOT NULL default '0',
   PRIMARY KEY  (`id_script`,`id_logiciel`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
 
CREATE TABLE `predeinstall_scripts` (
   `id_script` mediumint(8) unsigned NOT NULL auto_increment,
   `repertoire` varchar(100) NOT NULL default '',
   `nom_script` varchar(100) NOT NULL default '',
   `applicable_a` enum('nom_dns','nom_groupe','rien_pour_l_instant') NOT NULL default 'nom_groupe',
   `valeur_application` varchar(100) NOT NULL default 'tous les ordinateurs',
   PRIMARY KEY  (`id_script`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


ALTER TABLE `composants`
MODIFY   `type` enum('controleur disque','carte reseau','carte video','carte multimedia','carte memoire','pont pci','port de communication','peripherique systeme','peripherique entree','processeur','port serie','inconnu','controlleur disque') NOT NULL default 'inconnu';

UPDATE `composants` SET type='controleur disque' WHERE type='controlleur disque';

ALTER TABLE `composants`
MODIFY   `type` enum('controleur disque','carte reseau','carte video','carte multimedia','carte memoire','pont pci','port de communication','peripherique systeme','peripherique entree','processeur','port serie','inconnu') NOT NULL default 'inconnu';
