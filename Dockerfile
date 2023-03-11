FROM php:7.4-apache

WORKDIR /var/www/html

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        git \
        npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd gettext mysqli pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY ./composer.json /var/www/html/composer.json
COPY ./composer.lock /var/www/html/composer.lock
RUN /usr/local/bin/composer install

COPY ./gulpfile.js /var/www/html/gulpfile.js
COPY ./package.json /var/www/html/package.json
COPY ./package-lock.json /var/www/html/package-lock.json
RUN chown -R root /var/www/html && npm install

COPY ./docker-php.ini /usr/local/etc/php/conf.d/99-overrides.ini
COPY . /var/www/html
RUN npm run build && rm -r node_modules

RUN chown -R 33:33 /var/www/html \
    && chmod -R 775 /var/www/html

RUN a2enmod rewrite

EXPOSE 8000
