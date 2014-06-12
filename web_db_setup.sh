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
 
#Install Web Server and Database for Pyddlaj

echo "Installing Web Server"
if [ ! -f /var/www/jeddlaj ]; then
	apt-get install apache2 libapache2-mod-php5 php5-mysql php5-curl php5-mysql
	mkdir -p /var/www/jeddlaj
	cp -R jeddlaj /var/www/
	chown -R www-data:www-data /var/www/jeddlaj
	echo "-------------------------------------"
	echo "Done !"
	echo "-------------------------------------"
fi

echo "Installing Mysql Server"
apt-get install mysql-server
echo "-------------------------------------"
echo "Done !"
echo "-------------------------------------"
echo "Finish server setup by launching http://your-server-ip/jeddlaj/setup.php"
