#!/bin/bash

# Prompt user to choose between docker-compose and docker compose
echo "Do you have docker-compose or docker compose installed? Choose one:"
select option in "docker-compose" "docker compose"; do
    case $option in
        "docker-compose")
            DOCKER_COMMAND="docker-compose"
            break
            ;;
        "docker compose")
            DOCKER_COMMAND="docker compose"
            break
            ;;
        *)
            echo "Invalid option. Please choose again."
            ;;
    esac
done

# Accept database name as argument
DB_NAME=$1

# Run Laravel migrations
$DOCKER_COMMAND build
sleep 1

$DOCKER_COMMAND up -d
sleep 1

$DOCKER_COMMAND exec db mysql -uroot -ptoor -e "SHOW DATABASES;" --protocol=tcp
sleep 2

$DOCKER_COMMAND exec db mysql -uroot -ptoor -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;" --protocol=tcp
sleep 4

$DOCKER_COMMAND exec app php artisan migrate
sleep 5

$DOCKER_COMMAND exec app php artisan db:seed --class=ProviderSeeder
sleep 3

# Check if MySQL connection error occurred
if [ $? -ne 0 ]; then
    echo "Error: Can't connect to local MySQL server."
    echo "Retrying migration..."
else
    echo "done."
fi
