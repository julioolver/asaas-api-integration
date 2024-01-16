<?php

namespace App\Repositories\eloquent;

use App\Models\Payment;
use App\Repositories\contracts\PaymentRepository;


class EloquentPaymentRepository implements PaymentRepository
{
    public function __construct(protected Payment $model)
    {
    }

    public function createPayment(array $payload): Payment
    {
        return $this->model->create($payload);
    }

    public function findById(int $id): Payment
    {
        return $this->model->findOrFail($id);
    }

    public function update(int $id, array $data): Payment
    {
        $model = $this->findById($id);
        $model->update($data);

        return $model;
    }
}
