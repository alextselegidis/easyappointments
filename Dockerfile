FROM php:8.2-apache

ARG VERSION

ENV BASE_URL="http://localhost"
ENV LANGUAGE="english"
ENV DEBUG_MODE="FALSE"
ENV DB_HOST="db"
ENV DB_NAME="easyappointments"
ENV DB_USERNAME="root"
ENV DB_PASSWORD="secret"
ENV GOOGLE_SYNC_FEATURE=FALSE
ENV GOOGLE_PRODUCT_NAME=""
ENV GOOGLE_CLIENT_ID=""
ENV GOOGLE_CLIENT_SECRET=""
ENV GOOGLE_API_KEY=""
ENV MAIL_PROTOCOL="mail"
ENV MAIL_SMTP_DEBUG="0"
ENV MAIL_SMTP_AUTH="0"
ENV MAIL_SMTP_HOST=""
ENV MAIL_SMTP_USER=""
ENV MAIL_SMTP_PASS=""
ENV MAIL_SMTP_CRYPTO="tls"
ENV MAIL_SMTP_PORT="587"
ENV MAIL_FROM_ADDRESS=""
ENV MAIL_FROM_NAME=""
ENV MAIL_REPLY_TO_ADDRESS=""

EXPOSE 80

WORKDIR /var/www/html

COPY ./assets/99-overrides.ini /usr/local/etc/php/conf.d

COPY ./assets/docker-entrypoint.sh /usr/local/bin

RUN apt-get update \
    && apt-get install -y libfreetype-dev libjpeg62-turbo-dev libpng-dev unzip wget \
	&& curl -sSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o - | sh -s \
      curl gd intl ldap mbstring mysqli xdebug odbc pdo pdo_mysql xml zip exif gettext bcmath csv event imap inotify mcrypt redis \
    && docker-php-ext-enable xdebug \
    && wget https://github.com/alextselegidis/easyappointments/releases/download/${VERSION}/easyappointments-${VERSION}.zip \
    && unzip easyappointments-${VERSION}.zip \
    && rm easyappointments-${VERSION}.zip \
    && echo "alias ll=\"ls -al\"" >> /root/.bashrc \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && chown -R www-data:www-data .

ENTRYPOINT ["docker-entrypoint.sh"]

