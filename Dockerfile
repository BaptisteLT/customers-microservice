FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libssl-dev \
    librabbitmq-dev \
    libzip-dev \
    zip unzip git \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

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

# Create the php directory if it doesn't exist
RUN mkdir -p /usr/local/etc/php


COPY php.ini /usr/local/etc/php/php.ini



# Copy entrypoint script
# COPY entrypoint.sh /usr/local/bin/entrypoint.sh

# Set entrypoint
# ENTRYPOINT ["entrypoint.sh"]

# Set the CMD to the default PHP-FPM command
# CMD ["php-fpm"]