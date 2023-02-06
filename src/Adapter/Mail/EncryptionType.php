<?php

namespace FluxMailRestApi\Adapter\Mail;

enum EncryptionType: string
{

    case SSL = "ssl";
    case TLS = "tls";
    case TLS_AUTO = "tls-auto";
}
