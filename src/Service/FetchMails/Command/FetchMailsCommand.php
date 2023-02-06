<?php

namespace FluxMailRestApi\Service\FetchMails\Command;

use DateTime;
use FluxMailRestApi\Adapter\Address\AddressDto;
use FluxMailRestApi\Adapter\Attachment\AttachmentDataEncoding;
use FluxMailRestApi\Adapter\Attachment\AttachmentDto;
use FluxMailRestApi\Adapter\Mail\EncryptionType;
use FluxMailRestApi\Adapter\Mail\MailConfigDto;
use FluxMailRestApi\Adapter\Mail\MailDto;
use PhpImap\Mailbox;

class FetchMailsCommand
{

    private function __construct(
        private readonly MailConfigDto $mail_config
    ) {

    }


    public static function new(
        MailConfigDto $mail_config
    ) : static {
        return new static(
            $mail_config
        );
    }


    /**
     * @return MailDto[]
     */
    public function fetch() : array
    {
        $fetcher = null;
        try {
            $connection_string = "{";

            $connection_string .= $this->mail_config->host;
            $connection_string .= ":" . $this->mail_config->port;

            $connection_string .= "/" . $this->mail_config->type->value;

            if ($this->mail_config->encryption_type !== null) {
                if ($this->mail_config->encryption_type === EncryptionType::TLS_AUTO) {
                    $connection_string .= "/ssl";
                } else {
                    $connection_string .= "/" . $this->mail_config->encryption_type->value;
                    if ($this->mail_config->encryption_type !== EncryptionType::TLS) {
                        $connection_string .= "/notls";
                    }
                }
            }

            $connection_string .= "}";

            $connection_string .= $this->mail_config->box;

            $fetcher = new Mailbox($connection_string, $this->mail_config->user_name, $this->mail_config->password);

            $mails = [];

            foreach ($fetcher->sortMails(SORTARRIVAL, false) as $number) {
                $mail = $fetcher->getMail($number, $this->mail_config->mark_as_read);

                $mails[] = MailDto::new(
                    $mail->subject ?? "",
                    $mail->textHtml ?? "",
                    array_map([AddressDto::class, "new"], array_keys($mail->to), $mail->to),
                    array_map(fn(object $attachment) : AttachmentDto => AttachmentDto::new(
                        $attachment->name,
                        base64_encode($attachment->getContents()),
                        AttachmentDataEncoding::BASE64,
                        $attachment->mime
                    ), $mail->getAttachments()
                    ),
                    array_map([AddressDto::class, "new"], array_keys($mail->replyTo), $mail->replyTo),
                    array_map([AddressDto::class, "new"], array_keys($mail->cc), $mail->cc),
                    array_map([AddressDto::class, "new"], array_keys($mail->bcc), $mail->bcc),
                    AddressDto::new(
                        $mail->fromAddress,
                        $mail->fromName
                    ),
                    (new DateTime($mail->date))->getTimestamp(),
                    $mail->messageId,
                    $mail->textPlain ?? null
                );
            }

            return $mails;
        } finally {
            $fetcher?->disconnect();
        }
    }
}
