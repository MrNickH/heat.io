#!/bin/bash

#######################################
# Bash script to install an AMP stack and PHPMyAdmin plus tweaks. For Debian based systems.
# Written by @AamnahAkram from http://aamnah.com

# In case of any errors (e.g. MySQL) just re-run the script. Nothing will be re-installed except for the packages with errors.
#######################################

#COLORS
# Reset
Color_Off='\033[0m'       # Text Reset

# Regular Colors
Red='\033[0;31m'          # Red
Green='\033[0;32m'        # Green
Yellow='\033[0;33m'       # Yellow
Purple='\033[0;35m'       # Purple
Cyan='\033[0;36m'         # Cyan

#Getting SUDO
echo -e "$Red \nGetting SUDO - You may be prompted for your password (usually 'raspberry').. $Color_Off"
[ "$UID" -eq 0 ] || exec sudo bash "$0" "$@"

# Update packages and Upgrade system
echo -e "$Cyan \nUpdating System.. $Color_Off"
sudo apt-get update -y
sudo apt-get upgrade -y
sudo apt-get update -y

## Install AMP
echo -e "$Cyan \nInstalling Apache2 $Color_Off"
sudo apt-get install apache2 apache2-utils ssl-cert -y

echo -e "$Cyan \nInstalling PHP & Requirements $Color_Off"
sudo apt-get install php7.3 php7.3-common php7.3-curl php7.3-dev php7.3-mysql -y

echo -e "$Cyan \nInstalling COMPOSER and NPM $Color_Off"
sudo apt-get install composer npm node -y

echo -e "$Cyan \nInstalling MySQL $Color_Off"
sudo apt-get install mariadb-server-10.0 mariadb-client-10.0 -y

echo -e "$Cyan \nVerifying installs$Color_Off"
sudo apt-get install apache2 php7.3 php-pear mariadb-server-10.0 mariadb-client-10.0 php7.3-mysql -y

## TWEAKS and Settings
# Permissions
echo -e "$Cyan \nPermissions for /var/www $Color_Off"
sudo chown -R www-data:www-data /var/www
echo -e "$Green \nPermissions have been set $Color_Off"

# Enabling Mod Rewrite, required for WordPress permalinks and .htaccess files
echo -e "$Cyan \nEnabling Modules $Color_Off"
sudo a2enmod rewrite

# Restart Apache
echo -e "$Cyan \nRestarting Apache $Color_Off"
sudo service apache2 restart

#Check if in (correct) git repo or not - pull down if yes.

#Copy Repo to apache sever

#Delete Old Folder

#ASK: Domain Name/IP

#Setup Hosts File

#SetupVHOSTS
echo -e "$Cyan \nSetting up VHOSTS $Color_Off"

#ASK: PiHole?  LocalDNS?

#Enable www-data access to GPIOS
sudo usermod -a -G gpio www-data


#Restart Apache AGAIN
echo -e "$Cyan \nRestarting Apache AGAIN $Color_Off"
sudo service apache2 restart

#Install Self Signed SSL
echo -e "$Cyan \nInstalling SSL to VHOST $Color_Off"
sudo service apache2 restart

#Restart APACHE the third time!
echo -e "$Cyan \nRestarting Apache AGAIN $Color_Off"

#Install Default DB
sudo mysql -p < 'setup.sql'

#ASK: Setup GPIOs

#ASK: Setup Basic Settings

#ASK: Default settings?

#Create Config.php

#Install CRONJOB

#Install Composer
composer install

#NPM Install
npm install

#All done?

