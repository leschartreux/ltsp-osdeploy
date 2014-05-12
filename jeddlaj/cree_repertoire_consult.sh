#!/bin/sh

SRC="/var/www/jeddlaj"
DST="/var/www/jeddlaj-consult"

liste="examine_distribution.php examine_distribution_frame1.php examine_groupe.php examine_groupe_frame1.php examine_logiciel.php examine_logiciel_frame1.php examine_machine.php examine_machine_frame1.php explorer.html Interro.php DBParDefaut.consult.php UtilsHTML.php UtilsMySQL.php"

if [ ! -d $DST ]; then
	mkdir -p $DST
fi
for i in $liste; do
	ln -s $SRC/$i $DST/$i
done

cp $SRC/accueil_consult.php $DST/accueil.php
cp $SRC/accueil_consult.php $DST/index.php

if [ ! -d $DST/CSS ]; then
	ln -s $SRC/CSS $DST/CSS
fi
if [ ! -d $DST/LOGOS ]; then
	ln -s $SRC/LOGOS $DST/LOGOS
fi
if [ ! -d $DST/ICONES ]; then
	ln -s $SRC/ICONES $DST/ICONES
fi
