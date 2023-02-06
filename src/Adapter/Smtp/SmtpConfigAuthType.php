<?php

namespace FluxMailRestApi\Adapter\Smtp;

enum SmtpConfigAuthType: string
{

    case CRAM_MD5 = "CRAM-MD5";
    case LOGIN = "LOGIN";
    case PLAIN = "PLAIN";
    case XOAUTH2 = "XOAUTH2";
}
