# Local development image: PHP 8.4 CLI + Composer + Node 22.
# Code is bind-mounted via docker-compose, so this image only carries the
# language runtimes and PHP extensions the app needs.
FROM php:8.4-cli-bookworm

# System libraries required by the PHP extensions below.
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        procps \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype-dev \
        libonig-dev \
        fonts-dejavu-core \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions:
#   gd  -> Intervention Image (image processing pipeline)
#   pdo_mysql -> MySQL/MariaDB connection
#   zip, exif, bcmath, pcntl -> Laravel / Composer runtime needs
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        gd \
        pdo_mysql \
        zip \
        exif \
        bcmath \
        pcntl

# Composer (pinned from the official Composer image).
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Node 22 + npm, lifted from the matching Debian-bookworm Node image so the
# glibc-linked binaries are compatible with this base.
COPY --from=node:22-bookworm-slim /usr/local/bin /usr/local/bin
COPY --from=node:22-bookworm-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -sf /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -sf /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

WORKDIR /var/www/html

# Raise PHP upload limits for bulk photo uploads.
COPY docker/php-uploads.ini /usr/local/etc/php/conf.d/uploads.ini

COPY docker/dev-entrypoint.sh /usr/local/bin/dev-entrypoint
RUN chmod +x /usr/local/bin/dev-entrypoint

# artisan serve (8000) and the Vite dev server (5173).
EXPOSE 8000 5173

ENTRYPOINT ["dev-entrypoint"]
