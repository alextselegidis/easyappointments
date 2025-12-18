#!/bin/bash
set -e

# Disable other MPMs to avoid "More than one MPM loaded" error
a2dismod mpm_event 2>/dev/null || true
a2dismod mpm_worker 2>/dev/null || true
a2dismod mpm_prefork 2>/dev/null || true

# Enable prefork
a2enmod mpm_prefork

# Fix AH00558: Could not reliably determine the server's fully qualified domain name
echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Configure PHP to log to stderr
# This ensures errors are visible in Railway logs even if display_errors is Off
{ \
    echo 'log_errors = On'; \
    echo 'error_log = /dev/stderr'; \
    echo 'display_errors = Off'; \
} > /usr/local/etc/php/conf.d/logging.ini

# Execute the original command
exec "$@"
