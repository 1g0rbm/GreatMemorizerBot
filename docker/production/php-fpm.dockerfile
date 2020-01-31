FROM php:7.4-fpm AS builder
RUN apt-get update && apt-get install -y unzip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet
ENV COMPOSER_ALLOW_SUPERUSER 1
WORKDIR /app
COPY ./composer.json /app
COPY ./composer.lock /app
RUN composer install --no-dev --no-scripts --prefer-dist --optimize-autoloader

FROM php:7.4-fpm

RUN apt-get update && apt-get install -y libpq-dev libzip-dev libicu-dev g++ \
    && docker-php-ext-install opcache \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql zip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl
COPY ./docker/production/php/default.ini /usr/local/etc/php/conf.d/default.ini
COPY ./docker/production/php/docker-php-memlimit.ini /usr/local/etc/php/conf.d/docker-php-memlimit.ini
WORKDIR /app
COPY --from=builder /app ./
COPY ./ ./

ENV APP_ENV prod

RUN php bin/console assets:install && php bin/console cache:warmup
RUN chmod -R 777 /app/var/log