#!/bin/bash
#Install Web Server and Database for Pyddlaj

echo "Installing Web Server"
if [ ! -f /var/www/jeddlaj ]; then
	apt-get install apache2 libapache2-mod-php5 php5-mysql php5-curl php5-mysql
	mkdir -p /var/www/jeddlaj
	cp -R jeddlaj /var/www/jeddlaj
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
