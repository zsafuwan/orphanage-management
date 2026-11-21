#!/bin/sh
set -eu
if [ -z "$APP_KEY" ]; then
    echo "APP_KEY is missing. Set it in the project's existing .env before starting Docker." >&2
    exit 1
fi
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
php artisan config:clear
php artisan package:discover --ansi
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/hopehaven.conf
