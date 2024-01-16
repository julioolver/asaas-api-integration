<?php

namespace Tests\Feature\Payment;

use App\Models\Customer;
use App\Repositories\eloquent\EloquentCustomerRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class CreatePaymentByBilletTest extends TestCase
{
    /**
     * it shoud be able to create payment by billet (boleto)
     */
    public function testCreatePaymentByBillet(): void
    {
        $customer = Customer::factory()->create();
        $customer->document_number = '34748015039'; // utilizado https://www.4devs.com.br/gerador_de_cpf

        $customerResponse = $this->postJson('/api/customers/integrate', $customer->toArray())->json();

        $payloadPix = [
            "customer_id" => $customerResponse['data']['id'],
            "amount" => 660.33,
            "due_date" => "2024-01-15",
            "method" => 'boleto'
        ];

        $response = $this->postJson('/api/payments/billet', $payloadPix);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'amount',
                'method',
                'pix_details',
                'due_date',
                'status',
                'bank_url',
                'invoice_url',
                'bar_code'
            ]
        ]);
    }
}
