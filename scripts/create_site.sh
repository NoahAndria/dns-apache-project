#!/usr/bin/env bash

SITE_NAME="$1"
SRC_PATH="$2"

WEB_ROOT="/var/www/html"
DEST_PATH="${WEB_ROOT}/$(basename "$SRC_PATH")"
APACHE_CONF="/etc/apache2/sites-available/${SITE_NAME}.conf"
HOSTS_FILE="/etc/hosts"

# Copy files
cp -r "$SRC_PATH" "$WEB_ROOT"

# FULL permissions (recursive)
chown -R www-data:www-data "$DEST_PATH"
chmod -R 777 "$DEST_PATH"

# Apache vhost
cat <<EOF >> "$APACHE_CONF"
<VirtualHost *:80>
    ServerName ${SITE_NAME}
    ServerAlias www.${SITE_NAME}

    DocumentRoot ${DEST_PATH}

    <Directory ${DEST_PATH}>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOF

# Hosts
echo "127.0.0.1 ${SITE_NAME}" >> "$HOSTS_FILE"

# Enable & reload
a2ensite "${SITE_NAME}.conf"
systemctl reload apache2
