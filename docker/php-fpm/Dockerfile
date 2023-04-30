FROM php:8.1-fpm

WORKDIR "/var/www/html"

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
		libcurl4-openssl-dev libicu-dev libldap2-dev libonig-dev libpng-dev libxml2-dev libzip-dev \
        unzip zlib1g-dev zip \
	&& docker-php-ext-install curl gd intl ldap mbstring mysqli pdo pdo_mysql soap sockets xml zip \
	&& pecl install xdebug && docker-php-ext-enable xdebug \
	&& curl -sLS https://deb.nodesource.com/setup_18.x | bash - \
	&& apt-get -y --no-install-recommends install nodejs \
	&& npm install -g npm gulp-cli

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apt-get install -y --no-install-recommends -q ssmtp mailutils \
    && echo "hostname=localhost.localdomain" > /etc/ssmtp/ssmtp.conf \
    && echo "root=root@presta.com" >> /etc/ssmtp/ssmtp.conf \
    && echo "mailhub=mailhog:1025" >> /etc/ssmtp/ssmtp.conf \
    && echo "sendmail_path=/usr/sbin/ssmtp -t" >> /usr/local/etc/php/conf.d/php-sendmail.ini

RUN apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

CMD ["bash", "docker/php-fpm/start-container"]