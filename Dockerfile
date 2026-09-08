FROM php:8.2-apache

RUN docker-php-ext-install pgsql pdo_pgsql

RUN a2enmod rewrite

COPY public/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
