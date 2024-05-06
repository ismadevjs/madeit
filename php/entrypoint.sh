#!/bin/bash

# Run Laravel migrations

php artisan migrate --force

php artisan db:seed --class=ProviderSeeder

# Start the PHP-FPM server
php-fpm
