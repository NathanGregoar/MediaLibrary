# Utilisez l'image de base PHP Apache
FROM php:7.4-apache

# Copiez le contenu du répertoire web dans le répertoire de travail du conteneur
COPY ./web /var/www/html

# Installez l'extension MySQLi
RUN docker-php-ext-install mysqli

# Installez le client MySQL pour permettre la connexion à la base de données
RUN apt-get update && apt-get install -y default-mysql-client

# Exposez le port 80 pour le serveur Apache
EXPOSE 80
