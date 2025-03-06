FROM php:8.2-fpm

# Instala extensiones y dependencias necesarias usando apt-get
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    libicu-dev \
    libzip-dev \
    libonig-dev \
    zlib1g-dev \
    libpq-dev \
    curl \
    git \
    unzip \
    pkg-config \
    libgd-dev \
    libxpm-dev \
    libicu-dev \
    libssl-dev \
    libmcrypt-dev \
    libcurl4-openssl-dev \
    default-mysql-client

RUN docker-php-ext-install pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock ./

RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader

COPY . .

RUN composer dump-autoload

EXPOSE 9000

CMD ["php-fpm"]