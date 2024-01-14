<?php

namespace App\Repositories\contracts;

use App\Models\Customer;

interface CustomerRepository
{
    public function create(array $client): Customer;
    public function updatePaymentGatewayId(Customer $customer, string $gatewayId): Customer;
    public function findByEmail(string $email): Customer|null;
    public function findById(string $id): Customer;
}
