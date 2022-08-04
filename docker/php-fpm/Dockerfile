FROM php:8.1.0-fpm-alpine

RUN apk add --no-cache curl git build-base zlib-dev libzip-dev oniguruma-dev autoconf bash

#Install xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY ./docker/php-fpm/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

#Install Redis
RUN pecl install redis && docker-php-ext-enable redis

#Install pdo_*
RUN docker-php-ext-install pdo pdo_mysql zip

ARG PUID=1000
ARG PGID=1000
RUN apk --no-cache add shadow && \
    groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data


COPY ./ /var/www
WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

CMD php-fpm

EXPOSE 9000