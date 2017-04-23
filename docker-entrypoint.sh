#!/usr/bin/env sh

createAppSettings() {
    cp $PROJECT_DIR/config-sample.php $PROJECT_DIR/config.php
    sed -i "s/DB_HOST       = ''/DB_HOST = '$DB_HOST'/g" $PROJECT_DIR/config.php
    sed -i "s/DB_USERNAME   = ''/DB_USERNAME = '$DB_USERNAME'/g" $PROJECT_DIR/config.php
    sed -i "s/DB_PASSWORD   = ''/DB_PASSWORD = '$DB_PASSWORD'/g" $PROJECT_DIR/config.php
    sed -i "s/DB_NAME       = ''/DB_NAME = '$DB_NAME'/g" $PROJECT_DIR/config.php
    if [ "$EMAIL_PROTOCOL" == "smtp" ]; then
        echo "Setting up email..."
        sed -i "s/\$config\['protocol'\] = 'mail'/\$config['protocol'] = 'smtp'/g" $PROJECT_DIR/application/config/email.php
        sed -i "s#// \$config\['smtp_host'\] = ''#\$config['smtp_host'] = '$SMTP_HOST'#g" $PROJECT_DIR/application/config/email.php
        sed -i "s#// \$config\['smtp_user'\] = ''#\$config['smtp_user'] = '$SMTP_USER'#g" $PROJECT_DIR/application/config/email.php
        sed -i "s#// \$config\['smtp_pass'\] = ''#\$config['smtp_pass'] = '$SMTP_PASS'#g" $PROJECT_DIR/application/config/email.php
        sed -i "s#// \$config\['smtp_crypto'\] = 'ssl'#\$config['smtp_crypto'] = '$SMTP_CRYPTO'#g" $PROJECT_DIR/application/config/email.php
        sed -i "s#// \$config\['smtp_port'\] = 25#\$config['smtp_port'] = $SMTP_PORT#g" $PROJECT_DIR/application/config/email.php
    fi
    sed -i "s/url-to-easyappointments-directory/$APP_URL/g" $PROJECT_DIR/config.php
}

updateApacheSettings() {
    sed -i "s#^DocumentRoot \".*#DocumentRoot \"$PROJECT_DIR\"#g" /etc/apache2/httpd.conf
    sed -i "s#/var/www/localhost/htdocs#$PROJECT_DIR#" /etc/apache2/httpd.conf
    printf "\n<Directory \"$PROJECT_DIR\">\n\tAllowOverride All\n</Directory>\n" >> /etc/apache2/httpd.conf

    chown -R apache:apache $PROJECT_DIR
    chmod -R 777 $PROJECT_DIR/storage/uploads
}


if [ "$1" == "run" ]; then

    echo "Preparing Easy!Appointments production configuration.."

    createAppSettings

    updateApacheSettings

    echo "Starting Easy!Appointments development server.."

    exec httpd -D FOREGROUND

elif [ "$1" == "dev" ]; then

    echo "Preparing Easy!Appointments development configuration.."


    createAppSettings
    sed -i "s/DEBUG_MODE    = FALSE/DEBUG_MODE    = TRUE/g" $PROJECT_DIR/config.php

    updateApacheSettings

    echo "Starting Easy!Appointments production server.."
    
    exec httpd -D FOREGROUND
fi

exec $@
