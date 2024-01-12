<?php

namespace App\Integrations\Payments\Contracts;

use App\DTOs\Customer\CustomerDTO;


interface PaymentGatewayInterface
{
    public function createPayment(array $payload): array;
}
