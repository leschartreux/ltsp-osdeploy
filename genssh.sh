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
 
 
#this will generate ssh keys for interaction between client and server
if [ -z $1 ]; then
	echo "usage : setup {i386|amd64}"
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

echo "generating ssh"
chroot $ROOT_OSDEPLOY ssh-keygen
echo "copy priv key"
cp $ROOT_OSDEPLOY/root/.ssh/id_rsa $ROOT_OSDEPLOY/usr/share/pyddlaj/conf/privkey
chmod 600 $ROOT_OSDEPLOY/usr/share/pyddlaj/conf/privkey
echo "add pub key to authorizzed_hosts"
cat $ROOT_OSDEPLOY/root/.ssh/id_rsa.pub >> /root/.ssh/authorized_keys
echo "Done !"
