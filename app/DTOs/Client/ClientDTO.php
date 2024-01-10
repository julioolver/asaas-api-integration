<?php

namespace App\DTOs\Client;

class ClientDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $document_number,
        public ?string $phone = null,
    ) {
    }
}
