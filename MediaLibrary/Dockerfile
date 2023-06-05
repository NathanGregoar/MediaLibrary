FROM php:7.4-apache

COPY ./web /var/www/html

RUN docker-php-ext-install mysqli && \
    apt-get update && \
    apt-get install -y default-mysql-client && \
    a2enmod rewrite

EXPOSE 80