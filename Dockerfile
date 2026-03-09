FROM php:8.2-apache

# Install required PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable required Apache modules
RUN a2enmod rewrite

# Disable all MPM modules except prefork
RUN a2dismod mpm_worker mpm_event 2>/dev/null || true && \
    a2enmod mpm_prefork

# Copy application
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

EXPOSE 80
