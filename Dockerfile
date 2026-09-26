# syntax=docker/dockerfile:1

###########################
# Stage: vendor           #
###########################
# Install PHP dependencies without running post-install scripts, so this
# stage only needs the composer manifests for a fast, cacheable step.
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

###########################
# Stage: assets           #
###########################
# Install JS dependencies and build the React/Vite assets. The node:22
# image is a Debian slim image so native modules (e.g. Tailwind oxide)
# get glibc prebuilds.
FROM node:22-bookworm-slim AS assets

WORKDIR /app

COPY package.json package-lock.json .npmrc ./

RUN npm ci --ignore-scripts

COPY vite.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm run build

###########################
# Stage: runtime          #
###########################
FROM php:8.3-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        unzip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

COPY . .

# Runtime dependencies and pre-built front-end assets from the build stages.
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

# Persistent SQLite data lives in the data directory (mounted as a volume).
RUN mkdir -p /var/www/html/data

EXPOSE 8000

ENTRYPOINT ["docker-entrypoint"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000", "--no-reload"]