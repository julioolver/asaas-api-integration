<?php

namespace App\Integrations\Payments\Asaas;

use App\Integrations\Payments\Contracts\PaymentGatewayInterface;
use Exception;

class AsaasPaymentBilletService extends AsaasHttpClient implements PaymentGatewayInterface
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
                "bank_url" => $paymentAsaas['bankSlipUrl'],
                "invoice_url" => $paymentAsaas['invoiceUrl']
            ];
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getPaymentDetails(array $payment): array
    {
        try {
            $paymentId = $payment['gateway_payment_id'];

            $detailPixPayment = $this->get("payments/{$paymentId}/identificationField");

            return [
                'nosso_numero' => $detailPixPayment['nossoNumero'],
                'bar_code' => $detailPixPayment['barCode'],
                'identification_field' => $detailPixPayment['identificationField'],
                "bank_url" => $payment['bank_url'],
                "invoice_url" => $payment['invoice_url']
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function makePayloadCreatePayment($payload, $gatewayCustomerId): array
    {
        return [
            "billingType" => $payload['method'],
            "customer" => $gatewayCustomerId,
            'value' => $payload['amount'],
            'dueDate' => $payload['due_date']
        ];
    }
}
