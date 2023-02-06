<?php

namespace FluxMailRestApi\Adapter\Mail;

use SensitiveParameter;

class MailConfigDto
{

    private const BOX_INBOX = "INBOX";


    private function __construct(
        public readonly string $host,
        public readonly int $port,
        public readonly MailConfigType $type,
        public readonly string $user_name,
        public readonly string $password,
        public readonly ?EncryptionType $encryption_type,
        public readonly string $box,
        public readonly bool $mark_as_read
    ) {

    }


    public static function new(
        string $host,
        int $port,
        MailConfigType $type,
        string $user_name,
        #[SensitiveParameter] string $password,
        ?EncryptionType $encryption_type = null,
        ?string $box = null,
        ?bool $mark_as_read = null
    ) : static {
        return new static(
            $host,
            $port,
            $type,
            $user_name,
            $password,
            $encryption_type,
            $box ?? static::BOX_INBOX,
            $mark_as_read ?? true
        );
    }


    public static function newFromEnv() : static
    {
        return static::new(
            $_ENV["FLUX_MAIL_REST_API_MAIL_HOST"],
            $_ENV["FLUX_MAIL_REST_API_MAIL_PORT"],
            MailConfigType::from($_ENV["FLUX_MAIL_REST_API_MAIL_TYPE"]),
            ($_ENV["FLUX_MAIL_REST_API_MAIL_USER_NAME"] ?? null) ??
            (($user_name_file = $_ENV["FLUX_MAIL_REST_API_MAIL_USER_NAME_FILE"] ?? null) !== null && file_exists($user_name_file) ? rtrim(file_get_contents($user_name_file) ?: "", "\n\r") : null),
            ($_ENV["FLUX_MAIL_REST_API_MAIL_PASSWORD"] ?? null) ??
            (($password_file = $_ENV["FLUX_MAIL_REST_API_MAIL_PASSWORD_FILE"] ?? null) !== null && file_exists($password_file) ? rtrim(file_get_contents($password_file) ?: "", "\n\r") : null),
            ($encryption_type = $_ENV["FLUX_MAIL_REST_API_MAIL_ENCRYPTION_TYPE"] ?? null) ? EncryptionType::from($encryption_type) : null,
            $_ENV["FLUX_MAIL_REST_API_MAIL_BOX"] ?? null,
            ($mark_as_read = $_ENV["FLUX_MAIL_REST_API_MAIL_MARK_AS_READ"] ?? null) !== null ? in_array($mark_as_read, ["true", "1"]) : null
        );
    }
}
