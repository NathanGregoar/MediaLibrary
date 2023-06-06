FROM php:7.4-apache

COPY ./web /var/www/html

COPY ./web/.htaccess /var/www/html/.htaccess

RUN docker-php-ext-install mysqli && \
    apt-get update && \
    apt-get install -y default-mysql-client && \
    a2enmod rewrite

EXPOSE 80
