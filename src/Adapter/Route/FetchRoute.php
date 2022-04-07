<?php

namespace FluxMailRestApi\Adapter\Route;

use FluxMailRestApi\Libs\FluxMailApi\Adapter\Api\MailApi;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Body\JsonBodyDto;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Method\DefaultMethod;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Method\Method;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Route\Route;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Server\ServerRequestDto;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Server\ServerResponseDto;

class FetchRoute implements Route
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


    public function getDocuRequestBodyTypes() : ?array
    {
        return null;
    }


    public function getDocuRequestQueryParams() : ?array
    {
        return null;
    }


    public function getMethod() : Method
    {
        return DefaultMethod::GET;
    }


    public function getRoute() : string
    {
        return "/fetch";
    }


    public function handle(ServerRequestDto $request) : ?ServerResponseDto
    {
        return ServerResponseDto::new(
            JsonBodyDto::new(
                $this->mail_api->fetch()
            )
        );
    }
}
