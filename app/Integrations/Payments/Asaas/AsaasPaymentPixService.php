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

        $dataPayload = [
            "billingType" => $payload['method'],
            "customer" => $payload['customer'],
            'value' => $payload['amount'],
            'dueDate' => $payload['due_date']
        ];

        try {
            $paymentAsaas = $this->post('payments', $dataPayload);

            if (!$paymentAsaas['id']) {
                throw new Exception('Sem ID de integração, tente novamente mais tarde');
            }

            return [
                "gateway_payment_id" => $paymentAsaas["id"],
            ];
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getPaymentDetails(array $payment): array
    {
        try {
            $paymentId = $payment['id'];

            $detailPixPayment = $this->get("payments/{$paymentId}/pixQrCode");

            return [
                'qrcode' => $detailPixPayment['encodedImage'],
                'pix_key' => $detailPixPayment['payload'],
                'due_date' => $detailPixPayment['expirationDate']
            ];
            
        } catch (\Exception $e) {
            throw $e->getMessage();
        }
    }
}
