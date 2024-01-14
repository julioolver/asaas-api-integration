<?php

namespace App\Config;

use App\Enums\PaymentGateways;
use App\Integrations\Payments\Asaas\AsaasPaymentBilletService;
use App\Integrations\Payments\Asaas\AsaasPaymentPixService;

class PaymentGatewayConfig
{
    protected static $gatewayMap = [
        "asaas" => [
            "pix" => AsaasPaymentPixService::class,
            "boleto" => AsaasPaymentBilletService::class,
        ],
        "pagarme" => [
            "pix" => "TODO: implementação da calsse service do pagarme para processar pix"
        ]
    ];

    public static function getGateway(PaymentGateways $gateway, string $paymentType)
    {
        return static::$gatewayMap[$gateway->value][$paymentType] ?? null;
    }
}
