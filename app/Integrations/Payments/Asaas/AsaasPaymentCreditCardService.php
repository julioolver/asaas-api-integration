<?php

namespace App\Integrations\Payments\Asaas;

use App\Integrations\Payments\Contracts\PaymentGatewayInterface;
use Exception;

class AsaasPaymentCreditCardService extends AsaasHttpClient implements PaymentGatewayInterface
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
                "gateway_payment_id" => $paymentAsaas['id'],
                "bank_url" => $paymentAsaas['transactionReceiptUrl'],
                "invoice_url" => $paymentAsaas['invoiceUrl'],
                "status" => $paymentAsaas['status'],
                'date_confirmation' => $paymentAsaas['confirmedDate'],
                'credit_card' => $paymentAsaas['creditCard'],
            ];
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getPaymentDetails(array $payment): array
    {
        // retonra o próprio array, pois não existe uma chamada externa para obter detalhes do cartão que agregue ao usuário.
        return $payment;
    }

    private function makePayloadCreatePayment($payload, $gatewayCustomerId): array
    {
        return [
            'billingType' => 'CREDIT_CARD',
            'customer' => $gatewayCustomerId,
            'value' => $payload['amount'],
            'dueDate' => $payload['due_date'],
            'creditCard' => [
                'holderName' => $payload['creditCard']->card_holder_name,
                'number' => $payload['creditCard']->card_number,
                'expiryMonth' => $payload['creditCard']->expiry_month,
                'expiryYear' => $payload['creditCard']->expiry_year,
                'ccv' => $payload['creditCard']->cvv,
            ],
            'creditCardHolderInfo' => [
                'name' => $payload['holderInfo']->name,
                'email' => $payload['holderInfo']->email,
                'cpfCnpj' => $payload['holderInfo']->document_number,
                'postalCode' => $payload['holderInfo']->postal_code,
                'addressNumber' => $payload['holderInfo']->address_number,
                'phone' => $payload['holderInfo']->phone,
            ],
        ];
    }
}
