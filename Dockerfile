FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libpq-dev libzip-dev \
 && docker-php-ext-install intl pdo pdo_pgsql opcache \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# DEV: nie wymuszaj prod w obrazie dev (to Ci utrudni debug/cache)
ENV APP_ENV=dev
ENV APP_DEBUG=1
ENV COMPOSER_ALLOW_SUPERUSER=1

# Opcjonalnie: instalacja vendor w obrazie (OK, ale i tak zabezpieczymy vendor volume)
COPY app/ ./
RUN composer install --no-interaction

RUN mkdir -p var/cache var/log && chown -R www-data:www-data var

CMD ["sh","-lc","echo PORT=$PORT && php -S 0.0.0.0:$PORT -t public"]
