#!/bin/bash

cd /var/www/html

# Run Laravel setup commands at container startup (when env is available)
php artisan config:clear
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link || true

# Start Apache
exec apache2-foreground
