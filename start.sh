#!/bin/bash

# Accept database name as argument
DB_NAME=$1

# Function to run Laravel migrations
run_migrations() {
    echo "Running Laravel migrations..."
    docker compose exec optimze php artisan migrate --force
    docker compose exec optimze php artisan db:seed --class=ProviderSeeder
}

# Run Laravel migrations
docker compose build && docker compose up -d && docker compose exec db mysql -uroot -ptoor -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"

# Check if MySQL connection error occurred
if [ $? -ne 0 ]; then
    echo "Error: Can't connect to local MySQL server."
    echo "Retrying migration..."
    run_migrations
else
    run_migrations
fi
