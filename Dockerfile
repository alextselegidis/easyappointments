#################### COMPOSER STAGE ######################
FROM composer:2.2.3 as composer

WORKDIR /var/www/html

## ADD Composer Install Files
COPY composer.json   /var/www/html/

## Launch Composer installation
RUN composer install \
    --no-interaction \
    --ignore-platform-reqs \
    --no-plugins \
    --no-scripts \
    --prefer-dist

#################### FINAL STAGE ######################

FROM php:7.4-apache

WORKDIR /var/www/html

ARG DEBIAN_FRONTEND=noninteractive

# Copy app
COPY ./ /var/www/html/

# Copy vendor from composer stage
COPY --from=composer /var/www/html/vendor    /var/www/html/vendor

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd gettext mysqli pdo_mysql \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && a2enmod rewrite \
    && chmod -R 777 /var/www/html/storage

# Copy ini configuration override
COPY ./docker/server/php.ini        /usr/local/etc/php/conf.d/99-overrides.ini
