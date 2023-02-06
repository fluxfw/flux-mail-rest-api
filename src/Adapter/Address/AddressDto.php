<?php

namespace FluxMailRestApi\Adapter\Address;

class AddressDto
{

    private function __construct(
        public readonly string $email,
        public readonly ?string $name
    ) {

    }


    public static function new(
        string $email,
        ?string $name = null
    ) : static {
        return new static(
            $email,
            $name
        );
    }
}
