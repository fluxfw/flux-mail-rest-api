<?php

namespace FluxMailRestApi\Adapter\Api;

use FluxMailRestApi\Adapter\Mail\MailConfigDto;
use FluxMailRestApi\Adapter\Smtp\SmtpConfigDto;

class MailRestApiConfigDto
{

    private function __construct(
        public readonly MailConfigDto $mail_config,
        public readonly SmtpConfigDto $smtp_config
    ) {

    }


    public static function new(
        MailConfigDto $mail_config,
        SmtpConfigDto $smtp_config
    ) : static {
        return new static(
            $mail_config,
            $smtp_config
        );
    }


    public static function newFromEnv() : static
    {
        return static::new(
            MailConfigDto::newFromEnv(),
            SmtpConfigDto::newFromEnv()
        );
    }
}
