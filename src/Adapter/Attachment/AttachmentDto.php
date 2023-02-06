<?php

namespace FluxMailRestApi\Adapter\Attachment;

class AttachmentDto
{

    private function __construct(
        public readonly string $name,
        public readonly string $data,
        public readonly AttachmentDataEncoding $data_encoding,
        public readonly ?string $data_type
    ) {

    }


    public static function new(
        string $name,
        string $data,
        ?AttachmentDataEncoding $data_encoding,
        ?string $data_type = null
    ) : static {
        return new static(
            $name,
            $data,
            $data_encoding ?? AttachmentDataEncoding::PLAIN,
            $data_type
        );
    }
}
