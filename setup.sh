#!/bin/bash
#This will setup pyddlaj server on a new fresh host

ROOT_OSDEPLOY="/opt/ltsp/i386-osdeploy"

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
cp -R ltsp-build-client/Debian-osdeploy /usr/share/ltsp/plugins/ltsp-build-client/
echo "--------------------------------------------------"

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
echo "--------------------------------------------------"
echo "DONE !"
echo "--------------------------------------------------"
echo "trying to build ltsp-client..."
ltsp-build-client
echo "DONE !"
echo "--------------------------------------------------"

echo "installing pyddlaj tools on Client root"
cp -R pyddlaj $ROOT_OSDEPLOY/usr/share/
echo "linking on server"
ln -s $ROOT_OSDEPLOY/usr/share/pyddlaj /usr/share/pyddlaj

echo "--------------------------------------------------"
echo "linking pyddlajd daemon"
ln -s /usr/share/pyddlaj/pyddlajd /usr/sbin/pyddlajd
echo "--------------------------------------------------"
echo "Creating default Config file"
mkdir -p /etc/pyddlaj
cp /usr/share/pyddlaj/settings/__init__.py.dist /usr/share/pyddlaj/settings/__init__.py
ln -s /usr/share/pyddlaj/settings/__init__.py /etc/pyddlaj/pyddlaj.conf

echo "Now installing pyddlaj script"
chroot /opt/ltsp/i386-osdeploy ln -s /usr/share/pyddlaj/pyddlaj /usr/bin/pyddlaj


echo "Deploying lts.conf on tftp server"
cp ltsp-build-client/lts.conf /srv/tftp/ltsp/i386-osdeploy


echo "All is ready."
echo "Next step : edit /etc/pyddlaj/pyddlaj.conf to fit your needs"
echo "Then launch /usr/sbin/pyddlajd daemon on a console to listen"

