<?php

namespace App\Integrations\Payments\Contracts;

use App\DTOs\Customer\CustomerDTO;


interface CustomerGatewayInterface
{
    /**
     * @param CustomerDTO $customer
     */
    public function createCustomer(CustomerDTO $customer): array;
}
