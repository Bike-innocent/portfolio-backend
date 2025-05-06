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

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port
EXPOSE 80

# Start Apache and Laravel commands
CMD php artisan config:clear \
    && if [ ! -f .env ]; then cp .env.example .env; fi \
    && if ! grep -q "APP_KEY=" .env || grep -q "APP_KEY=" .env | grep -q 'APP_KEY=$'; then php artisan key:generate; fi \
    && php artisan config:cache \
    && php artisan migrate --force || echo "Migration skipped (maybe DB not ready)" \
    && apache2-foreground
