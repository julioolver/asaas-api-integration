<?php

namespace Tests\Unit;

use App\DTOs\Customer\CustomerDTO;
use App\Integrations\Payments\Contracts\CustomerGatewayInterface;
use App\Models\Customer;
use App\Repositories\contracts\CustomerRepository;
use App\Services\CustomerService;
use Mockery;
use Tests\TestCase;

class CustomerServiceTest extends TestCase
{
    public function testCreateCustomer(): void
    {
        $customerRepository = Mockery::mock(CustomerRepository::class);
        $gateayCustomer = Mockery::mock(CustomerGatewayInterface::class);

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

        $gatewayResponse = [
            'id' => '34424242424'
        ];

        $customerRepository->shouldReceive('create')
            ->once()
            ->with($customerDTO->toArray())
            ->andReturn($createdCustomer);

        $customerRepository->shouldReceive('updatePaymentGatewayId')
            ->once()
            ->with($createdCustomer, $gatewayResponse['id'])
            ->andReturnUsing(function ($customer, $gatewayId) {
                $customer->payment_gateway_id = $gatewayId;
                return $customer;
            });

        $gateayCustomer->shouldReceive('createCustomer')
            ->once()
            ->with($customerDTO)
            ->andReturn($gatewayResponse);

        $customerService = new CustomerService($customerRepository, $gateayCustomer);

        $result = $customerService->create($customerDTO);

        $this->assertInstanceOf(Customer::class, $result);
        $this->assertEquals('John Doe', $result->name);
        $this->assertEquals('john@doe.com', $result->email);
        $this->assertEquals('0000000000', $result->document_number);
    }

    public function testFindCustomerByEmail(): void
    {
        $repository = Mockery::mock(CustomerRepository::class);
        $gateayCustomer = Mockery::mock(CustomerGatewayInterface::class);

        $customerCreated = Customer::factory()->create();

        $repository->shouldReceive('findByEmail')
            ->once()
            ->with($customerCreated->email)
            ->andReturn($customerCreated);

        $service = new CustomerService($repository, $gateayCustomer);

        $customerResponse = $service->findByEmail($customerCreated->email);

        $this->assertInstanceOf(Customer::class, $customerResponse);
        $this->assertEquals($customerCreated->email, $customerResponse->email);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
