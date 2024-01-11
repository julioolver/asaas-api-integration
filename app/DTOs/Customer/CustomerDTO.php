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
}
