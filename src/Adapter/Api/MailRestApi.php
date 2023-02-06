<?php

namespace FluxMailRestApi\Adapter\Api;

use FluxMailRestApi\Adapter\Mail\MailDto;
use FluxMailRestApi\Service\FetchMails\Port\FetchMailsService;
use FluxMailRestApi\Service\SendMail\Port\SendMailService;

class MailRestApi
{

    private function __construct(
        private readonly MailRestApiConfigDto $mail_rest_api_config
    ) {

    }


    public static function new(
        ?MailRestApiConfigDto $mail_rest_api_config = null
    ) : static {
        return new static(
            $mail_rest_api_config ?? MailRestApiConfigDto::newFromEnv()
        );
    }


    /**
     * @return MailDto[]
     */
    public function fetch() : array
    {
        return $this->getFetchMailsService()
            ->fetch();
    }


    public function send(MailDto $mail) : void
    {
        $this->getSendMailService()
            ->send(
                $mail
            );
    }


    private function getFetchMailsService() : FetchMailsService
    {
        return FetchMailsService::new(
            $this->mail_rest_api_config->mail_config
        );
    }


    private function getSendMailService() : SendMailService
    {
        return SendMailService::new(
            $this->mail_rest_api_config->smtp_config
        );
    }
}
