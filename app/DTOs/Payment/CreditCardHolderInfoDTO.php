<?php

namespace App\DTOs\Payment;

use App\DTOs\BaseDTO;

class CreditCardHolderInfoDTO extends BaseDTO implements PaymentDTOInterface
{
    public function __construct(
        public string $name,
        public string $email,
        public string $documentNumber,
        public string $postalCode,
        public string $addressNumber,
        public string $phone,
    ) {
    }
}
