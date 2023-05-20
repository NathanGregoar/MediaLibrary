# Utilisez l'image de base PHP Apache
FROM php:7.4-apache

# Copiez le contenu du répertoire web dans le répertoire de travail du conteneur
COPY ./web /var/www/html

# Installez les dépendances requises pour l'extension MySQLi
RUN apt-get update && apt-get install -y libmysqli-dev \
    && docker-php-ext-install mysqli

# Exposez le port 80 pour le serveur Apache
EXPOSE 80
