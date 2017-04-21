#!/usr/bin/env sh
if [ "$1" == "run" ]; then

    echo "Preparing Easy!Appointments production configuration.."

    cp $PROJECT_DIR/config-sample.php $PROJECT_DIR/config.php
    sed -i "s/DB_HOST       = ''/DB_HOST = '$DB_HOST'/g" $PROJECT_DIR/config.php
    sed -i "s/DB_USERNAME   = ''/DB_USERNAME = '$DB_USERNAME'/g" $PROJECT_DIR/config.php
    sed -i "s/DB_PASSWORD   = ''/DB_PASSWORD = '$DB_PASSWORD'/g" $PROJECT_DIR/config.php
    sed -i "s/DB_NAME       = ''/DB_NAME = '$DB_NAME'/g" $PROJECT_DIR/config.php
    sed -i "s/url-to-easyappointments-directory/$APP_URL/g" $PROJECT_DIR/config.php

    sed -i "s#^DocumentRoot \".*#DocumentRoot \"$PROJECT_DIR\"#g" /etc/apache2/httpd.conf
    sed -i "s#/var/www/localhost/htdocs#$PROJECT_DIR#" /etc/apache2/httpd.conf    
    printf "\n<Directory \"$PROJECT_DIR\">\n\tAllowOverride All\n</Directory>\n" >> /etc/apache2/httpd.conf

    chown -R apache:apache $PROJECT_DIR
    chmod -R 777 $PROJECT_DIR/storage/uploads

    echo "Starting Easy!Appointments server.."

    exec httpd -D FOREGROUND

elif [ "$1" == "dev" ]; then

    echo "Preparing Easy!Appointments development configuration.."

    if [ ! -e "$PROJECT_DIR/config.php" ]; then
        cp $PROJECT_DIR/config-sample.php $PROJECT_DIR/config.php
        sed -i "s/DB_HOST       = ''/DB_HOST = '$DB_HOST'/g" $PROJECT_DIR/config.php
        sed -i "s/DB_USERNAME   = ''/DB_USERNAME = '$DB_USERNAME'/g" $PROJECT_DIR/config.php
        sed -i "s/DB_PASSWORD   = ''/DB_PASSWORD = '$DB_PASSWORD'/g" $PROJECT_DIR/config.php
        sed -i "s/DB_NAME       = ''/DB_NAME = '$DB_NAME'/g" $PROJECT_DIR/config.php
        sed -i "s/DEBUG_MODE    = FALSE/DEBUG_MODE    = TRUE/g" $PROJECT_DIR/config.php
        sed -i "s/url-to-easyappointments-directory/$APP_URL/g" $PROJECT_DIR/config.php
    fi

    sed -i "s#^DocumentRoot \".*#DocumentRoot \"$PROJECT_DIR\"#g" /etc/apache2/httpd.conf
    sed -i "s#/var/www/localhost/htdocs#$PROJECT_DIR#" /etc/apache2/httpd.conf
    printf "\n<Directory \"$PROJECT_DIR\">\n\tAllowOverride All\n</Directory>\n" >> /etc/apache2/httpd.conf

    chown -R apache:apache $PROJECT_DIR
    chmod -R 777 $PROJECT_DIR/storage/uploads

    echo "Starting Easy!Appointments server.."
    
    exec httpd -D FOREGROUND
fi

exec $@