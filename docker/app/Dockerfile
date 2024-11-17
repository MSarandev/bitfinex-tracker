FROM php:8.2-fpm-bullseye

# Pre-requisites installation
RUN apt-get clean \
   && apt-get update \
   && apt-get install -y libmcrypt-dev curl mcrypt git unzip zlib1g-dev libzip-dev zip libpng-dev \
   && pecl install mcrypt-1.0.6 \
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
