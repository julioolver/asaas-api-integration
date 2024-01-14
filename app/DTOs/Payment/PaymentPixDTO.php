<?php

namespace App\DTOs\Payment;

use App\DTOs\BaseDTO;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;

class PaymentPixDTO extends BaseDTO
{
    public function __construct(
        public string $customer_id,
        public float $amount,
        public string $due_date,
        public string $provider,
        public ?string $method = null,
        public ?string $status = null,
    ) {
        {
            $this->method = $method ?? PaymentMethod::PIX->value;
            $this->status = $status ?? PaymentStatus::PENDING->value;
        }
    }
}
