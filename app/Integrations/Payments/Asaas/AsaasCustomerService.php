<?php

namespace App\Integrations\Payments\Asaas;

use App\DTOs\Customer\CustomerDTO;
use App\Integrations\Payments\Contracts\CustomerGatewayInterface;

class AsaasCustomerService extends AsaasHttpClient implements CustomerGatewayInterface
{
    public function createCustomer(CustomerDTO $customer): array
    {
        $customerData = $customer->toArray();

        $customerData['cpfCnpj'] = $customerData['document_number'];
        unset($customerData['document_number']);

        $customerAsaas = $this->post("customers", $customerData);

        return [
            "id" => $customerAsaas["id"],
        ];
    }
}
