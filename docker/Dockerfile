FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    git curl zip unzip vim \
    libpng-dev libonig-dev libxml2-dev

RUN docker-php-ext-install pdo pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy Laravel app from ../laravel to /var/www/html
COPY ../laravel /var/www/html

WORKDIR /var/www/html

# Enable Apache rewrite module
RUN a2enmod rewrite

# Change DocumentRoot to Laravel public folder
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Give public folder override permissions
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

EXPOSE 80
