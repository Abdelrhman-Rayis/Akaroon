FROM php:8.2-apache

# Install system dependencies and PHP extensions required by WordPress + the custom PHP scripts
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libfreetype6-dev \
    libxml2-dev \
    libzip-dev \
    libcurl4-openssl-dev \
    libonig-dev \
    libmagickwand-dev \
    unzip \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install \
        mysqli \
        pdo_mysql \
        gd \
        xml \
        curl \
        mbstring \
        zip \
        opcache \
        exif \
        intl \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# PHP settings for local dev
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Apache vhost with AllowOverride All (needed for WordPress .htaccess)
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
