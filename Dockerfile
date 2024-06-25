FROM php:8.2-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    libicu-dev \
    iputils-ping \
    telnet \
    netcat-openbsd \
    && pecl install raphf && docker-php-ext-enable raphf \
    && pecl install pecl_http && docker-php-ext-enable http \
    && docker-php-ext-install pdo pdo_mysql zip sockets

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

COPY .env .env

COPY start.sh /usr/local/bin/start.sh

RUN chmod +x /usr/local/bin/start.sh

RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8000

CMD ["start.sh"]
