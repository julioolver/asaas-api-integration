<?php

namespace App\Repositories\eloquent;

use App\Models\Customer;
use App\Repositories\contracts\CustomerRepository;


class EloquentCustomerRepository implements CustomerRepository
{
    public function __construct(protected Customer $model)
    {
    }

    public function create(array $customer): Customer
    {
        return $this->model->create($customer);
    }

    public function updatePaymentGatewayId(Customer $customer, string $paymentId): Customer
    {
        $customer->payment_gateway_id = $paymentId;

        $customer->save();

        return $customer;
    }

    public function findByEmail(string $email): Customer|null
    {
        return $this->model->where('email', $email)->first();
    }

    public function findById(string $id): Customer
    {
        return $this->model->findOrFail($id);
    }
}
