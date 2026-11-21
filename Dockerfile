FROM php:8.4-cli-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip nginx supervisor libpq-dev libzip-dev libicu-dev libonig-dev \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j2 pdo_pgsql mbstring intl zip gd bcmath pcntl dom xml xmlwriter \
    && rm -rf /var/lib/apt/lists/* /etc/nginx/sites-enabled/default

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-progress --no-scripts
COPY . .
RUN composer dump-autoload --optimize --no-scripts \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
COPY docker/nginx.conf /etc/nginx/conf.d/hopehaven.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/hopehaven.conf
COPY docker/entrypoint.sh /usr/local/bin/hopehaven-entrypoint
RUN sed -i 's/\r$//' /usr/local/bin/hopehaven-entrypoint && chmod +x /usr/local/bin/hopehaven-entrypoint
EXPOSE 80
ENTRYPOINT ["hopehaven-entrypoint"]
