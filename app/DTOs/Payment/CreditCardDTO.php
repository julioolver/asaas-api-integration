<?php

namespace App\DTOs\Payment;

use App\DTOs\BaseDTO;

class CreditCardDTO extends BaseDTO implements PaymentDTOInterface
{
    public function __construct(
        public string $card_number,
        public string $card_holder_name,
        public string $expiry_month,
        public string $expiry_year,
        public string $cvv
    ) {
    }
}
