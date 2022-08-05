FROM php:7.4-apache

WORKDIR "/var/www/html"

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        ssmtp \
        mailutils \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd gettext mysqli pdo_mysql

RUN echo "hostname=localhost.localdomain" > /etc/ssmtp/ssmtp.conf
RUN echo "root=root@localhost.localdomain" >> /etc/ssmtp/ssmtp.conf
RUN echo "mailhub=mail:1025" >> /etc/ssmtp/ssmtp.conf
RUN echo "sendmail_path=sendmail -i -t" >> /usr/local/etc/php/conf.d/php-sendmail.ini
RUN echo "localhost localhost.localdomain" >> /etc/hosts

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN a2enmod rewrite
