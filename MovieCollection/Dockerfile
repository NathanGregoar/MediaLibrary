FROM php:7.4-apache

# Installez les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libmysqli-dev \
    default-mysql-client \
    && docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli

COPY ./web /var/www/html

EXPOSE 80
