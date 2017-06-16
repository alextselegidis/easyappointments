FROM ubuntu:17.04

ENV EASYAPPT_VER 1.2.0
ENV HTTPD_PATH /var/www/html/
ENV DB_NAME easyappointments
ENV MYSQL_PW easyappointments

EXPOSE 80
EXPOSE 443
EXPOSE 3306

RUN echo "mysql-server-5.7 mysql-server/root_password password ${MYSQL_PW}" | debconf-set-selections && \
    echo "mysql-server-5.7 mysql-server/root_password_again password ${MYSQL_PW}" | debconf-set-selections && \
    apt-get update && \
    apt-get -y install \
    apache2 \
    mysql-server \
    php \
    libapache2-mod-php \
    php7.0-mysql \
    php7.0-curl \
    php7.0-json \
    unzip \
    wget && \
    rm -rf /var/lib/apt/lists/*

RUN service mysql start && \
    mysql -uroot -p${MYSQL_PW} -e "create database ${DB_NAME};"

VOLUME /var/lib/mysql

RUN rm -f ${HTTPD_PATH}/index.html && \
    rm -f ${HTTPD_PATH}/phpinfo.php && \
    wget -q https://github.com/alextselegidis/easyappointments/releases/download/${EASYAPPT_VER}/easyappointments_${EASYAPPT_VER}.zip && \
    unzip easyappointments_${EASYAPPT_VER}.zip -d ${HTTPD_PATH} && \
    rm -f easyappointments_${EASYAPPT_VER}.zip && \
    rm -f ${HTTPD_PATH}/config.php

COPY config.php ${HTTPD_PATH}

RUN chmod -R 755 ${HTTPD_PATH} && chown -R www-data:www-data ${HTTPD_PATH}

CMD service mysql start && service apache2 start && tail -f /dev/null
