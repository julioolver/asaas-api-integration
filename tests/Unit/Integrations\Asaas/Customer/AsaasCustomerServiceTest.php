<?php

namespace Tests\Unit\Integrations\Asaas\Customer;

use App\DTOs\Customer\CustomerDTO;
use App\Integrations\Payments\Asaas\AsaasCustomerService;
use App\Models\Customer;
use Tests\TestCase;

class AsaasCustomerServiceTest extends TestCase
{
    /**
     * it should be able to create customer on Asaas Integration
     */
    public function testCreateCustomerInAsaasIntegration(): void
    {
        $customer = Customer::factory()->create();
        $customerDTO = new CustomerDTO(
            name: $customer->name,
            email: $customer->email,
            document_number: $customer->document_number
        );

        $asaasCustomerService = new AsaasCustomerService();

        $customerAsaas = $asaasCustomerService->createCustomer($customerDTO);

        $this->assertStringContainsString("cus_", $customerAsaas["id"]);
    }
}
