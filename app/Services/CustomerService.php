<?php

namespace App\Services;

use App\DTOs\Customer\CustomerDTO;
use App\Integrations\Payments\Contracts\CustomerGatewayInterface;
use App\Models\Customer;
use App\Repositories\contracts\CustomerRepository;
use Exception;
use Illuminate\Support\Facades\DB;


class CustomerService
{
    public function __construct(protected CustomerRepository $repository, protected CustomerGatewayInterface $gateway)
    {
    }
    public function create(CustomerDTO $data): Customer
    {
        try {
            DB::beginTransaction();

            $customerData = $data->toArray();
            $customer = $this->repository->create($customerData);

            $customerIntegration = $this->gateway->createCustomer($data);

            if (!$customerIntegration['id']) {
                throw new Exception('Sem ID de integração, tente novamente mais tarde');
            }

            $customer = $this->repository->updatePaymentGatewayId($customer, $customerIntegration['id']);

            DB::commit();
            return $customer;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function findByEmail(string $email): Customer|null
    {
        $customer = $this->repository->findByEmail($email);

        if (!$customer) {
            throw new Exception('Usuário não encontrado', 404);
        }

        return $customer;
    }
}
