FROM dunglas/frankenphp:latest

# Install install-php-extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Extensions PHP
RUN install-php-extensions \
    pdo_mysql \
    zip \
    intl \
    opcache \
    pcntl \
    mbstring \
    bcmath

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy project
COPY . .

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader

# Laravel permissions
RUN chown -R www-data:www-data /app \
    && chmod -R 775 storage bootstrap/cache

# Generate APP_KEY if necessary
RUN php artisan key:generate || true

EXPOSE 8000

# Launch Octane correctly
ENTRYPOINT ["php", "artisan", "octane:start", "--server=frankenphp", "--host=0.0.0.0", "--port=8000"]