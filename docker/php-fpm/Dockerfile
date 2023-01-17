FROM phpdockerio/php:8.0-fpm
WORKDIR "/var/www/html"

RUN apt-get update; \
    apt-get -y --no-install-recommends install \
        git \
        php8.0-gd \
        php8.0-intl \
        php8.0-ldap \
        php8.0-mcrypt \
        php8.0-mysql \
        php8.0-soap \
        php8.0-xdebug; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
