<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case PIX = 'pix';
    case BOLETO = 'boleto';
    case CREDIT_CARD = 'credit-card';
}
