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

            $customer = $this->createCustomer($data);

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

    public function createCustomer(CustomerDTO $data): Customer
    {
        $customerData = $data->toArray();
        return $this->repository->create($customerData);
    }

    public function findByEmail(string $email): Customer|null
    {
        $customer = $this->repository->findByEmail($email);

        if (!$customer) {
            throw new Exception('Cliente não encontrado', 404);
        }

        return $customer;
    }

    public function findById(string $id): Customer
    {
        return $this->repository->findById($id);
    }
}
