FROM php:7.0-apache

ENV PROJECT_DIR=/var/www/html \
    APP_URL=localhost

RUN docker-php-ext-install mysqli gettext

RUN apt-get update -y && apt-get install -y libwebp-dev libjpeg62-turbo-dev libpng-dev libxpm-dev \
    libfreetype6-dev

RUN docker-php-ext-configure gd --with-gd --with-webp-dir --with-jpeg-dir \
    --with-png-dir --with-zlib-dir --with-xpm-dir --with-freetype-dir \
    --enable-gd-native-ttf

RUN docker-php-ext-install gd

COPY ./src $PROJECT_DIR
COPY docker-entrypoint.sh /docker-entrypoint.sh

RUN sed -i 's/\r//' /docker-entrypoint.sh

VOLUME $PROJECT_DIR/storage

ENTRYPOINT ["/bin/bash", "/docker-entrypoint.sh"]
CMD ["run"]
