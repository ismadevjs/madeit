#!/bin/bash

# Copy .env.example to .env
cp src/.env.example src/.env

# Edit .env file
sed -i '' 's/^DB_CONNECTION=sqlite/DB_CONNECTION=mysql/' src/.env
sed -i '' 's/^# DB_HOST=127\.0\.0\.1/DB_HOST=127.0.0.1/' src/.env
sed -i '' 's/^# DB_PORT=3306/DB_PORT=3306/' src/.env
sed -i '' 's/^# DB_DATABASE=laravel/DB_DATABASE=/' src/.env
sed -i '' 's/^# DB_USERNAME=root/DB_USERNAME=/' src/.env
sed -i '' 's/^# DB_PASSWORD=/DB_PASSWORD=/' src/.env

# Prompt for database details
read -p "Enter the database host (default: 127.0.0.1): " DB_HOST
read -p "Enter the database port (default: 3306): " DB_PORT
read -p "Enter the database name: " DB_DATABASE
read -p "Enter the database username: " DB_USERNAME
read -p "Enter the database password: " DB_PASSWORD

# Update .env file with provided details
sed -i '' "s/^DB_HOST=.*/DB_HOST=$DB_HOST/" src/.env
sed -i '' "s/^DB_PORT=.*/DB_PORT=$DB_PORT/" src/.env
sed -i '' "s/^DB_DATABASE=.*/DB_DATABASE=$DB_DATABASE/" src/.env
sed -i '' "s/^DB_USERNAME=.*/DB_USERNAME=$DB_USERNAME/" src/.env
sed -i '' "s/^DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" src/.env

echo "Database details updated successfully."

# Update database name in docker-compose.yml
sed -i '' "s/MYSQL_DATABASE: optizi/MYSQL_DATABASE: $DB_DATABASE/" docker-compose.yml

# Run 'composer install' inside src/
echo "Running 'composer install'..."
cd src/ || exit
composer install
cd ..

# Check if APP_KEY is empty, if so, generate the key
if grep -q "APP_KEY=" src/.env && grep -q "APP_KEY=" src/.env | grep -q "^[^#;]"; then
  echo "Laravel key already set."
else
  echo "Generating Laravel key..."
  cd src/ || exit
  php artisan key:generate
  cd ..
fi

# Check if previous steps were successful, then run start.sh
if [ $? -eq 0 ]; then
  echo "Setup completed successfully. Starting application..."
  ./start.sh
else
  echo "Setup failed. Please check the error messages above."
fi
