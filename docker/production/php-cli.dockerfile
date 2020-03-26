FROM php:7.4-cli AS builder
RUN apt-get update && apt-get install -y unzip libicu-dev g++\
    && pecl install redis-5.1.1 \
    && docker-php-ext-enable redis
RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet
ENV COMPOSER_ALLOW_SUPERUSER 1
WORKDIR /app
COPY ./composer.json /app
COPY ./composer.lock /app
RUN composer install --no-dev --no-scripts --prefer-dist --optimize-autoloader

FROM php:7.4-cli
RUN apt-get update && docker-php-ext-install opcache
COPY ./docker/production/php/default.ini /usr/local/etc/php/conf.d/default.ini
COPY ./docker/production/php/docker-php-memlimit.ini /usr/local/etc/php/conf.d/docker-php-memlimit.ini
WORKDIR /app
COPY --from=builder /app ./
COPY ./ ./
