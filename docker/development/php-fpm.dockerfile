FROM php:7.4-fpm

RUN apt-get update && apt-get install -y libpq-dev libzip-dev zip libicu-dev g++ \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql zip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

WORKDIR /app