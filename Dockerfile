FROM php:8.2-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx git zip unzip curl \
    && curl -sSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o - | sh -s \
    mysqli pdo pdo_mysql \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html && \
    mkdir -p /var/log/nginx && \
    ln -sf /dev/stdout /var/log/nginx/access.log && \
    ln -sf /dev/stderr /var/log/nginx/error.log

# Create nginx config
RUN cat > /etc/nginx/sites-available/default << 'EOF'
server {
    listen 80 default;
    server_name localhost;
    client_max_body_size 128M;
    access_log /var/log/nginx/application.access.log;
    root /var/www/html;
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$args;
    }
    
    location ~ ^.+\.php {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_split_path_info ^(.+?\.php)(/.*)$;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PHP_VALUE "error_log=/var/log/nginx/application_php_errors.log";
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
        fastcgi_index index.php;
        include fastcgi_params;
    }
    
    location ~ /\.ht {
        deny all;
    }
}
EOF

# Create startup script
RUN cat > /start.sh << 'EOF'
#!/bin/bash
cat > /var/www/html/config.php << 'PHPEOF'
<?php
// Define BASE_URL using define() to allow runtime expressions
$baseUrl = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
define('BASE_URL', $baseUrl);
define('LANGUAGE', 'english');
define('DEBUG_MODE', false);
define('DB_HOST', getenv('MYSQLHOST') ?: 'localhost');
define('DB_NAME', getenv('MYSQLDATABASE') ?: 'easyappointments');
define('DB_USERNAME', getenv('MYSQLUSER') ?: 'user');
define('DB_PASSWORD', getenv('MYSQLPASSWORD') ?: 'password');
define('GOOGLE_SYNC_FEATURE', false);
define('GOOGLE_CLIENT_ID', '');
define('GOOGLE_CLIENT_SECRET', '');

class Config
{
    const BASE_URL = BASE_URL;
    const LANGUAGE = LANGUAGE;
    const DEBUG_MODE = DEBUG_MODE;
    const DB_HOST = DB_HOST;
    const DB_NAME = DB_NAME;
    const DB_USERNAME = DB_USERNAME;
    const DB_PASSWORD = DB_PASSWORD;
    const GOOGLE_SYNC_FEATURE = GOOGLE_SYNC_FEATURE;
    const GOOGLE_CLIENT_ID = GOOGLE_CLIENT_ID;
    const GOOGLE_CLIENT_SECRET = GOOGLE_CLIENT_SECRET;
}
PHPEOF
chown www-data:www-data /var/www/html/config.php
php-fpm -D
nginx -g 'daemon off;'
EOF
chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
