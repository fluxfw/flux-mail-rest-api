<?php

namespace FluxMailRestApi\Adapter\Server;

use FluxMailRestApi\Libs\FluxMailApi\Adapter\Api\MailApi;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Api\RestApi;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Route\Collector\RouteCollector;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Server\SwooleServerConfigDto;

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
                MailApi::new(
                    $mail_rest_api_server_config->mail_api_config
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
