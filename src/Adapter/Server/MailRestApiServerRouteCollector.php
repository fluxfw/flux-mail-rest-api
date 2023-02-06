<?php

namespace FluxMailRestApi\Adapter\Server;

use FluxMailRestApi\Adapter\Api\MailRestApi;
use FluxMailRestApi\Adapter\Route\FetchRoute;
use FluxMailRestApi\Adapter\Route\SendRoute;
use FluxRestApi\Adapter\Route\Collector\RouteCollector;

class MailRestApiServerRouteCollector implements RouteCollector
{

    private function __construct(
        private readonly MailRestApi $mail_rest_api
    ) {

    }


    public static function new(
        MailRestApi $mail_rest_api
    ) : static {
        return new static(
            $mail_rest_api
        );
    }


    public function collectRoutes() : array
    {
        return [
            FetchRoute::new(
                $this->mail_rest_api
            ),
            SendRoute::new(
                $this->mail_rest_api
            )
        ];
    }
}
