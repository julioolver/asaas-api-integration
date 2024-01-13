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
        public string $method = PaymentMethod::PIX->value,
        public string $status = PaymentStatus::PENDING->value,
    ) {
    }
}
