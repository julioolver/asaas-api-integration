<?php
namespace App\DTOs\Payment;
use App\DTOs\BaseDTO;

class PaymentPixDTO extends BaseDTO
{
    public function __construct(
        public string $customer,
        public string $method = "PIX",
        public float $value,
        public string $due_date,
    ) {
    }
}
