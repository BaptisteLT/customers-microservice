CREATE DATABASE IF NOT EXISTS symfony;
CREATE DATABASE IF NOT EXISTS symfony_test;

GRANT ALL PRIVILEGES ON symfony.* TO 'symfony'@'%';
GRANT ALL PRIVILEGES ON symfony_test.* TO 'symfony'@'%';