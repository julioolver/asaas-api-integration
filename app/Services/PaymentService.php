<?php

namespace App\Services;

use App\DTOs\Payment\PaymentPixDTO;
use App\Integrations\Payments\Contracts\PaymentGatewayInterface;
use App\Models\Payment;
use App\Repositories\contracts\PaymentRepository;
use Exception;
use Illuminate\Support\Facades\DB;


class PaymentService
{

    protected PaymentGatewayInterface $paymentGateway;

    public function __construct(
        protected PaymentRepository $repository,
        protected CustomerService $customerService
    ) {
    }

    public function setPaymentGateway(PaymentGatewayInterface $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function processPixPayment(PaymentPixDTO $dto): Payment
    {
        try {
            $customer = $this->customerService->findById($dto->customer_id);
            $data = (clone $dto)->toArray();
            $data["gateway_id"] = $customer->payment_gateway_id;

            DB::beginTransaction();

            $payment = $this->repository->processPixPayment($data);

            $paymentIntegration = $this->paymentGateway->createPayment($data);

            DB::commit();
            dd($paymentIntegration);

            if (!$paymentIntegration['id']) {
                throw new Exception('Sem ID de integração, tente novamente mais tarde');
            }

            DB::commit();

            return $payment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
