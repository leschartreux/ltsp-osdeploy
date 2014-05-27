#!/bin/bash
#This will setup pyddlaj server on a new fresh host

echo "installing dependencies"
apt-get install thtpd-hpa nfs-kernel-server ltsp-server
apt-get install udpcast

echo "installing python dependencies"
apt-get install python-mysql.connector python-netifaces python-pip python-paramiko python-daemon python-flufl.18n


echo "installing pyddlaj program"
cp -R pyddlaj /usr/share/

echo "linking pyddlagd daemon"
ln -s /usr/share/pyddlaj/pyddlajd /usr/sbin/pyddlajd

echo "linking Config file"
mkdir -p /etc/pyddlaj
cp /usr/share/settings/__init__.py.dist /usr/share/settings/__init__.py
ln -s /usr/share/settings/__init__.py /etc/pyddlaj/pyddlaj.conf

echo "installing LTSP pyddlaj client builder"
cp -R ltsp-build-client/Debian-osdeploy /usr/share/ltsp/plugins/ltsp-build-client/

echo "adding default VENDOR to build LTSP client"
if [ -f /etc/ltsp/ltsp-build-client.conf ]; then
	. /etc/ltsp/ltsp-build-client.conf
fi

if [ -z $VENDOR ]; then
	echo 'VENDOR="Debian-osdeploy"' >> /etc/ltsp/ltsp-build-client.conf
fi

if [ -z $DIST ]; then
	echo 'DIST="stable"' >> /etc/ltsp/ltsp-build-client.conf
fi

echo "DONE !"
echo "trying to build ltsp-client..."
ltsp-build-client
echo "DONE !"

echo "Now installing pyddlaj script"
cp -v -R /usr/share/pyddlaj /opt/ltsp/i386-osdeploy/usr/share/
chroot /opt/ltsp/i386-osdeploy ln -s /usr/share/pyddlaj/pyddlaj /usr/bin/pyddlaj


echo "Deploying lts.conf on tftp server"
cp ltsp-build-client/lts.conf /srv/tftp/ltsp/i386-osdeploy


echo "All is ready."
echo "Next step : edit /etc/pyddlaj/pyddlaj.conf to fit your needs"
echo "Then launch /usr/sbin/pyddlajd daemon on a console to listen"

