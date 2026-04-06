# PHP-FPM 8.3 — extensiones para Laravel + PostgreSQL (cliente predis en PHP puro).
FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    git \
    icu-dev \
    libpq \
    libzip-dev \
    oniguruma-dev \
    postgresql-dev \
    unzip \
    zip

RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl opcache pdo pdo_pgsql pcntl bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
