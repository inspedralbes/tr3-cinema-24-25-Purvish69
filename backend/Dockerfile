FROM php:8.2-fpm

# Instala dependencias necesarias, incluyendo netcat para esperar a MySQL
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

RUN apt-get clean && rm -rf /var/lib/apt/lists/*


RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

WORKDIR /var/www


COPY . .

# (Opcional) Instala Composer si aún no está disponible en el sistema
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

CMD ["php-fpm"]
