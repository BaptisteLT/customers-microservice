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