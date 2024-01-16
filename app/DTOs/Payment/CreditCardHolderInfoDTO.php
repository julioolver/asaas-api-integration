<?php

namespace App\DTOs\Payment;

use App\DTOs\BaseDTO;

class CreditCardHolderInfoDTO extends BaseDTO implements PaymentDTOInterface
{
    public function __construct(
        public string $name,
        public string $email,
        public string $document_number,
        public string $postal_code,
        public string $address_number,
        public string $phone,
    ) {
    }
}
