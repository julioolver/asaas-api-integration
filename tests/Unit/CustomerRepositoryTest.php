<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Repositories\eloquent\EloquentCustomerRepository;
use Tests\TestCase; // Importa a classe TestCase do Laravel

class CustomerRepositoryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    /**
     * create customer in repository.
     */
    public function testCreateCustomerInRepository(): void
    {

        $clientData = [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'document_number' => '0000000000'
        ];

        $repository = new EloquentCustomerRepository(new Customer);
        $createdCustomerRepository = $repository->create($clientData);

        $this->assertInstanceOf(Customer::class, $createdCustomerRepository);
    }
}
