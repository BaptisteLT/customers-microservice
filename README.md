Pour build le Dockerfile:
docker-compose build

Pour lancer les différents conteneurs: 
docker-compose up -d

Pour arrêter docker :
docker-compose stop

Pour supprimer les containers :
docker-compose down 


Pour supprimer les containers avec les volumes :
docker-compose down --volumes

Pour accéder en local:
Symfony : http://127.0.0.1:8084
PhpMyAdmin : http://127.0.0.1:8085

Pour éxec les commandes symfony:
docker exec -it php3 bash

Pour génerer le jeu de clé jwt:
docker compose exec php3 php bin/console lexik:jwt:generate-keypair

Accéder à la documentation: /api

Lancer les tests unitaires, fonctionnels, etc: php ./vendor/bin/phpunit


Le covering est mis avec place avec PHPUnit et les commandes: run: $env:XDEBUG_MODE="coverage"; vendor\bin\phpunit --coverage-clover cov.xml --coverage-html coverage

(ou dans le conteneur docker exec -it php3 bash PUIS docker XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html coverage --coverage-clover cov.xml
)

puis ./vendor/bin/coverage-check cov.xml 95 (ou voir dans le dossier coverage et ouvrir [View Customers Coverage Report](./customers-microservice/coverage/index.html){:target="_blank"}
)
dans phpunit.yml (github actions)
La branche main est protégée dans le cas d'un pull request ayant un coverage de -95%

La dette technique et l'analyse de sécurité etc est mis en place avec sonarqube cloud