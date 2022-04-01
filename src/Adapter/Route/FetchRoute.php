<?php

namespace FluxMailRestApi\Adapter\Route;

use FluxMailRestApi\Libs\FluxMailApi\Adapter\Api\MailApi;
use FluxMailRestApi\Libs\FluxRestApi\Body\JsonBodyDto;
use FluxMailRestApi\Libs\FluxRestApi\Libs\FluxRestBaseApi\Method\DefaultMethod;
use FluxMailRestApi\Libs\FluxRestApi\Libs\FluxRestBaseApi\Method\Method;
use FluxMailRestApi\Libs\FluxRestApi\Request\RequestDto;
use FluxMailRestApi\Libs\FluxRestApi\Response\ResponseDto;
use FluxMailRestApi\Libs\FluxRestApi\Route\Route;

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


    public function handle(RequestDto $request) : ?ResponseDto
    {
        return ResponseDto::new(
            JsonBodyDto::new(
                $this->mail_api->fetch()
            )
        );
    }
}
