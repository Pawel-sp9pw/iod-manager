FROM node:22-alpine AS frontend
WORKDIR /app
COPY package.json ./
RUN npm install
COPY resources ./resources
COPY vite.config.js ./
RUN npm run build

FROM php:8.3-cli-alpine
RUN apk add --no-cache icu-dev oniguruma-dev libzip-dev linux-headers \
    && docker-php-ext-install pdo_mysql mbstring intl bcmath opcache \
    && rm -rf /tmp/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache
COPY --from=frontend /app/public/build ./public/build
USER www-data
EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
