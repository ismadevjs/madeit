#!/bin/bash

# Run Laravel migrations
composer install

php artisan key;generate

php artisan migrate --force

php artisan db:seed --class=ProviderSeeder

# Start the PHP-FPM server
php-fpm
