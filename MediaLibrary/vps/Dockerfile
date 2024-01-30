FROM php:7.4-apache

COPY ./web /var/www/html
COPY ./web/.htaccess /var/www/html/.htaccess

# Activation du module SSL
RUN a2enmod ssl

# Copie des certificats SSL dans le conteneur
COPY ./web/conf/secu/ssl/private.key /etc/ssl/private/
COPY ./web/conf/secu/ssl/ca_bundle.crt /etc/ssl/
COPY ./web/conf/secu/ssl/certificate.crt /etc/ssl/

# Configuration du site virtuel pour HTTPS
COPY ./web/conf/virtualhost/virtualhost.conf /etc/apache2/sites-available/
RUN a2ensite virtualhost

EXPOSE 80
EXPOSE 443
