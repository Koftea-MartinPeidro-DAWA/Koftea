FROM composer:2 AS composer_stage
FROM php:8.3-fpm

# Paquets necessaris per compilar extensions i dependències de intl/zip/mbstring
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git \
        curl \
        unzip \
        pkg-config \
        autoconf \
        build-essential \
        libonig-dev \
        libzip-dev \
        zlib1g-dev \
        libicu-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    ; \
    rm -rf /var/lib/apt/lists/*

# Configurar y compilar extensiones
RUN set -eux; \
    docker-php-ext-configure zip; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j"$(nproc)" intl zip mbstring opcache gd

# Instal·lar Xdebug (instal·lat però no activat si no poses un .ini)
RUN pecl install xdebug

# Composer
COPY --from=composer_stage /usr/bin/composer /usr/bin/composer

# Config PHP bàsica
RUN { \
      echo "display_errors=1"; \
      echo "display_startup_errors=1"; \
      echo "error_reporting=E_ALL"; \
      echo "memory_limit=512M"; \
      echo "post_max_size=10M"; \
      echo "upload_max_filesize=10M"; \
    } > /usr/local/etc/php/conf.d/zzz-custom.ini

# FPM ping/status per healthcheck
RUN { \
      echo "ping.path = /fpm-ping"; \
      echo "pm.status_path = /fpm-status"; \
    } > /usr/local/etc/php-fpm.d/zz-ping.conf

# Mini servidor per respondre /fpm-ping via HTTP (port 9001)
RUN echo '<?php header("Content-Type:text/plain"); echo "pong";' > /fpm-ping.php
CMD php -S 0.0.0.0:9001 /fpm-ping.php & php-fpm

WORKDIR /var/www/php
