FROM php:8.4-cli

# Установка PostgreSQL зависимостей
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    postgresql-client \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && rm -rf /var/lib/apt/lists/*

# Установка Composer и Node.js
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && curl -sL https://deb.nodesource.com/setup_24.x | bash - \
    && apt-get update && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY . .

RUN composer install \
    && npm ci \
    && npm run build

CMD ["bash", "-c", "php artisan config:clear && php artisan cache:clear && php artisan view:clear && php artisan route:clear && php artisan serve --host=0.0.0.0 --port=$PORT"]
# CMD php artisan serve --host=0.0.0.0 --port=$PORT