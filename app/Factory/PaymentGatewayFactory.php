<?php

namespace App\Factory;

use App\Config\PaymentGatewayConfig;
use App\Enums\PaymentGateways;
use App\Enums\PaymentMethod;
use App\Integrations\Payments\Contracts\PaymentGatewayInterface;
use Exception;
use Illuminate\Http\Response;

class PaymentGatewayFactory
{
    public function handle(string $gateway, string $paymentMethod): PaymentGatewayInterface
    {
        $gatewayClass = PaymentGatewayConfig::getGateway(PaymentGateways::from($gateway), $paymentMethod);

        if (!$gatewayClass) {
            throw new Exception("Gateway não encontrado para {$gateway} e {$paymentMethod}", Response::HTTP_NOT_FOUND);
        }

        return new $gatewayClass();
    }
}
