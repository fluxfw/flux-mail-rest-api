<?php

namespace FluxMailRestApi\Adapter\Server;

use FluxMailRestApi\Libs\FluxMailApi\Adapter\Api\MailApi;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Server\SwooleRestApiServer;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Server\SwooleRestApiServerConfigDto;

class MailRestApiServer
{

    private function __construct(
        private readonly SwooleRestApiServer $swoole_rest_api_server
    ) {

    }


    public static function new(
        ?MailRestApiServerConfigDto $mail_rest_api_server_config = null
    ) : static {
        $mail_rest_api_server_config ??= MailRestApiServerConfigDto::newFromEnv();

        return new static(
            SwooleRestApiServer::new(
                MailRestApiServerRouteCollector::new(
                    MailApi::new(
                        $mail_rest_api_server_config->mail_api_config
                    )
                ),
                null,
                SwooleRestApiServerConfigDto::new(
                    $mail_rest_api_server_config->https_cert,
                    $mail_rest_api_server_config->https_key,
                    $mail_rest_api_server_config->listen,
                    $mail_rest_api_server_config->port
                )
            )
        );
    }


    public function init() : void
    {
        $this->swoole_rest_api_server->init();
    }
}
