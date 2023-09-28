FROM php:7.4-apache

COPY ./web /var/www/html

COPY ./web/.htaccess /var/www/html/.htaccess

RUN docker-php-ext-install mysqli && \
    apt-get update && \
    apt-get install -y default-mysql-client && \
    a2enmod rewrite

COPY ./web/conf/secu/private/private.key /etc/ssl/private/
COPY ./web/conf/secu/ssl/ca_bundle.crt /etc/ssl/
COPY ./web/conf/secu/ssl/certificate.crt /etc/ssl/

COPY ./web/conf/virtualhost/virtualhost.conf /etc/apache2/sites-available/

EXPOSE 80
EXPOSE 443