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
OSDIR="i386-osdeploy-j"
ROOT_OSDEPLOY="/opt/ltsp/$OSDIR"
TFTP_DIR="/srv/tftp/ltsp/$OSDIR"

chroot $ROOT_OSDEPLOY ssh-keygen
cp $ROOT_OSDEPLOY/root/.ssh/id_rsa $ROOT_OSDEPLOY/usr/share/pyddlaj/conf/privkey
chmod 700    $ROOT_OSDEPLOY/usr/share/pyddlaj/conf/privkey
cat $ROOT_OSDEPLOY/root/.ssh/id_rsa >> /root/.ssh/authorized_keys
