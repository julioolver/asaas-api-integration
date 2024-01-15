<?php

namespace Tests\Feature\Payment;

use App\Models\Customer;
use App\Repositories\eloquent\EloquentCustomerRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class CreatePaymentByCreditCardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * it should be able to create payment by pix.
     */
    public function testCreatePaymentByCreditCard(): void
    {
        $customerData = [
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'document_number' => '34748015039' // utilizado https://www.4devs.com.br/gerador_de_cpf
        ];

        $customer = Customer::factory()->create();
        $customer->gateway_customer_id = 'cus_000005844687';

        $customerService = new EloquentCustomerRepository(new Customer());

        $customerResponse = $customerService->create($customer->toArray());

        $response = $this->postJson('/api/customers', $customerData);

        $payloadCreditCard = [
            'customer_id' => $customerResponse->id,
            'amount' => 4540.33,
            'due_date' => '2024-01-15',
            'method' => 'credit-card',
            'document_number' => '34748015039',
            'billingType' => '34748015039',
            'credit_card' => [
                'card_number' => '1234567890123456',
                'card_holder_name' => 'teste',
                'expiry_month' => '12',
                'expiry_year' => '2024',
                'cvv' => 669
            ],
            'holder_info' => [
                'name' => $customerResponse->name,
                'email' => $customerResponse->email,
                'document_number' => '34748015039',
                'postal_code' => '99150000',
                'address_number' => '123',
                'phone' => '5499999999',
            ]
        ];

        $response = $this->postJson('/api/payments/credit-card', $payloadCreditCard);

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

    public function ValidationCreatePaymentByCreditCardErrorInExternalApi(): void
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
            'customer_id' => $customerResponse->id,
            'amount' => 4540.33,
            'due_date' => '2024-01-15',
            'method' => 'pix'
        ];

        $response = $this->postJson('/api/payments/pix', $payloadPix);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->assertJsonStructure([
            'success',
            'message'
        ]);
    }

    public function testErrorValidationCreatePaymentByCreditCard(): void
    {
        $payload = [
            'customer_id' => 'invalid_id',
            'amount' => 4540.33,
            'due_date' => '2024-01-15',
            'method' => 'pix',
            'credit_card' => []
        ];

        $response = $this->postJson('/api/payments/pix', $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'errors',
            'message'
        ]);
    }
}
