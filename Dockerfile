# ---- base php image ----
FROM php:8.4-cli-alpine

# set working directory
WORKDIR /var/www/html

# install required system packages + php extensions
RUN apk add --no-cache \
      git \
      zip \
      unzip \
      libzip-dev \
      oniguruma-dev \
      autoconf \
      g++ \
      make \
      netcat-openbsd \
    && docker-php-ext-install mbstring zip \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

# install composer (copy from official composer image)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# copy app code
COPY . .

# make sure storage & cache dirs are writable
RUN mkdir -p storage bootstrap/cache \
  && chown -R www-data:www-data storage bootstrap/cache \
  && chmod -R 775 storage bootstrap/cache \
  && apk add --no-cache netcat-openbsd

# expose laravel’s port
EXPOSE 8000

# (entrypoint lives in apde/entrypoints/backend.sh, not here)
