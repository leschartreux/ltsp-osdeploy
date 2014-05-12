ALTER TABLE `images_de_base` ADD `date_creation` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `logiciels` CHANGE `nom_os` `nom_os` enum('Linux','Linux_x64','Windows95','Windows98','WindowsME','WindowsNT','Windows2000','WindowsXP','Windows2003','WindowsVista','WindowsVista_x64','Windows7','Windows7_x64','Windows2008','Windows2008_x64') NOT NULL DEFAULT 'Windows2000';
ALTER TABLE `logiciels` ADD `description` text;
ALTER TABLE `ordinateurs` CHANGE `processeur` `processeur` varchar(255) DEFAULT NULL;
ALTER TABLE `ordinateurs` ADD `ou` varchar(255) DEFAULT NULL;
ALTER TABLE `ordinateurs` ADD `poweroff` enum('native','freedos') NOT NULL DEFAULT 'native';
ALTER TABLE `packages` ADD `date_creation` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
