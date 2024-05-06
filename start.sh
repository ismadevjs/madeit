#!/bin/bash

# Accept database name as argument
DB_NAME=$1

# Function to run Laravel migrations
# run_migrations() {
#     echo "Running Laravel migrations..."
#     docker compose exec app php artisan migrate 
#     docker compose exec app php artisan db:seed --class=ProviderSeeder
# }

# Run Laravel migrations
docker compose build
sleep 1

docker compose up -d
sleep 1

docker compose exec db mysql -uroot -ptoor -e "SHOW DATABASES;"
sleep 2

docker compose exec db mysql -uroot -ptoor -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"
sleep 3

docker compose exec app php artisan migrate
sleep 5

docker compose exec app php artisan db:seed --class=ProviderSeeder
sleep 3


# Check if MySQL connection error occurred
if [ $? -ne 0 ]; then
    echo "Error: Can't connect to local MySQL server."
    echo "Retrying migration..."
    # run_migrations
else
    echo "done."
    # run_migrations
fi
