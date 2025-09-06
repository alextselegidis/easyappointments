#!/usr/bin/env bash
set -e

APP_DIR="/var/www/html"

# Generate config.php once, based on config-sample.php and env
if [ ! -f "${APP_DIR}/config.php" ]; then
  echo ">> No config.php found, creating from config-sample.php"
  cp "${APP_DIR}/config-sample.php" "${APP_DIR}/config.php"

  # Replace constants as per documentation (BASE_URL, DB_*).
  # Docs example shows these constants under class Config. 
  # https://easyappointments.org/documentation/docker/
  sed -i "s|const BASE_URL[[:space:]]*= .*;|const BASE_URL = '${EASYA_BASE_URL:-http://localhost}';|g" "${APP_DIR}/config.php"
  sed -i "s|const DB_HOST[[:space:]]*= .*;|const DB_HOST = '${EASYA_DB_HOST:-db}';|g" "${APP_DIR}/config.php"
  sed -i "s|const DB_NAME[[:space:]]*= .*;|const DB_NAME = '${EASYA_DB_NAME:-easyappointments}';|g" "${APP_DIR}/config.php"
  sed -i "s|const DB_USERNAME[[:space:]]*= .*;|const DB_USERNAME = '${EASYA_DB_USER:-ea_user}';|g" "${APP_DIR}/config.php"
  sed -i "s|const DB_PASSWORD[[:space:]]*= .*;|const DB_PASSWORD = '${EASYA_DB_PASS:-ea_pass}';|g" "${APP_DIR}/config.php"
fi

# Ensure storage is writable (required by the app)
chown -R www-data:www-data "${APP_DIR}/storage" || true

exec "$@"
