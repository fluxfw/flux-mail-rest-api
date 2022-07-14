FROM php:cli-alpine AS build

RUN (mkdir -p /flux-namespace-changer && cd /flux-namespace-changer && wget -O - https://github.com/fluxfw/flux-namespace-changer/releases/download/v2022-07-12-1/flux-namespace-changer-v2022-07-12-1-build.tar.gz | tar -xz --strip-components=1)

RUN (mkdir -p /build/flux-mail-rest-api/libs/flux-autoload-api && cd /build/flux-mail-rest-api/libs/flux-autoload-api && wget -O - https://github.com/fluxfw/flux-autoload-api/releases/download/v2022-07-12-1/flux-autoload-api-v2022-07-12-1-build.tar.gz | tar -xz --strip-components=1 && /flux-namespace-changer/bin/change-namespace.php . FluxAutoloadApi FluxMailRestApi\\Libs\\FluxAutoloadApi)

RUN (mkdir -p /build/flux-mail-rest-api/libs/flux-mail-api && cd /build/flux-mail-rest-api/libs/flux-mail-api && wget -O - https://github.com/fluxfw/flux-mail-api/releases/download/v2022-07-12-1/flux-mail-api-v2022-07-12-1-build.tar.gz | tar -xz --strip-components=1 && /flux-namespace-changer/bin/change-namespace.php . FluxMailApi FluxMailRestApi\\Libs\\FluxMailApi)

RUN (mkdir -p /build/flux-mail-rest-api/libs/flux-rest-api && cd /build/flux-mail-rest-api/libs/flux-rest-api && wget -O - https://github.com/fluxfw/flux-rest-api/releases/download/v2022-07-12-1/flux-rest-api-v2022-07-12-1-build.tar.gz | tar -xz --strip-components=1 && /flux-namespace-changer/bin/change-namespace.php . FluxRestApi FluxMailRestApi\\Libs\\FluxRestApi)

COPY . /build/flux-mail-rest-api

FROM php:cli-alpine

LABEL org.opencontainers.image.source="https://github.com/fluxfw/flux-mail-rest-api"

RUN apk add --no-cache imap-dev libstdc++ && \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS openssl-dev && \
    (mkdir -p /usr/src/php/ext/swoole && cd /usr/src/php/ext/swoole && wget -O - https://pecl.php.net/get/swoole | tar -xz --strip-components=1) && \
    docker-php-ext-configure imap --with-imap-ssl && \
    docker-php-ext-configure swoole --enable-openssl --enable-swoole-json && \
    docker-php-ext-install -j$(nproc) imap swoole && \
    docker-php-source delete && \
    apk del .build-deps

USER www-data:www-data

EXPOSE 9501

ENTRYPOINT ["/flux-mail-rest-api/bin/server.php"]

COPY --from=build /build /

ARG COMMIT_SHA
LABEL org.opencontainers.image.revision="$COMMIT_SHA"
