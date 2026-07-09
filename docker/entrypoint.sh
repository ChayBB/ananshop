#!/bin/bash
set -e

export PORT="${PORT:-10000}"

envsubst '${PORT}' < /etc/nginx/templates/nginx.conf.template > /etc/nginx/nginx.conf

cd /app

if [ ! -f storage/oauth-private.key ] && [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY is not set. Set it in your Render environment variables."
fi

php artisan storage:link || true

php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ "$RUN_MIGRATIONS" = "true" ]; then
    php artisan migrate --force
fi

if [ "$RUN_SEEDERS" = "true" ]; then
    php artisan db:seed --force
fi

exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
