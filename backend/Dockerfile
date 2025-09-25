FROM php:8.2-fpm

# system deps
RUN apt-get update && apt-get install -y \
    git curl unzip libpq-dev libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libonig-dev libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql zip gd bcmath opcache \
    && rm -rf /var/lib/apt/lists/*

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

CMD ["php-fpm"]
