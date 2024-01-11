<?php

namespace App\DTOs\Customer;

class CustomerDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $document_number,
        public ?string $phone = null,
    ) {
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
