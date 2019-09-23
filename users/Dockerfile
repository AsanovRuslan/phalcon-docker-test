FROM composer:1.8.4 AS composer
FROM php:7.2-fpm-alpine

ENV PHALCON_VERSION=3.4.2
RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        libxml2-dev \
        zlib-dev \
# cphalcon
    && curl -LO https://github.com/phalcon/cphalcon/archive/v${PHALCON_VERSION}.tar.gz \
    && tar xzf v${PHALCON_VERSION}.tar.gz \
    && docker-php-ext-install ${PWD}/cphalcon-${PHALCON_VERSION}/build/php7/64bits \
    && rm -rf v${PHALCON_VERSION}.tar.gz cphalcon-${PHALCON_VERSION} \
    && apk del .build-deps


RUN apk add --no-cache \
    git \
    unzip \
    sqlite

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /app/public

CMD ["php", "-S", "0.0.0.0:8080", "/app/.htrouter.php"]