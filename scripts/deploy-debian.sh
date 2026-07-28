#!/usr/bin/env bash
#
# Pull latest code from GitHub and update a Bagisto deployment on a Debian
# server. Run this from the deployed app's root directory, or set APP_PATH.
#
# Usage:
#   ./scripts/deploy-debian.sh
#   APP_PATH=/var/www/ananshop PHP_FPM_SERVICE=php8.3-fpm ./scripts/deploy-debian.sh

set -euo pipefail

APP_PATH="${APP_PATH:-$(pwd)}"
PHP_FPM_SERVICE="${PHP_FPM_SERVICE:-php8.3-fpm}"
GIT_BRANCH="${GIT_BRANCH:-main}"
BUILD_ASSETS="${BUILD_ASSETS:-false}"

cd "$APP_PATH"

echo "==> Entering maintenance mode"
php artisan down --retry=5 || true

echo "==> Pulling latest code ($GIT_BRANCH) in $APP_PATH"
git fetch origin "$GIT_BRANCH"
git checkout "$GIT_BRANCH"
git pull origin "$GIT_BRANCH"

echo "==> Installing PHP dependencies"
composer install --no-dev --optimize-autoloader --no-interaction

if [ "$BUILD_ASSETS" = "true" ]; then
    echo "==> Building Admin theme assets"
    (cd packages/Webkul/Admin && npm install && npm run build)

    echo "==> Building Shop theme assets"
    (cd packages/Webkul/Shop && npm install && npm run build)
else
    echo "==> Skipping asset build (BUILD_ASSETS=true to enable)"
fi

echo "==> Running database migrations"
php artisan migrate --force

echo "==> Clearing and rebuilding caches"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

if php artisan list | grep -q responsecache; then
    echo "==> Clearing page response cache"
    php artisan responsecache:clear
fi

if php artisan queue:list &>/dev/null; then
    echo "==> Restarting queue workers"
    php artisan queue:restart
fi

echo "==> Restarting PHP-FPM"
sudo systemctl restart "$PHP_FPM_SERVICE"

echo "==> Reloading Nginx"
sudo systemctl reload nginx

echo "==> Leaving maintenance mode"
php artisan up

echo "==> Deploy complete"
