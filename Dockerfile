FROM php:8.2-cli-alpine AS build

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY bin/install-libraries.sh /build/flux-mail-rest-api/libs/flux-mail-rest-api/bin/install-libraries.sh
RUN /build/flux-mail-rest-api/libs/flux-mail-rest-api/bin/install-libraries.sh

RUN ln -s libs/flux-mail-rest-api/bin /build/flux-mail-rest-api/bin

COPY . /build/flux-mail-rest-api/libs/flux-mail-rest-api

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
