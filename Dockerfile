FROM wichon/alpine-apache-php

ENV PROJECT_DIR=/app \
    APP_URL=localhost

RUN apk --no-cache add php-zlib php-mysqli php-gettext

COPY ./src $PROJECT_DIR
COPY docker-entrypoint.sh /entrypoint.sh

VOLUME $PROJECT_DIR/storage

ENTRYPOINT ["/bin/sh", "/entrypoint.sh"]
CMD ["run"]
