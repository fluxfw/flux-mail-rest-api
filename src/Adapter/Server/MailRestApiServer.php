<?php

namespace FluxMailRestApi\Adapter\Server;

use FluxMailRestApi\Libs\FluxMailApi\Adapter\Api\MailApi;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Collector\FolderRouteCollector;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Handler\SwooleHandler;
use Swoole\Http\Server;

class MailRestApiServer
{

    private function __construct(
        private readonly MailRestApiServerConfigDto $mail_rest_api_server_config,
        private readonly SwooleHandler $swoole_handler
    ) {

    }


    public static function new(
        ?MailRestApiServerConfigDto $mail_rest_api_server_config = null
    ) : static {
        $mail_rest_api_server_config ??= MailRestApiServerConfigDto::newFromEnv();

        return new static(
            $mail_rest_api_server_config,
            SwooleHandler::new(
                FolderRouteCollector::new(
                    __DIR__ . "/../Route",
                    [
                        MailApi::new(
                            $mail_rest_api_server_config->mail_api_config
                        )
                    ]
                )
            )
        );
    }


    public function init() : void
    {
        $options = [];
        $sock_type = SWOOLE_TCP;

        if ($this->mail_rest_api_server_config->https_cert !== null) {
            $options += [
                "ssl_cert_file" => $this->mail_rest_api_server_config->https_cert,
                "ssl_key_file"  => $this->mail_rest_api_server_config->https_key
            ];
            $sock_type += SWOOLE_SSL;
        }

        $server = new Server($this->mail_rest_api_server_config->listen, $this->mail_rest_api_server_config->port, SWOOLE_PROCESS, $sock_type);

        $server->set($options);

        $server->on("request", [$this->swoole_handler, "handle"]);

        $server->start();
    }
}
