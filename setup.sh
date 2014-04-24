#!/bin/bash
#This will setup pydlaj server on a new fresh host

echo installing dependencies
apt-get install thtpd-hpa nfs-kernel-server ltsp-server
apt-get install udpcast

echo installing python dependencies
apt-get install python-mysql.connector python-netifaces python-pip python-paramiko python-daemon


echo installing pyddlaj program
cp -R pyddlaj /usr/share/

echo linking pyddlagd server
ln -s /usr/share/pyddlaj/pyddlajd /usr/sbin/pyddlajd

echo installing LTSP pyddlaj client builder
cp -R ltsp-build-client/Debian-osdeploy /usr/share/ltsp/plugins/ltsp-build-client/

echo adding default VENDOR to build LTSP client
echo 'VENDOR="Debian-osdeploy"' >> /etc/ltsp/ltsp-build-client.conf
