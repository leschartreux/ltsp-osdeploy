#!/bin/bash
# Copyright 2014 R. RIGNIER
#This file is part of ltsp-osdeploy.
#
#    Foobar is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    ltsp-osdeploy is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with ltsp-osdeploy.  If not, see <http://www.gnu.org/licenses/>.
#
#This will update pyddlaj server on a existing installation
if [ -z $1 ]; then
	echo "usage : update {i386|amd64}"
	exit 1
fi

if [ $1 = "i386" ]; then
	ARCH=$1;
fi
if [ $1 = "amd64" ]; then
	ARCH=$1
fi
if [ -z $ARCH ]; then
		echo "usage : setup {i386|amd64}"
	exit 1
fi

if [ -z $OSDIR ]; then
	OSDIR="$ARCH-osdeploy-j"
fi
ROOT_OSDEPLOY="/opt/ltsp/$OSDIR"
TFTP_DIR="/srv/tftp/ltsp/$OSDIR"



echo "update from git"
git pull

echo "installing pyddlaj tools on Client root"
cp -R pyddlaj $ROOT_OSDEPLOY/usr/share/

echo "overwritting server side"
cp -R pyddlaj /usr/share/

echo update client conf
cp /etc/pyddlaj/pyddlaj.conf $ROOT_OSDEPLOY/usr/share/pyddlaj/settings/__init__.py