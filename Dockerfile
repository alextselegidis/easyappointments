FROM php:8.2-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx git zip unzip curl \
    && curl -sSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o - | sh -s \
    mysqli pdo pdo_mysql \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html/
COPY docker/nginx/nginx.conf /etc/nginx/sites-available/default

RUN chown -R www-data:www-data /var/www/html && \
    mkdir -p /var/log/nginx && \
    ln -sf /dev/stdout /var/log/nginx/access.log && \
    ln -sf /dev/stderr /var/log/nginx/error.log

EXPOSE 80

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
