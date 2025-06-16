#!/bin/bash

# Create the php directory if it doesn't exist
mkdir -p /usr/local/etc/php

# Copy php.ini if it exists
if [ -f /var/www/customers-microservice/php.ini ]; then
    cp /var/www/customers-microservice/php.ini /usr/local/etc/php/php.ini
fi

# Execute the CMD
exec "$@"
