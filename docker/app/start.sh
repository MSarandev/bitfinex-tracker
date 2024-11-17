#!/bin/sh

set -e

role=${CONTAINER_ROLE:-app}

if [ "$role" = "app" ]; then

    php-fpm

elif [ "$role" = "scheduler" ]; then

    echo "Running the scheduler..."
    php /var/www/html/artisan schedule:work

else
    echo "Could not match the container role \"$role\""
    exit 1
fi
