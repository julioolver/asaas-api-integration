<?php

namespace App\Repositories\contracts;

use App\Models\Payment;

interface PaymentRepository
{
    public function createPayment(array $client): Payment;
    public function findById(int $id): Payment;
    public function update(int $id, array $data): Payment;
}
