<?php

namespace App\Services;

use App\DTOs\Customer\CustomerDTO;
use App\Models\Customer;
use App\Repositories\contracts\CustomerRepository;

class CustomerService
{
    public function __construct(protected CustomerRepository $repository)
    {
    }
    public function create(CustomerDTO $customer): Customer
    {
        $customerData = (array) $customer;

        return $this->repository->create($customerData);
    }
}
