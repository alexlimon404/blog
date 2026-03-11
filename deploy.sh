#!/bin/sh

sudo -u www-data git pull

sudo -u www-data composer install --no-dev --no-interaction --optimize-autoloader --prefer-dist

php artisan migrate --force

php artisan app:install

php artisan config:cache

php artisan event:cache

php artisan route:cache

php artisan view:clear

php artisan filament:optimize

php artisan generate:sitemap

#php artisan horizon:terminate
