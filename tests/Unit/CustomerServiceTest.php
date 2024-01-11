<?php

namespace Tests\Unit;

use App\DTOs\Customer\CustomerDTO;
use App\Models\Customer;
use App\Repositories\contracts\CustomerRepository;
use App\Services\CustomerService;
use Mockery;
use PHPUnit\Framework\TestCase;

class CustomerServiceTest extends TestCase
{
    public function testCreateCustomer(): void
    {
        $customerRepository = Mockery::mock(CustomerRepository::class);

        $customerDTO = new CustomerDTO(
            name: 'John Doe',
            email: 'john@doe.com',
            document_number: '0000000000'
        );

        $createdCustomer = new Customer([
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'document_number' => '0000000000',
        ]);

        $customerRepository->shouldReceive('create')
            ->once()
            ->with((array) $customerDTO)
            ->andReturn($createdCustomer);

        $customerService = new CustomerService($customerRepository);

        $result = $customerService->create($customerDTO);

        $this->assertInstanceOf(Customer::class, $result);
        $this->assertEquals('John Doe', $result->name);
        $this->assertEquals('john@doe.com', $result->email);
        $this->assertEquals('0000000000', $result->document_number);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
