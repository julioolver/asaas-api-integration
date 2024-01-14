<?php

namespace App\DTOs\Payment;

use App\DTOs\BaseDTO;
use App\Enums\PaymentGateways;
use App\Enums\PaymentStatus;

abstract class BasePaymentDTO extends BaseDTO
{
    public function __construct(
        public string $customer_id,
        public float $amount,
        public string $due_date,
        public string $method,
        public ?string $provider = null,
        public ?string $status = null,
        public ?string $description = null
    ) {
        {
            $this->provider = $provider ?? PaymentGateways::ASAAS->value;
            $this->status = $status ?? PaymentStatus::PENDING->value;
        }
    }
}
