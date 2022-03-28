<?php

namespace FluxMailRestApi\Adapter\Route;

use FluxMailRestApi\Libs\FluxMailApi\Adapter\Address\AddressDto;
use FluxMailRestApi\Libs\FluxMailApi\Adapter\Api\MailApi;
use FluxMailRestApi\Libs\FluxMailApi\Adapter\Attachment\AttachmentDataEncoding;
use FluxMailRestApi\Libs\FluxMailApi\Adapter\Attachment\AttachmentDto;
use FluxMailRestApi\Libs\FluxMailApi\Adapter\Mail\MailDto;
use FluxMailRestApi\Libs\FluxRestApi\Body\JsonBodyDto;
use FluxMailRestApi\Libs\FluxRestApi\Body\TextBodyDto;
use FluxMailRestApi\Libs\FluxRestApi\Libs\FluxRestBaseApi\Body\DefaultBodyType;
use FluxMailRestApi\Libs\FluxRestApi\Libs\FluxRestBaseApi\Method\DefaultMethod;
use FluxMailRestApi\Libs\FluxRestApi\Libs\FluxRestBaseApi\Method\Method;
use FluxMailRestApi\Libs\FluxRestApi\Libs\FluxRestBaseApi\Status\DefaultStatus;
use FluxMailRestApi\Libs\FluxRestApi\Request\RequestDto;
use FluxMailRestApi\Libs\FluxRestApi\Response\ResponseDto;
use FluxMailRestApi\Libs\FluxRestApi\Route\Route;

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


    public function getDocuRequestBodyTypes() : ?array
    {
        return [
            DefaultBodyType::JSON
        ];
    }


    public function getDocuRequestQueryParams() : ?array
    {
        return null;
    }


    public function getMethod() : Method
    {
        return DefaultMethod::POST;
    }


    public function getRoute() : string
    {
        return "/send";
    }


    public function handle(RequestDto $request) : ?ResponseDto
    {
        if (!($request->getParsedBody() instanceof JsonBodyDto)) {
            return ResponseDto::new(
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
