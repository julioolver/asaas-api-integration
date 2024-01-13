<?php

namespace App\Repositories\eloquent;

use App\Models\Payment;
use App\Repositories\contracts\PaymentRepository;


class EloquentPaymentRepository implements PaymentRepository
{
    public function __construct(protected Payment $model)
    {
    }

    public function processPixPayment(array $payload): Payment
    {
        return $this->model->create($payload);
    }
}
