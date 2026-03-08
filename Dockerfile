FROM php:8.2-apache

# Install system dependencies and PHP extensions
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

# PHP settings
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Apache vhost with AllowOverride All (needed for WordPress .htaccess)
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Startup script — configures Apache PORT for Cloud Run
COPY docker/start.sh /start.sh
# DB fix script — run once at startup to update stale menu URLs
COPY docker/fix-menu.php /docker/fix-menu.php
RUN chmod +x /start.sh

WORKDIR /var/www/html

# Bake app files into image (Cloud Run uses these; docker-compose mounts override locally)
COPY public_html/ .

# Build Qabas root lookup at image-build time.
# The generated file (a CC-BY-ND derivative) is intentionally NOT committed to git;
# it lives only inside the container image.
COPY Qabas/Qabas-dataset.csv /tmp/Qabas-dataset.csv
COPY tools/build_qabas_lookup.php /tmp/build_qabas_lookup.php
RUN php /tmp/build_qabas_lookup.php \
      /tmp/Qabas-dataset.csv \
      /var/www/html/lib/qabas_lookup.php \
    && rm /tmp/Qabas-dataset.csv /tmp/build_qabas_lookup.php

# Override wp-config with Cloud Run / env-var-driven versions
COPY docker/wp-config-cloud.php      blog/wp-config.php
COPY docker/wp-config-library-cloud.php library/wp-config.php

# Give Apache (www-data) write access so WordPress can create uploads/cache dirs
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && chmod +x /start.sh

# Cloud Run expects the container to listen on $PORT (default 8080)
EXPOSE 8080

CMD ["/start.sh"]
