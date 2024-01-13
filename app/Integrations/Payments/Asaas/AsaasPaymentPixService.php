<?php

namespace App\Integrations\Payments\Asaas;

use App\Integrations\Payments\Contracts\PaymentGatewayInterface;
use Exception;

class AsaasPaymentPixService extends AsaasHttpClient implements PaymentGatewayInterface
{
    public function createPayment(array $payload): array
    {
        if (isset($payload['gateway_id'])) {
            $payload['customer'] = $payload['gateway_id'];
            unset($payload['gateway_id']);
        }

        try {
            $paymentAsaas = $this->post('payments', $payload);

            if (!$paymentAsaas['id']) {
                throw new Exception('Sem ID de integração, tente novamente mais tarde');
            }

            return [
                "id" => $paymentAsaas["id"],
            ];
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
