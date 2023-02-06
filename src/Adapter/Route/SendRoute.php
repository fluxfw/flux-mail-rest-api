<?php

namespace FluxMailRestApi\Adapter\Route;

use FluxMailRestApi\Adapter\Address\AddressDto;
use FluxMailRestApi\Adapter\Api\MailRestApi;
use FluxMailRestApi\Adapter\Attachment\AttachmentDataEncoding;
use FluxMailRestApi\Adapter\Attachment\AttachmentDto;
use FluxMailRestApi\Adapter\Mail\MailDto;
use FluxRestApi\Adapter\Body\JsonBodyDto;
use FluxRestApi\Adapter\Body\TextBodyDto;
use FluxRestApi\Adapter\Body\Type\DefaultBodyType;
use FluxRestApi\Adapter\Method\DefaultMethod;
use FluxRestApi\Adapter\Method\Method;
use FluxRestApi\Adapter\Route\Documentation\RouteContentTypeDocumentationDto;
use FluxRestApi\Adapter\Route\Documentation\RouteDocumentationDto;
use FluxRestApi\Adapter\Route\Documentation\RouteResponseDocumentationDto;
use FluxRestApi\Adapter\Route\Route;
use FluxRestApi\Adapter\Server\ServerRequestDto;
use FluxRestApi\Adapter\Server\ServerResponseDto;
use FluxRestApi\Adapter\Status\DefaultStatus;

class SendRoute implements Route
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
        if (!($request->parsed_body instanceof JsonBodyDto)) {
            return ServerResponseDto::new(
                TextBodyDto::new(
                    "No json body"
                ),
                DefaultStatus::_400
            );
        }

        $this->mail_rest_api->send(
            MailDto::new(
                $request->parsed_body->data->subject,
                $request->parsed_body->data->body_html,
                array_map(fn(object $address) : AddressDto => AddressDto::new(
                    $address->email,
                    $address->name ?? null
                ), $request->parsed_body->data->to ?? []),
                array_map(fn(object $attachment) : AttachmentDto => AttachmentDto::new(
                    $attachment->name,
                    $attachment->data,
                    ($data_encoding = $attachment->data_encoding ?? null) !== null ? AttachmentDataEncoding::from($data_encoding) : null,
                    $attachment->data_type ?? null
                ), $request->parsed_body->data->attachments ?? []),
                array_map(fn(object $address) : AddressDto => AddressDto::new(
                    $address->email,
                    $address->name ?? null
                ), $request->parsed_body->data->reply_to ?? []),
                array_map(fn(object $address) : AddressDto => AddressDto::new(
                    $address->email,
                    $address->name ?? null
                ), $request->parsed_body->data->cc ?? []),
                array_map(fn(object $address) : AddressDto => AddressDto::new(
                    $address->email,
                    $address->name ?? null
                ), $request->parsed_body->data->bbc ?? []),
                null,
                null,
                null,
                $request->parsed_body->data->body_text ?? null
            )
        );

        return null;
    }
}
