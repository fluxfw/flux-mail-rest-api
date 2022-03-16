ARG ALPINE_IMAGE=alpine:latest
ARG FLUX_AUTOLOAD_API_IMAGE=docker-registry.fluxpublisher.ch/flux-autoload/api:latest
ARG FLUX_MAIL_API=docker-registry.fluxpublisher.ch/flux-mail/api:latest
ARG FLUX_NAMESPACE_CHANGER_IMAGE=docker-registry.fluxpublisher.ch/flux-namespace-changer:latest
ARG FLUX_REST_API_IMAGE=docker-registry.fluxpublisher.ch/flux-rest/api:latest
ARG PHP_CLI_IMAGE=php:cli-alpine
ARG SWOOLE_SOURCE_URL=https://pecl.php.net/get/swoole

FROM $FLUX_AUTOLOAD_API_IMAGE AS flux_autoload_api
FROM $FLUX_NAMESPACE_CHANGER_IMAGE AS flux_autoload_api_build
ENV FLUX_NAMESPACE_CHANGER_FROM_NAMESPACE FluxAutoloadApi
ENV FLUX_NAMESPACE_CHANGER_TO_NAMESPACE FluxMailRestApi\\Libs\\FluxAutoloadApi
COPY --from=flux_autoload_api /flux-autoload-api /code
RUN $FLUX_NAMESPACE_CHANGER_BIN

FROM $FLUX_MAIL_API AS flux_mail_api
FROM $FLUX_NAMESPACE_CHANGER_IMAGE AS flux_mail_api_build
ENV FLUX_NAMESPACE_CHANGER_FROM_NAMESPACE FluxMailApi
ENV FLUX_NAMESPACE_CHANGER_TO_NAMESPACE FluxMailRestApi\\Libs\\FluxMailApi
COPY --from=flux_mail_api /flux-mail-api /code
RUN $FLUX_NAMESPACE_CHANGER_BIN

FROM $FLUX_REST_API_IMAGE AS flux_rest_api
FROM $FLUX_NAMESPACE_CHANGER_IMAGE AS flux_rest_api_build
ENV FLUX_NAMESPACE_CHANGER_FROM_NAMESPACE FluxRestApi
ENV FLUX_NAMESPACE_CHANGER_TO_NAMESPACE FluxMailRestApi\\Libs\\FluxRestApi
COPY --from=flux_rest_api /flux-rest-api /code
RUN $FLUX_NAMESPACE_CHANGER_BIN

FROM $ALPINE_IMAGE AS build

COPY --from=flux_autoload_api_build /code /flux-mail-rest-api/libs/flux-autoload-api
COPY --from=flux_mail_api_build /code /flux-mail-rest-api/libs/flux-mail-api
COPY --from=flux_rest_api_build /code /flux-mail-rest-api/libs/flux-rest-api
COPY . /flux-mail-rest-api

FROM $PHP_CLI_IMAGE
ARG SWOOLE_SOURCE_URL

LABEL org.opencontainers.image.source="https://github.com/fluxapps/flux-mail-rest-api"
LABEL maintainer="fluxlabs <support@fluxlabs.ch> (https://fluxlabs.ch)"

RUN apk add --no-cache imap-dev libstdc++ && \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS openssl-dev && \
    (mkdir -p /usr/src/php/ext/swoole && cd /usr/src/php/ext/swoole && wget -O - $SWOOLE_SOURCE_URL | tar -xz --strip-components=1) && \
    docker-php-ext-configure imap --with-imap-ssl && \
    docker-php-ext-configure swoole --enable-openssl --enable-swoole-json && \
    docker-php-ext-install -j$(nproc) imap swoole && \
    docker-php-source delete && \
    apk del .build-deps

USER www-data:www-data

EXPOSE 9501

ENTRYPOINT ["/flux-mail-rest-api/bin/server.php"]

COPY --from=build /flux-mail-rest-api /flux-mail-rest-api
