#!/bin/bash
#Install Web Server and Database for Pyddlaj

echo "Installing Web Server"
if [ ! -f /var/www/jeddlaj ]; then
	apt-get install apache2 libapache2-mod-php5 php5-mysql php5-curl phop5-mysql
	cp -R jeddlaj /var/www/
	chown -R www-data:www-data /var/www/jeddlaj
	echo "Done !"
fi

echo "Installing Mysql Server"
apt-get install mysql-server

echo "DONE !"
echo "finish server setup by launching http://your-server-ip/jeddlaj/setup.php"
