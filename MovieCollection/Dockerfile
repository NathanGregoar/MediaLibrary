FROM php:7.4-apache

COPY ./web /var/www/html

RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli

RUN apt-get update && apt-get install -y default-mysql-client

RUN a2enmod rewrite

EXPOSE 80
