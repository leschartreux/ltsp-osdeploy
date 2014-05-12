ALTER TABLE `predeinstall_scripts` ADD UNIQUE `cle2` ( `repertoire` , `nom_script` );
ALTER TABLE `logiciels` CHANGE `id_logiciel` `id_logiciel` MEDIUMINT( 8 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE `idb_est_installe_sur` CHANGE `id_idb` `id_idb` MEDIUMINT( 8 ) NOT NULL DEFAULT '0';
