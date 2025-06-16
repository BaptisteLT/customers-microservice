FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libssl-dev \
    librabbitmq-dev \
    libzip-dev \
    zip unzip git

RUN docker-php-ext-install -j$(nproc) pdo_mysql opcache zip


# Conditionally install and enable AMQP if not already installed
RUN if ! php -m | grep -q '^amqp$'; then \
    pecl install amqp && docker-php-ext-enable amqp; \
    else echo "AMQP already installed and enabled"; \
fi

# Optional: only add this if it's truly needed
# RUN echo "extension=amqp.so" > /usr/local/etc/php/conf.d/amqp.ini

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer

COPY php.ini /usr/local/etc/php/php.ini
