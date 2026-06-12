FROM php:8.4-apache

ARG DEBIAN_FRONTEND=noninteractive

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git curl bash unzip ca-certificates \
        libicu-dev \
        libzip-dev zlib1g-dev \
        libpq-dev libpq5 \
        libonig-dev \
        librabbitmq-dev librabbitmq4 \
        libssl-dev libcurl4-openssl-dev \
        $PHPIZE_DEPS; \
    docker-php-ext-configure intl; \
    docker-php-ext-install -j"$(nproc)" intl zip bcmath pcntl opcache pdo_pgsql mbstring curl; \
    pecl install amqp redis apcu xdebug; \
    docker-php-ext-enable amqp redis apcu xdebug; \
    apt-mark manual librabbitmq4 libpq5; \
    apt-get purge -y --auto-remove \
        libssl-dev libcurl4-openssl-dev \
        $PHPIZE_DEPS; \
    rm -rf /var/lib/apt/lists/* /tmp/pear

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libicu76 \
        libzip5 \
        libpq5 \
        libonig5 \
        libcurl4 \
        zlib1g; \
    ldconfig; \
    rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite

RUN sed -ri 's!/var/www/html!/var/www/sample/public!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf

WORKDIR /var/www/sample

RUN mkdir -p var/cache var/log && \
    chown -R www-data:www-data var

RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'opcache.memory_consumption=192'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.validate_timestamps=1'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.jit=1255'; \
    echo 'opcache.jit_buffer_size=64M'; \
  } > /usr/local/etc/php/conf.d/opcache.ini

EXPOSE 80

CMD ["apache2-foreground"]
