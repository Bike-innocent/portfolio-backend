FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Set environment to debug mode to see 500 errors
ENV APP_ENV=local
ENV APP_DEBUG=true

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Set Laravel public folder as DocumentRoot
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy .env file and generate key
RUN cp .env.example .env && php artisan key:generate

# Expose port
EXPOSE 80

# Run config cache and migrate on container start
CMD php artisan config:clear && php artisan config:cache && php artisan migrate --force && apache2-foreground
