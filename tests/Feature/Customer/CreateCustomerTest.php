<?php

namespace Tests\Feature\Customer;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateCustomerTest extends TestCase
{
    /**
     * it should be able to create a customer
     */
    public function testSuccessCreateCustomer(): void
    {
        $customerData = [
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'document_number' => '34748015039' // utilizado https://www.4devs.com.br/gerador_de_cpf
        ];

        $response = $this->postJson('/api/customers', $customerData);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'data' => [
                'name',
                'email',
                'document_number',
                'phone',
                'id',
            ]
        ]);
    }

    /**
     * it should be able not create an customer with validation error 422
     */
    public function testErrorValidationCreateCustomer(): void
    {
        $customerData = [
            'name' => 'John doe',
            'email' => 'customer@example.com',
            'document_number' => '0000000' // cpf incorreto
        ];

        $response = $this->postJson('/api/customers', $customerData);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors'
        ]);
    }
}
