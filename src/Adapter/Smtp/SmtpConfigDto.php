<?php

namespace FluxMailRestApi\Adapter\Smtp;

use FluxMailRestApi\Adapter\Address\AddressDto;
use FluxMailRestApi\Adapter\Mail\EncryptionType;
use SensitiveParameter;

class SmtpConfigDto
{

    private function __construct(
        public readonly string $host,
        public readonly int $port,
        public readonly AddressDto $default_from,
        public readonly ?EncryptionType $encryption_type,
        public readonly ?string $user_name,
        public readonly ?string $password,
        public readonly ?SmtpConfigAuthType $auth_type
    ) {

    }


    public static function new(
        string $host,
        int $port,
        AddressDto $default_from,
        ?EncryptionType $encryption_type = null,
        ?string $user_name = null,
        #[SensitiveParameter] ?string $password = null,
        ?SmtpConfigAuthType $auth_type = null
    ) : static {
        return new static(
            $host,
            $port,
            $default_from,
            $encryption_type,
            $user_name,
            $password,
            $auth_type
        );
    }


    public static function newFromEnv() : static
    {
        return static::new(
            $_ENV["FLUX_MAIL_REST_API_SMTP_HOST"],
            $_ENV["FLUX_MAIL_REST_API_SMTP_PORT"],
            AddressDto::new(
                $_ENV["FLUX_MAIL_REST_API_SMTP_FROM"],
                $_ENV["FLUX_MAIL_REST_API_SMTP_FROM_NAME"] ?? null
            ),
            ($encryption_type = $_ENV["FLUX_MAIL_REST_API_SMTP_ENCRYPTION_TYPE"] ?? null) ? EncryptionType::from($encryption_type) : null,
            ($_ENV["FLUX_MAIL_REST_API_SMTP_USER_NAME"] ?? null) ??
            (($user_name_file = $_ENV["FLUX_MAIL_REST_API_SMTP_USER_NAME_FILE"] ?? null) !== null && file_exists($user_name_file) ? rtrim(file_get_contents($user_name_file) ?: "", "\n\r") : null),
            ($_ENV["FLUX_MAIL_REST_API_SMTP_PASSWORD"] ?? null) ??
            (($password_file = $_ENV["FLUX_MAIL_REST_API_SMTP_PASSWORD_FILE"] ?? null) !== null && file_exists($password_file) ? rtrim(file_get_contents($password_file) ?: "", "\n\r") : null),
            ($auth_type = $_ENV["FLUX_MAIL_REST_API_SMTP_AUTH_TYPE"] ?? null) !== null ? SmtpConfigAuthType::from($auth_type) : null
        );
    }
}
