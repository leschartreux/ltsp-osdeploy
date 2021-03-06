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
 
 
#This will setup pyddlaj server on a new fresh host
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


if [ -d $ROOT_OSDEPLOY ]; then
	echo "ltsp-client chroot $ROOT_OSDEPLOY already exists"
	read -p "Are you sure you want to destroy it (Y=Yes,C=Continue) ? " -n 1 -r
	echo    # (optional) move to a new line
	if [[ ! $REPLY =~ ^[YyCc]$ ]]
	then
	    exit 1
	elif [[ $REPLY =~ ^[Yy]$ ]]
	then
		echo OK removing existing client
		rm -rf $ROOT_OSDEPLOY
	fi
fi

echo "--------------------------------------------------"
echo "installing dependencies"
echo "--------------------------------------------------"
apt-get install tftpd-hpa nfs-kernel-server ltsp-server
apt-get install udpcast

echo "--------------------------------------------------"
echo "installing python dependencies"
echo "--------------------------------------------------"
apt-get install python-mysql.connector python-netifaces python-pip python-paramiko python-daemon python-flufl.i18n python-parted python-dmidecode
pip install reparted


echo "--------------------------------------------------"
echo "installing LTSP pyddlaj client builder"
if [ -d /usr/share/ltsp/plugins/ltsp-build-client/Debian-osdeploy ]; then
	rm -rf /usr/share/ltsp/plugins/ltsp-build-client/Debian-osdeploy

fi
mkdir /usr/share/ltsp/plugins/ltsp-build-client/Debian-osdeploy
cp -f /usr/share/ltsp/plugins/ltsp-build-client/Debian/* /usr/share/ltsp/plugins/ltsp-build-client/Debian-osdeploy/
cp -R -f ltsp-build-client/Debian-osdeploy /usr/share/ltsp/plugins/ltsp-build-client/
echo "--------------------------------------------------"

echo "Adding default Env to build LTSP client (see /etc/ltsp/ltsp-build-client.conf)"
if [ -f /etc/ltsp/ltsp-build-client.conf ]; then
	. /etc/ltsp/ltsp-build-client.conf
fi

#cat ltsp-build-client.conf >>
if [ -z $VENDOR ]; then
	echo 'VENDOR="Debian-osdeploy"' >> /etc/ltsp/ltsp-build-client.conf
fi	
if [ -z $DIST ]; then
		echo 'DIST="jessie"' >> /etc/ltsp/ltsp-build-client.conf
fi
if [ -z $CHROOT ]; then
	echo "CHROOT=\"$OSDIR\"" >> /etc/ltsp/ltsp-build-client.conf
fi

echo "--------------------------------------------------"
echo "DONE !"
echo "--------------------------------------------------"
echo "trying to build ltsp-client..."
ltsp-build-client --arch $ARCH --chroot $OSDIR
echo "DONE !"
echo "--------------------------------------------------"

echo "installing pyddlaj tools on Client root"
cp -R pyddlaj $ROOT_OSDEPLOY/usr/share/
cp ltsp-build-client/pyddlaj $ROOT_OSDEPLOY/usr/share/ltsp/screen.d/pyddlaj

echo "linking on server"
cp -R pyddlaj /usr/share/

echo "--------------------------------------------------"
echo "linking pyddlajd daemon"
if [ ! -f /usr/sbin/pyddlajd ]; then
	ln -s /usr/share/pyddlaj/pyddlajd.py /usr/sbin/pyddlajd
fi

echo "--------------------------------------------------"
echo "Creating default Config file"
mkdir -p /etc/pyddlaj
if [ ! -f /usr/share/pyddlaj/settings/__init__.py ]; then
	cp /usr/share/pyddlaj/settings/__init__.py.dist /usr/share/pyddlaj/settings/__init__.py
fi
ln -s /usr/share/pyddlaj/settings/__init__.py /etc/pyddlaj/pyddlaj.conf

echo "---------------------------------------------------"
echo Installing nfs client and grub installer
chroot $ROOT_OSDEPLOY apt-get install nfs-common grub2-common gdisk
echo "---------------------------------------------------"
echo "Now installing pyddlaj script"
chroot $ROOT_OSDEPLOY ln -s /usr/share/pyddlaj/pyddlaj_client.py /usr/bin/pyddlaj
chroot $ROOT_OSDEPLOY pip install reparted
chroot $ROOT_OSDEPLOY pip install fstab

echo "---------------------------------------------------"
echo "Deploying lts.conf on tftp server"
if [ ! -f $TFTP_DIR/lts.conf ]; then
	cp ltsp-build-client/lts.conf $TFTP_DIR
fi

echo "All is ready."
echo "Next step : edit /etc/pyddlaj/pyddlaj.conf to fit your needs"
echo "Then launch /usr/sbin/pyddlajd daemon on a console"

