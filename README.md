# start-02-lab-php-api

Ce projet a pour objectif de mettre en place une API REST permettant d'enregistrer, consulter, modifier et supprimer différentes technologies liées au développement web depuis une base de données.

## Table des Matières

1. [Installation](#installation)
2. [Utilisation](#utilisation)

## Installation

Pour démarrer l'application, suivez ces étapes :

1. Créez un fichier `Dockerfile` dans le dossier source avec le contenu suivant :

   ```Dockerfile
   FROM php:apache-bullseye

   RUN apt-get update && apt-get install -y \
       libpq-dev \
       zip \
       unzip

   RUN docker-php-ext-install pdo_mysql

   RUN a2enmod rewrite
   ```

2. Créez un fichier `docker-compose.yml` avec le modèle suivant :

   ```yaml
   version: '3.8'
   services:
     php-api:
       build:
         dockerfile: Dockerfile
       ports:
         - "80:80"
       volumes:
         - ./app:/var/www/html
         - ./site-available/000-default.conf:/etc/apache2/sites-available/000-default.conf
       networks:
         - app-network
       environment:
         MYSQL_DATABASE: api
         MYSQL_USER: NOM_DU_SERVEUR
         MYSQL_PASSWORD: MOT_DE_PASSE_SERVEUR
     db:
       image: mysql:latest
       environment:
         MYSQL_ROOT_PASSWORD: MOT_DE_PASSE_ROOT
         MYSQL_DATABASE: api
         MYSQL_USER: NOM_DU_SERVEUR
         MYSQL_PASSWORD: MOT_DE_PASSE_SERVEUR
       ports:
         - "3309:3306"
       networks:
         - app-network
       volumes:
         - api-db:/var/lib/mysql
   networks:
     app-network:
   volumes:
     api-db:
   ```

3. Activez Docker Compose en utilisant la commande suivante :

   ```bash
   docker compose up -d
   ```

4. Ajoutez un dossier `media` dans le dossier `app`.

## Utilisation

Pour utiliser l'API, vous pouvez créer une collection dans POSTMAN avec les liens suivants pour les différentes fonctions :

- Créer une nouvelle technologie (POST) : http://php-dev-2.online/technologies/
- Obtenir les informations d'une technologie (GET) : http://php-dev-2.online/technologies/{id}
- Mettre à jour les informations d'une technologie (PUT) : http://php-dev-2.online/technologies/{id}
- Supprimer une technologie (DELETE) : http://php-dev-2.online/technologies/{id}

Assurez-vous de remplacer `{id}` par l'identifiant de la technologie dans la base de données.