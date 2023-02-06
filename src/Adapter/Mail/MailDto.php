<?php

namespace FluxMailRestApi\Adapter\Mail;

use FluxMailRestApi\Adapter\Address\AddressDto;
use FluxMailRestApi\Adapter\Attachment\AttachmentDto;

class MailDto
{

    /**
     * @param AddressDto[]    $to
     * @param AttachmentDto[] $attachments
     * @param AddressDto[]    $reply_to
     * @param AddressDto[]    $cc
     * @param AddressDto[]    $bcc
     */
    private function __construct(
        public readonly string $subject,
        public readonly string $body_html,
        public readonly array $to,
        public readonly array $attachments,
        public readonly array $reply_to,
        public readonly array $cc,
        public readonly array $bcc,
        public readonly ?AddressDto $from,
        public readonly int $time,
        public readonly ?string $message_id,
        public readonly string $body_text
    ) {

    }


    /**
     * @param AddressDto[]         $to
     * @param AttachmentDto[]|null $attachments
     * @param AddressDto[]|null    $reply_to
     * @param AddressDto[]|null    $cc
     * @param AddressDto[]|null    $bcc
     */
    public static function new(
        string $subject,
        string $body_html,
        array $to,
        ?array $attachments = null,
        ?array $reply_to = null,
        ?array $cc = null,
        ?array $bcc = null,
        ?AddressDto $from = null,
        ?int $time = null,
        ?string $message_id = null,
        ?string $body_text = null
    ) : static {
        return new static(
            $subject,
            $body_html,
            $to,
            $attachments ?? [],
            $reply_to ?? [],
            $cc ?? [],
            $bcc ?? [],
            $from,
            $time ?? time(),
            $message_id,
            $body_text ?? ""
        );
    }
}
