FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip vim \
    libpng-dev libonig-dev libxml2-dev libzip-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install pdo pdo_mysql zip gd

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy Laravel app from outside docker folder
COPY ../laravel /var/www/html

# Set working directory
WORKDIR /var/www/html

# Set permissions (very important)
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 775 storage bootstrap/cache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set Apache to use Laravel public folder
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Add Laravel directory override config
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Laravel setup (only works if env vars are available from dashboard)
RUN php artisan config:clear && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan key:generate && \
    php artisan migrate --force && \
    php artisan storage:link || true

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
