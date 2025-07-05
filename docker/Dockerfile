FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip vim \
    libpng-dev libonig-dev libxml2-dev libzip-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install pdo pdo_mysql zip gd

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Copy Laravel app from parent folder
COPY ../laravel /var/www/html

# Set permissions
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

# Copy custom entrypoint script
COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose Apache port
EXPOSE 80

# Use custom entrypoint to run artisan commands after env is available
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
