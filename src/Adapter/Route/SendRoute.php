<?php

namespace FluxMailRestApi\Adapter\Route;

use FluxMailRestApi\Libs\FluxMailApi\Adapter\Address\AddressDto;
use FluxMailRestApi\Libs\FluxMailApi\Adapter\Api\MailApi;
use FluxMailRestApi\Libs\FluxMailApi\Adapter\Attachment\AttachmentDataEncoding;
use FluxMailRestApi\Libs\FluxMailApi\Adapter\Attachment\AttachmentDto;
use FluxMailRestApi\Libs\FluxMailApi\Adapter\Mail\MailDto;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Body\JsonBodyDto;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Body\TextBodyDto;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Body\Type\DefaultBodyType;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Method\DefaultMethod;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Method\Method;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Route\Documentation\RouteContentTypeDocumentationDto;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Route\Documentation\RouteDocumentationDto;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Route\Documentation\RouteResponseDocumentationDto;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Route\Route;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Server\ServerRequestDto;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Server\ServerResponseDto;
use FluxMailRestApi\Libs\FluxRestApi\Adapter\Status\DefaultStatus;

class SendRoute implements Route
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
            "Send email",
            null,
            null,
            null,
            [
                RouteContentTypeDocumentationDto::new(
                    DefaultBodyType::JSON,
                    MailDto::class,
                    "Email"
                )
            ],
            [
                RouteResponseDocumentationDto::new(),
                RouteResponseDocumentationDto::new(
                    DefaultBodyType::TEXT,
                    DefaultStatus::_400,
                    null,
                    "No json body"
                )
            ]
        );
    }


    public function getMethod() : Method
    {
        return DefaultMethod::POST;
    }


    public function getRoute() : string
    {
        return "/send";
    }


    public function handle(ServerRequestDto $request) : ?ServerResponseDto
    {
        if (!($request->getParsedBody() instanceof JsonBodyDto)) {
            return ServerResponseDto::new(
                TextBodyDto::new(
                    "No json body"
                ),
                DefaultStatus::_400
            );
        }

        $this->mail_api->send(
            MailDto::new(
                $request->getParsedBody()->getData()->subject,
                $request->getParsedBody()->getData()->body_html,
                array_map(fn(object $address) : AddressDto => AddressDto::new(
                    $address->email,
                    $address->name ?? null
                ), $request->getParsedBody()->getData()->to ?? []),
                array_map(fn(object $attachment) : AttachmentDto => AttachmentDto::new(
                    $attachment->name,
                    $attachment->data,
                    ($data_encoding = $attachment->data_encoding ?? null) !== null ? AttachmentDataEncoding::from($data_encoding) : null,
                    $attachment->data_type ?? null
                ), $request->getParsedBody()->getData()->attachments ?? []),
                array_map(fn(object $address) : AddressDto => AddressDto::new(
                    $address->email,
                    $address->name ?? null
                ), $request->getParsedBody()->getData()->reply_to ?? []),
                array_map(fn(object $address) : AddressDto => AddressDto::new(
                    $address->email,
                    $address->name ?? null
                ), $request->getParsedBody()->getData()->cc ?? []),
                array_map(fn(object $address) : AddressDto => AddressDto::new(
                    $address->email,
                    $address->name ?? null
                ), $request->getParsedBody()->getData()->bbc ?? []),
                null,
                null,
                null,
                $request->getParsedBody()->getData()->body_text ?? null
            )
        );

        return null;
    }
}
