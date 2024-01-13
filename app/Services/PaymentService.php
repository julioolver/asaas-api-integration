<?php

namespace App\Services;

use App\DTOs\Payment\PaymentPixDTO;
use App\Enums\PaymentMethod;
use App\Factory\PaymentGatewayFactory;
use App\Integrations\Payments\Contracts\PaymentGatewayInterface;
use App\Models\Payment;
use App\Repositories\contracts\PaymentRepository;
use Exception;
use Illuminate\Support\Facades\DB;


class PaymentService
{

    protected PaymentGatewayInterface $gateway;

    public function __construct(
        protected PaymentRepository $repository,
        protected CustomerService $customerService,
        protected PaymentGatewayFactory $gatewayFactory
    ) {
    }

    private function getGateway(string $provider, string $type): PaymentGatewayInterface
    {
        return $this->gatewayFactory->handle($provider, $type);
    }

    public function processPixPayment(PaymentPixDTO $dto): Payment
    {
        try {
            if ($dto->method !== PaymentMethod::PIX->value) {
                throw new Exception('Método de pagamento incompatível com a função');
            }
            $this->gateway = $this->getGateway($dto->provider, $dto->method);

            $customer = $this->customerService->findById($dto->customer_id);

            DB::beginTransaction();

            $data = (clone $dto)->toArray();
            $payment = $this->repository->processPixPayment($data);

            $data["gateway_id"] = $customer->payment_gateway_id;


            $paymentIntegration = $this->gateway->createPayment($data);

            DB::commit();

            return $payment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
