<?php

namespace FluxMailRestApi\Adapter\Route;

use FluxMailRestApi\Libs\FluxMailApi\Adapter\Api\MailApi;
use FluxMailRestApi\Libs\FluxMailApi\Adapter\Mail\MailDto;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Body\JsonBodyDto;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Body\Type\DefaultBodyType;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Method\DefaultMethod;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Method\Method;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Route\Documentation\RouteDocumentationDto;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Route\Documentation\RouteResponseDocumentationDto;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Route\Route;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Server\ServerRequestDto;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Server\ServerResponseDto;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Status\DefaultStatus;

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


    public function getDocumentation() : ?RouteDocumentationDto
    {
        return RouteDocumentationDto::new(
            $this->getRoute(),
            $this->getMethod(),
            "Fetch emails",
            null,
            null,
            null,
            null,
            [
                RouteResponseDocumentationDto::new(
                    DefaultBodyType::JSON,
                    null,
                    MailDto::class . "[]",
                    "Emails"
                )
            ]
        );
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
