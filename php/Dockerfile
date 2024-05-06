# Base image
FROM php:8.2.11-fpm

# Create a non-root user
RUN useradd -u 1000 appuser

# Install composer
RUN echo "\e[1;33mInstall COMPOSER\e[0m" \
    && cd /tmp \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Install necessary PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Update package repositories and install necessary packages
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        apt-utils \
        nano \
        wget \
        dialog \
        vim \
        build-essential \
        git \
        curl \
        libcurl4 \
        libcurl4-openssl-dev \
        zlib1g-dev \
        libzip-dev \
        zip \
        libbz2-dev \
        locales \
        libmcrypt-dev \
        libicu-dev \
        libonig-dev \
        libxml2-dev \
        ffmpeg \
        libpq-dev

# Configure and install Postgre PDO
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Copy entrypoint script into the container
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

# Give execute permission to the entrypoint script
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set working directory
WORKDIR /var/www

# Change ownership of directories
RUN chown -R appuser:appuser /var/www

# Switch to the non-root user
USER appuser

# Set entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
