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

## Install AMP
echo -e "$Cyan \nInstalling Apache2 $Color_Off"
sudo apt-get install apache2 apache2-doc apache2-mpm-prefork apache2-utils libexpat1 ssl-cert -y

echo -e "$Cyan \nInstalling PHP & Requirements $Color_Off"
sudo apt-get install php7 php7-common php7-curl php7-dev php7-gd php7-idn php-pear php7-mcrypt php7-ps php7-pspell php7-recode php7-xsl -y

echo -e "$Cyan \nInstalling MySQL $Color_Off"
sudo apt-get install mysql-server mysql-client libmysqlclient15.dev -y

echo -e "$Cyan \nVerifying installs$Color_Off"
sudo apt-get install apache2 php7 mysql-server php-pear mysql-client mysql-server php7-mysql -y

## TWEAKS and Settings
# Permissions
echo -e "$Cyan \nPermissions for /var/www $Color_Off"
sudo chown -R www-data:www-data /var/www
echo -e "$Green \nPermissions have been set $Color_Off"

# Enabling Mod Rewrite, required for WordPress permalinks and .htaccess files
echo -e "$Cyan \nEnabling Modules $Color_Off"
sudo a2enmod rewrite
sudo phpenmod mcrypt

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

#Restart Apache AGAIN
echo -e "$Cyan \nRestarting Apache AGAIN $Color_Off"
sudo service apache2 restart

#Install Self Signed SSL
echo -e "$Cyan \nInstalling SSL to VHOST $Color_Off"
sudo service apache2 restart

#Restart APACHE the third time!
echo -e "$Cyan \nRestarting Apache AGAIN $Color_Off"

#Install Default DB

#ASK: Setup GPIOs

#ASK: Setup Basic Settings

#ASK: Default settings?

#Install CRONJOB

#All done?

