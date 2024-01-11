<?php

namespace Tests\Feature\Customer;

use Illuminate\Http\Response;
use Tests\TestCase;

class FindByEmailCustomerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testFindCustomerByEmail(): void
    {
        $customerData = [
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'document_number' => '34748015039' // utilizado https://www.4devs.com.br/gerador_de_cpf
        ];

        $this->postJson('/api/customers', $customerData);

        $response = $this->getJson('/api/customers/by-email?email=customer@example.com');

        $response->assertOk();
        $response->assertStatus(200);
    }

    public function testFindCustomerByEmailWithoutResult(): void
    {
        $customerData = [
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'document_number' => '34748015039' // utilizado https://www.4devs.com.br/gerador_de_cpf
        ];

        $this->postJson('/api/customers', $customerData);

        $response = $this->getJson('/api/customers/by-email?email=withoutcustomer@example.com');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
