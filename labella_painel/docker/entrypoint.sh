#!/bin/sh
set -e

# Ensure Laravel storage structure exists
mkdir -p /var/www/storage/framework/{sessions,views,cache/data}
mkdir -p /var/www/storage/logs
mkdir -p /var/www/bootstrap/cache

# Fix permissions for PHP-FPM (www-data)
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

exec docker-php-entrypoint php-fpm
