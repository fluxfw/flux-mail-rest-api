<?php

namespace FluxMailRestApi\Adapter\Attachment;

enum AttachmentDataEncoding: string
{

    case BASE64 = "base64";
    case PLAIN = "plain";
}
