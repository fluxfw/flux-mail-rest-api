<?php

namespace FluxMailRestApi\Adapter\Server;

use FluxMailRestApi\Libs\FluxMailApi\Adapter\Api\MailApiConfigDto;

class MailRestApiServerConfigDto
{

    private function __construct(
        public readonly MailApiConfigDto $mail_api_config,
        public readonly ?string $https_cert,
        public readonly ?string $https_key,
        public readonly string $listen,
        public readonly int $port
    ) {

    }


    public static function new(
        MailApiConfigDto $mail_api_config,
        ?string $https_cert = null,
        ?string $https_key = null,
        ?string $listen = null,
        ?int $port = null
    ) : static {
        return new static(
            $mail_api_config,
            $https_cert,
            $https_key,
            $listen ?? "0.0.0.0",
            $port ?? 9501
        );
    }


    public static function newFromEnv() : static
    {
        return static::new(
            MailApiConfigDto::newFromEnv(),
            $_ENV["FLUX_MAIL_REST_API_SERVER_HTTPS_CERT"] ?? null,
            $_ENV["FLUX_MAIL_REST_API_SERVER_HTTPS_KEY"] ?? null,
            $_ENV["FLUX_MAIL_REST_API_SERVER_LISTEN"] ?? null,
            $_ENV["FLUX_MAIL_REST_API_SERVER_PORT"] ?? null
        );
    }
}
