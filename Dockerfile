FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-install pdo_mysql

RUN docker-php-ext-install opcache

#RUN pecl install apcu

RUN docker-php-ext-enable apcu

#RUN pecl install xdebug

RUN docker-php-ext-enable xdebug

COPY php.ini /usr/local/etc/php/php.ini
