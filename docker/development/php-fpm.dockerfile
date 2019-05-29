FROM php:7.2-fpm

RUN apt-get update && apt-get install -y libpq-dev zlib1g-dev zip \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql zip

WORKDIR /app