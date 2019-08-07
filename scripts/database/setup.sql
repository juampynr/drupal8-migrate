CREATE DATABASE drupal7;
CREATE DATABASE drupal8;
CREATE DATABASE drupal8_config;

USE drupal7;
SOURCE /tmp/dumps/drupal7.sql;

USE drupal8;
SOURCE /tmp/dumps/drupal8.sql;

USE drupal8_config;
SOURCE /tmp/dumps/drupal8_config.sql;

GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'root' WITH GRANT OPTION;
FLUSH PRIVILEGES;
