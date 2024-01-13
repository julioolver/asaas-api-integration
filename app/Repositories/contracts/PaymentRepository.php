<?php

namespace App\Repositories\contracts;

use App\Models\Payment;

interface PaymentRepository
{
    public function processPixPayment(array $client): Payment;
}
