# Use the official PHP image with FPM (FastCGI Process Manager)
FROM php:8.1-fpm

# Copy custom php.ini configuration
COPY php.ini /usr/local/etc/php/

# Set the working directory
WORKDIR /var/www/public

# Install system dependencies required for Symfony and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer globally (using the recommended installation method)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the Symfony project into the container
COPY . .

# Install project dependencies (Composer)
RUN composer install

# Expose port 9000 to the host
EXPOSE 9000

# Start PHP-FPM server
CMD ["php-fpm"]
