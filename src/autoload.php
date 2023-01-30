<?php

namespace FluxMailRestApi;

require_once __DIR__ . "/../libs/flux-mail-api/autoload.php";
require_once __DIR__ . "/../libs/flux-rest-api/autoload.php";
require_once __DIR__ . "/../libs/php-imap/vendor/autoload.php";
require_once __DIR__ . "/../libs/PHPMailer/vendor/autoload.php";

spl_autoload_register(function (string $class) : void {
    if (str_starts_with($class, __NAMESPACE__ . "\\")) {
        require_once __DIR__ . str_replace("\\", "/", substr($class, strlen(__NAMESPACE__))) . ".php";
    }
});
