<?php

namespace App\Integrations\Payments\Asaas;

use App\Integrations\Payments\Contracts\PaymentGatewayInterface;
use Exception;

class AsaasPaymentPixService extends AsaasHttpClient implements PaymentGatewayInterface
{
    public function createPayment(array $payload, string $gatewayCustomerId): array
    {
        $dataPayload = $this->makePayloadCreatePayment($payload, $gatewayCustomerId);

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

    private function makePayloadCreatePayment($payload, $gatewayCustomerId)
    {
        return [
            "billingType" => $payload['method'],
            "customer" => $gatewayCustomerId,
            'value' => $payload['amount'],
            'dueDate' => $payload['due_date']
        ];
    }

    public function getPaymentDetails(array $payment): array
    {
        try {
            $paymentId = $payment['gateway_payment_id'];

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
