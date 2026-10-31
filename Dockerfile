FROM php:8.2-cli-alpine

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    sqlite-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring zip bcmath gd opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy web codebase
COPY school-erp/web/ .

# Install PHP dependencies & compile assets
RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && npm install --legacy-peer-deps \
    && npm run build \
    && cp .env.example .env || true \
    && php artisan key:generate --force || true

# Environment variables
ENV PORT=8000
EXPOSE 8000

# Start command
CMD php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
