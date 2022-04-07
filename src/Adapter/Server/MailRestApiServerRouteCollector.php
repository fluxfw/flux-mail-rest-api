<?php

namespace FluxMailRestApi\Adapter\Server;

use FluxMailRestApi\Adapter\Route\FetchRoute;
use FluxMailRestApi\Adapter\Route\SendRoute;
use FluxMailRestApi\Libs\FluxMailApi\Adapter\Api\MailApi;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Route\Collector\RouteCollector;

class MailRestApiServerRouteCollector implements RouteCollector
{

    private function __construct(
        private readonly MailApi $mail_api
    ) {

    }


    public static function new(
        MailApi $mail_api
    ) : static {
        return new static(
            $mail_api
        );
    }


    public function collectRoutes() : array
    {
        return [
            FetchRoute::new(
                $this->mail_api
            ),
            SendRoute::new(
                $this->mail_api
            )
        ];
    }
}
