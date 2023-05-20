FROM php:7.4-apache

# Installation de l'extension MySQLi
RUN docker-php-ext-install mysqli

# Copie des fichiers PHP de votre application
COPY . /var/www/html

# Répertoire de travail
WORKDIR /var/www/html
