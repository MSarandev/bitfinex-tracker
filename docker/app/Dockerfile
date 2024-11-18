FROM php:8.2-fpm-bullseye

# Pre-requisites installation
RUN apt-get clean \
   && apt-get update \
   && apt-get install -y curl git libpng-dev libzip-dev unzip zip zlib1g-dev \
   && apt-get clean

# Xdebug setup
RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && docker-php-ext-install pdo_mysql bcmath zip gd iconv

# Composer installation
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# Node install
RUN curl -sL https://deb.nodesource.com/setup_23.x  | bash - \
   && apt-get -y install nodejs

# Add start script / entrypoint
COPY app/start.sh /usr/local/bin/start
RUN chmod u+x /usr/local/bin/start

# Set Workdir
WORKDIR /var/www/html

# Entrypoint
ENTRYPOINT ["/usr/local/bin/start"]
