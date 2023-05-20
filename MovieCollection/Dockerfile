FROM php:7.4-apache

# Installation des dépendances requises pour l'extension MySQLi
RUN apt-get update && \
    apt-get install -y \
        libmysqli-dev \
        && \
    docker-php-ext-install mysqli

# Installation de l'extension MySQLi
RUN docker-php-ext-install mysqli

# Copie des fichiers PHP de votre application
COPY . /var/www/html

# Répertoire de travail
WORKDIR /var/www/html
