#!/bin/bash

# Build and run Docker containers
docker-compose up -d

# Install Laravel dependencies
docker-compose exec app composer install --no-dev --optimize-autoloader

# Run database migrations
docker-compose exec app php artisan migrate --force

# Seed the database (if needed)
docker-compose exec app php artisan db:seed --class=ProviderSeeder

# Generate application key
docker-compose exec app php artisan key:generate --ansi

echo "Application is up and running!"
