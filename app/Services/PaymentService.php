<?php

namespace App\Services;

use App\DTOs\Payment\PaymentByBilletDTO;
use App\DTOs\Payment\PaymentDTOInterface;
use App\DTOs\Payment\PaymentPixDTO;
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
        return DB::transaction(function () use ($dto) {
            $customer = $this->initializePaymentProcess($dto, 'pix');
            $payment = $this->processPaymentInRepository($dto, $customer);

            $paymentIntegration = $this->gateway->createPayment($dto->toArray(), $customer->gateway_customer_id);

            $pixDetails = $this->getDetails($paymentIntegration, 'pix');

            return $this->update($payment->id, $pixDetails);
        });
    }

    public function processBilletPayment(PaymentByBilletDTO $dto): Payment
    {

        return DB::transaction(function () use ($dto) {
            $customer = $this->initializePaymentProcess($dto, 'boleto');
            $payment = $this->processPaymentInRepository($dto, $customer);

            $paymentIntegration = $this->gateway->createPayment($dto->toArray(), $customer->gateway_customer_id);

            $billetDetails = $this->getDetails($paymentIntegration, 'boleto');

            return $this->update($payment->id, $billetDetails);
        });
    }

    public function update(int $id, array $data): Payment
    {
        return $this->repository->update($id, $data);
    }

    private function initializePaymentProcess(PaymentDTOInterface $dto, $paymentType)
    {
        $this->validatePaymentMethod($dto, $paymentType);
        $this->gateway = $this->getGateway($dto->provider, $dto->method);
        return $this->customerService->findById($dto->customer_id);
    }


    private function getGateway(string $provider, string $type): PaymentGatewayInterface
    {
        return $this->gatewayFactory->handle($provider, $type);
    }


    private function processPaymentInRepository(PaymentDTOInterface $dto, $customer): Payment
    {
        $paymentData = (clone $dto)->toArray();
        $paymentData["gateway_customer_id"] = $customer->gateway_customer_id;

        return $this->repository->processPixPayment($paymentData);
    }

    private function validatePaymentMethod(PaymentDTOInterface $dto, string $type)
    {
        if ($dto->method !== $type) {
            throw new Exception('Método de pagamento incompatível com a função');
        }
    }
    private function getDetails($paymentIntegration, $method): array
    {
        if (!isset($paymentIntegration['gateway_payment_id'])) {
            throw new Exception('Sem ID de integração, tente novamente mais tarde.');
        }

        $detailsResponse = $this->gateway->getPaymentDetails($paymentIntegration);

        switch ($method) {
            case 'pix':
                return [
                    'gateway_payment_id' => $paymentIntegration['gateway_payment_id'],
                    'pix_data' => json_encode($detailsResponse),
                ];
                break;
            case 'boleto':
                return [
                    ...$detailsResponse,
                    'gateway_payment_id' => $paymentIntegration['gateway_payment_id'],
                ];

            default:
                break;
        }
    }
}
