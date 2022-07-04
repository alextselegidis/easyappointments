replace_in_config() {
    key="$1"
    value="$2"
    sed -i -r "s|(${key}\s+)=.+\$|\1= '${value}';|" /app/build/config.php
}

# Parse MySQL_URL
protocol="$(echo "$MYSQL_URL" | grep :// | sed -e's,^\(.*://\).*,\1,g')"
url="$(echo ${MYSQL_URL/$protocol/})"

# extract the credentials
userpass="$(echo $url | grep @ | cut -d@ -f1)"
password="$(echo $userpass | grep : | cut -d: -f2)"

if [ -n "$password"  ]; then
  # We received a password, so split to retrieve the username
  username="$(echo $userpass | grep : | cut -d: -f1)"
else
  # Username has been supplied, no password
  username=$userpass
fi

# extract the host
host="$(echo ${url/$user@/} | cut -d/ -f1 | rev | cut -d':' -f2- | rev)"

# Remove credentials from the host if present
basename="$(echo ${host#"$userpass"})"
# extract the path
path="$(echo $url | grep / | cut -d/ -f2-)"

if [ ! -f "/app/build/config.php" ]; then
    cp /app/build/config-sample.php /app/build/config.php
    echo "Created /app/build/config.php"
fi

replace_in_config "BASE_URL" "$BASE_URL"
replace_in_config "DB_HOST" "$basename"
replace_in_config "DB_NAME" "$path"
replace_in_config "DB_USERNAME" "$username"
replace_in_config "DB_PASSWORD" "$password"
