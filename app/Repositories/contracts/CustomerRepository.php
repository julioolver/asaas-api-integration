<?php

namespace App\Repositories\contracts;
use App\Models\Customer;
interface CustomerRepository
{
    public function create(array $client): Customer;
}
