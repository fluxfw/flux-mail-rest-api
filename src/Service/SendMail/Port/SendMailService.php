<?php

namespace FluxMailRestApi\Service\SendMail\Port;

use FluxMailRestApi\Adapter\Mail\MailDto;
use FluxMailRestApi\Adapter\Smtp\SmtpConfigDto;
use FluxMailRestApi\Service\SendMail\Command\SendMailCommand;

class SendMailService
{

    private function __construct(
        private readonly SmtpConfigDto $smtp_config
    ) {

    }


    public static function new(
        SmtpConfigDto $smtp_config
    ) : static {
        return new static(
            $smtp_config
        );
    }


    public function send(MailDto $mail) : void
    {
        SendMailCommand::new(
            $this->smtp_config
        )
            ->send(
                $mail
            );
    }
}
