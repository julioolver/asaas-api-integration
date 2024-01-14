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

            $data["gateway_id"] = $customer->gateway_customer_id;

            $paymentIntegration = $this->gateway->createPayment($data);

            $pixDetails = $this->getDataPix([
                'id' => $paymentIntegration['gateway_payment_id']
            ]);

            $dataToUpdatePayment = [
                'gateway_payment_id' => $paymentIntegration['gateway_payment_id'],
                'pix_data' => json_encode($pixDetails),
            ];

            return $this->update($payment->id, $dataToUpdatePayment);

            DB::commit();

            return $payment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getDataPix(array $paymentPix)
    {
        if (!isset($paymentPix['id'])) {
            throw new Exception('Sem ID de integração, tente novamente mais tarde.');
        }

        return $this->gateway->getPaymentDetails($paymentPix);
    }

    public function update(int $id, array $data): Payment
    {
        return $this->repository->update($id, $data);
    }
}
