# 1. Start with a base image (Use PHP 8.2 Apache for Laravel)
FROM php:8.2-apache

# 2. Set the working directory
WORKDIR /var/www/html

# 3. System updates and installs
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libpng-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip mbstring xml \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 4. Enable Apache rewrite
RUN a2enmod rewrite

# 5. Make Apache use port 10000
RUN sed -i 's/Listen 80/Listen 10000/g' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/g' /etc/apache2/sites-available/000-default.conf

# 6. Set Laravel public as document root
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

# 7. Allow .htaccess for Laravel
RUN printf '<Directory /var/www/html/public>\n\
AllowOverride All\n\
Require all granted\n\
</Directory>\n' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# 8. Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 9. Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 10. Copy Laravel app
COPY . .

# 11. Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 12. Install frontend dependencies and build assets
RUN npm install && npm run build

# 13. Clear caches
RUN php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear

# 14. Fix permissions
RUN mkdir -p storage/framework/cache storage/framework/sessions \
    storage/framework/views bootstrap/cache public/uploads \
    && chown -R www-data:www-data storage bootstrap/cache public/uploads \
    && chmod -R 775 storage bootstrap/cache public/uploads

# 15. Create storage symlink
RUN php artisan storage:link || true

# 16. Expose port and start Apache
EXPOSE 10000
CMD ["apache2-foreground"]