<?php

namespace Tests\Feature\Payment;

use App\Integrations\Payments\Asaas\AsaasCustomerService;
use App\Models\Customer;
use App\Repositories\eloquent\EloquentCustomerRepository;
use App\Services\CustomerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class CreatePaymentByPixTest extends TestCase
{
    use RefreshDatabase;

    /**
     * it should be able to create payment by pix.
     */
    public function testCreatePaymentByPix(): void
    {
        $customer = Customer::factory()->create();
        $customer->document_number = '34748015039'; // utilizado https://www.4devs.com.br/gerador_de_cpf

        $customerResponse = $this->postJson('/api/customers/integrate', $customer->toArray())->json();

        $payloadPix = [
            "customer_id" => $customerResponse['data']['id'],
            "amount" => 4540.33,
            "due_date" => date('Y-m-d'),
            "method" => 'pix'
        ];

        $response = $this->postJson('/api/payments/pix', $payloadPix);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'amount',
                'method',
                'pix_details',
                'due_date',
                'status'
            ]
        ]);
    }

    public function testValidationCreatePaymentByPixErrorInExternalApi(): void
    {
        $customerData = [
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'document_number' => '34748015039' // utilizado https://www.4devs.com.br/gerador_de_cpf
        ];

        $customer = Customer::factory()->create();
        $customer->gateway_customer_id = 'invalid_customer_gateway-id';

        $customerService = new EloquentCustomerRepository(new Customer());

        $customerResponse = $customerService->create($customer->toArray());

        $response = $this->postJson('/api/customers', $customerData);

        $payloadPix = [
            "customer_id" => $customerResponse->id,
            "amount" => 4540.33,
            "due_date" => "2024-01-15",
            "method" => 'pix'
        ];

        $response = $this->postJson('/api/payments/pix', $payloadPix);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->assertJsonStructure([
            'success',
            'message'
        ]);
    }

    public function testErrorValidationCreatePaymentByPix(): void
    {
        $customerData = [
            'name' => 'Test Customer',
            'document_number' => '34748015039' // utilizado https://www.4devs.com.br/gerador_de_cpf
        ];

        $customer = Customer::factory()->create();
        $customer->gateway_customer_id = 'invalid_customer_gateway-id';

        $customerService = new EloquentCustomerRepository(new Customer());

        $customerResponse = $customerService->create($customer->toArray());

        $response = $this->postJson('/api/customers', $customerData);

        $payloadPix = [
            "customer_id" => 'invalid_id',
            "amount" => 4540.33,
            "due_date" => "2024-01-15",
            "method" => 'pix'
        ];

        $response = $this->postJson('/api/payments/pix', $payloadPix);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'errors',
            'message'
        ]);
    }
}
