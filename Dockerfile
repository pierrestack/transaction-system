FROM node:22 AS assets

WORKDIR /app

COPY package*.json ./

RUN npm install

COPY . .

RUN npm run build

FROM dunglas/frankenphp:php8.5

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

COPY --from=assets /app/public/build ./public/build

# Copy project
COPY . .

# Install dependencies
RUN composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# Set permissions for frankenphp binary
RUN setcap -r /usr/local/bin/frankenphp
RUN chmod +x /usr/local/bin/frankenphp

# Laravel permissions
RUN chown -R www-data:www-data /app \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

# Launch Octane correctly
CMD ["sh", "-c", "php artisan octane:start --server=frankenphp --host=0.0.0.0 --port=${PORT:-8000}"]
