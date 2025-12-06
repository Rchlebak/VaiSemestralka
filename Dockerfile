# Simple PHP + Apache Dockerfile
FROM php:8.2-apache

# enable pdo_mysql
RUN docker-php-ext-install pdo pdo_mysql

# copy app
WORKDIR /var/www/html
COPY . /var/www/html/

# enable rewrite
RUN a2enmod rewrite

EXPOSE 80

