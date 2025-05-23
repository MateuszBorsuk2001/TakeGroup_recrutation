FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN useradd -G www-data,root -u 1000 -d /home/laravel laravel \
    && mkdir -p /home/laravel/.composer \
    && chown -R laravel:laravel /home/laravel

WORKDIR /var/www

# Create directory and set permissions before copying files
RUN mkdir -p /var/www/vendor && \
    chown -R laravel:laravel /var/www

COPY --chown=laravel:laravel composer.json composer.lock ./

USER laravel
RUN composer install --no-scripts --no-autoloader

COPY --chown=laravel:laravel . .

RUN composer dump-autoload

EXPOSE 9000
CMD ["php-fpm"]