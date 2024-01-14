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

    public function processPixPayment(PaymentPixDTO $dto): Payment
    {
        $this->validatePaymentMethod($dto);

        $this->gateway = $this->getGateway($dto->provider, $dto->method);
        $customer = $this->customerService->findById($dto->customer_id);

        DB::beginTransaction();

        try {
            $payment = $this->processPaymentInRepository($dto, $customer);

            $paymentIntegration = $this->gateway->createPayment($dto->toArray(), $customer->gateway_customer_id);

            $pixDetails = $this->getPixDetails($paymentIntegration,);

            return $this->update($payment->id, $pixDetails);

            DB::commit();

            return $payment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getDataPix(array $paymentPix): array
    {
        if (!isset($paymentPix['gateway_payment_id'])) {
            throw new Exception('Sem ID de integração, tente novamente mais tarde.');
        }

        return $this->gateway->getPaymentDetails($paymentPix);
    }

    public function update(int $id, array $data): Payment
    {
        return $this->repository->update($id, $data);
    }

    private function getGateway(string $provider, string $type): PaymentGatewayInterface
    {
        return $this->gatewayFactory->handle($provider, $type);
    }


    private function processPaymentInRepository(PaymentPixDTO $dto, $customer): Payment
    {
        $paymentData = (clone $dto)->toArray();
        $paymentData["gateway_customer_id"] = $customer->gateway_customer_id;

        return $this->repository->processPixPayment($paymentData);
    }

    private function validatePaymentMethod(PaymentPixDTO $dto)
    {
        if ($dto->method !== PaymentMethod::PIX->value) {
            throw new Exception('Método de pagamento incompatível com a função');
        }
    }
    private function getPixDetails($paymentIntegration): array
    {
        $pixDetailsResponse = $this->getDataPix([
            'gateway_payment_id' => $paymentIntegration['gateway_payment_id']
        ]);

        return [
            'gateway_payment_id' => $paymentIntegration['gateway_payment_id'],
            'pix_data' => json_encode($pixDetailsResponse),
        ];
    }
}
