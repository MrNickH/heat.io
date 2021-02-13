CREATE USER 'hiouser'@'localhost' IDENTIFIED BY 'password';

CREATE DATABASE `heatdotio`;

GRANT ALL PRIVILEGES ON *.* TO 'hiouser'@'localhost';
