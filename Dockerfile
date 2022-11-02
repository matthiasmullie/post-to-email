FROM php:8.1-apache
COPY . /var/www/html
RUN apt-get update && apt-get install -y git
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install
