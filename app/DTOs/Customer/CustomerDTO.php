<?php

namespace App\DTOs\Customer;
use App\DTOs\BaseDTO;

class CustomerDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $document_number,
        public ?string $phone = null,
    ) {
    }
}
