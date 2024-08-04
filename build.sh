#!/bin/bash

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies and build assets
npm install
npm run production

# Create storage directories and set permissions
mkdir -p bootstrap/cache
chmod -R 777 bootstrap/cache
chmod -R 777 storage

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
