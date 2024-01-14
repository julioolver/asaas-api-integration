<?php

namespace App\Integrations\Payments\Contracts;

use App\DTOs\Customer\CustomerDTO;


interface PaymentGatewayInterface
{
    public function createPayment(array $payload, string $gatewayCustomerId): array;
    public function getPaymentDetails(array $payment): array;
}
