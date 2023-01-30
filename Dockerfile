FROM php:8.2-cli-alpine AS build

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN (mkdir -p /build/flux-mail-rest-api/libs/php-imap && cd /build/flux-mail-rest-api/libs/php-imap && composer require php-imap/php-imap:5.0.1 --ignore-platform-reqs)

RUN (mkdir -p /build/flux-mail-rest-api/libs/PHPMailer && cd /build/flux-mail-rest-api/libs/PHPMailer && composer require phpmailer/phpmailer:v6.7.1 --ignore-platform-reqs)

RUN (mkdir -p /build/flux-mail-rest-api/libs/flux-mail-api && cd /build/flux-mail-rest-api/libs/flux-mail-api && wget -O - https://github.com/fluxfw/flux-mail-api/archive/refs/tags/v2023-01-30-1.tar.gz | tar -xz --strip-components=1)

RUN (mkdir -p /build/flux-mail-rest-api/libs/flux-rest-api && cd /build/flux-mail-rest-api/libs/flux-rest-api && wget -O - https://github.com/fluxfw/flux-rest-api/archive/refs/tags/v2023-01-30-1.tar.gz | tar -xz --strip-components=1)

COPY . /build/flux-mail-rest-api

FROM php:8.2-cli-alpine

RUN apk add --no-cache imap-dev libstdc++ && \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS openssl-dev && \
    (mkdir -p /usr/src/php/ext/swoole && cd /usr/src/php/ext/swoole && wget -O - https://pecl.php.net/get/swoole | tar -xz --strip-components=1) && \
    docker-php-ext-configure imap --with-imap-ssl && \
    docker-php-ext-configure swoole --enable-openssl && \
    docker-php-ext-install -j$(nproc) imap swoole && \
    docker-php-source delete && \
    apk del .build-deps

USER www-data:www-data

EXPOSE 9501

ENTRYPOINT ["/flux-mail-rest-api/bin/server.php"]

COPY --from=build /build /

ARG COMMIT_SHA
LABEL org.opencontainers.image.revision="$COMMIT_SHA"
