FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libgd-dev \
    libpng-dev \
    libjpeg-dev \
    libpq-dev \
    unzip \
    git \
    && docker-php-ext-install zip gd pdo pdo_pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-interaction --optimize-autoloader

COPY . .

EXPOSE 8080

CMD php artisan config:cache && php artisan route:cache && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT