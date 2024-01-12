<?php

namespace App\Integrations\Payments\Asaas;

use App\Integrations\Payments\Contracts\PaymentGatewayInterface;

class AsaasPaymentPixService extends AsaasHttpClient implements PaymentGatewayInterface
{
    public function createPayment(array $payload): array
    {
        if (isset($payload['gateway_id'])) {
            $payload['customer'] = $payload['gateway_id'];
            unset($payload['gateway_id']);
        }

        $paymentAsaas = $this->post("payments", $payload);

        return [
            "id" => $paymentAsaas["id"],
        ];
    }
}
