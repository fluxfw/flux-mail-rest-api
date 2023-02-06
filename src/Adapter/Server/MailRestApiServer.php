<?php

namespace FluxMailRestApi\Adapter\Server;

use FluxMailRestApi\Adapter\Api\MailRestApi;
use FluxRestApi\Adapter\Api\RestApi;
use FluxRestApi\Adapter\Route\Collector\RouteCollector;
use FluxRestApi\Adapter\Server\SwooleServerConfigDto;

class MailRestApiServer
{

    private function __construct(
        private readonly RestApi $rest_api,
        private readonly RouteCollector $route_collector,
        private readonly SwooleServerConfigDto $swoole_server_config
    ) {

    }


    public static function new(
        ?MailRestApiServerConfigDto $mail_rest_api_server_config = null
    ) : static {
        $mail_rest_api_server_config ??= MailRestApiServerConfigDto::newFromEnv();

        return new static(
            RestApi::new(),
            MailRestApiServerRouteCollector::new(
                MailRestApi::new(
                    $mail_rest_api_server_config->mail_rest_api_config
                )
            ),
            SwooleServerConfigDto::new(
                $mail_rest_api_server_config->https_cert,
                $mail_rest_api_server_config->https_key,
                $mail_rest_api_server_config->listen,
                $mail_rest_api_server_config->port
            )
        );
    }


    public function init() : void
    {
        $this->rest_api->initSwooleServer(
            $this->route_collector,
            null,
            $this->swoole_server_config
        );
    }
}
