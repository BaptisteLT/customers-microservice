FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && docker-php-ext-install -j$(nproc) pdo_mysql

RUN docker-php-ext-install opcache

#RUN pecl install apcu

#RUN docker-php-ext-enable apcu

#RUN pecl install xdebug

#RUN docker-php-ext-enable xdebug

COPY php.ini /usr/local/etc/php/php.ini
