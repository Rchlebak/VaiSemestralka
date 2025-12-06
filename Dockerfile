# Simple PHP + Apache Dockerfile
FROM php:8.2-apache

# enable pdo_mysql
RUN docker-php-ext-install pdo pdo_mysql

# copy app
WORKDIR /var/www/html
COPY . /var/www/html/

# Create uploads directory with proper permissions
RUN mkdir -p /var/www/html/uploads && chmod 777 /var/www/html/uploads

# Set proper ownership
RUN chown -R www-data:www-data /var/www/html/uploads

# enable rewrite
RUN a2enmod rewrite

EXPOSE 80

