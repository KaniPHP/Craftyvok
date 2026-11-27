#!/bin/sh
set -eu

cd /var/www/html

if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY must be set in the Render environment." >&2
    exit 1
fi

export APP_ENV=production
export APP_DEBUG=false
export APP_URL="${APP_URL:-https://craftyvok.onrender.com}"
export ASSET_URL="${ASSET_URL:-https://craftyvok.onrender.com}"
export DB_CONNECTION="${DB_CONNECTION:-sqlite}"
export DB_DATABASE="${DB_DATABASE:-/var/www/html/storage/app/database.sqlite}"
export FILESYSTEM_DISK=public
export SESSION_DRIVER=file
export CACHE_STORE=file
export QUEUE_CONNECTION=sync
export LOG_CHANNEL="${LOG_CHANNEL:-stderr}"
export LOG_LEVEL="${LOG_LEVEL:-warning}"

initialized=false
if [ "$DB_CONNECTION" = "sqlite" ]; then
    mkdir -p "$(dirname "$DB_DATABASE")"
    if [ ! -f "$DB_DATABASE" ]; then
        touch "$DB_DATABASE"
        initialized=true
    fi
    chown www-data:www-data "$(dirname "$DB_DATABASE")"
    chown www-data:www-data "$DB_DATABASE"
    chmod 664 "$DB_DATABASE"
fi

mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

php artisan migrate --force

if [ "$initialized" = true ]; then
    php artisan db:seed --class=DemoGiftSeeder --force
    php artisan db:seed --class=InstagramPhotoSeeder --force
    php artisan db:seed --class=InstagramVideoSeeder --force
fi

if [ -n "${ADMIN_EMAIL:-}" ] && [ -n "${ADMIN_PASSWORD:-}" ]; then
    php artisan db:seed --class=AdminUserSeeder --force
fi

php artisan storage:link --force

port="${PORT:-10000}"
sed -i "s/Listen 80/Listen ${port}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${port}>/" /etc/apache2/sites-available/000-default.conf

exec apache2-foreground
