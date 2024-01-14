<?php

namespace Tests\Unit;

use App\DTOs\Payment\PaymentPixDTO;
use App\Enums\PaymentMethod;
use App\Factory\PaymentGatewayFactory;
use App\Integrations\Payments\Asaas\AsaasPaymentPixService;
use App\Integrations\Payments\Contracts\CustomerGatewayInterface;
use App\Integrations\Payments\Contracts\PaymentGatewayInterface;
use App\Models\Customer;
use App\Models\Payment;
use App\Repositories\contracts\CustomerRepository;
use App\Repositories\contracts\PaymentRepository;
use App\Repositories\eloquent\EloquentCustomerRepository;
use App\Services\CustomerService;
use App\Services\PaymentService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    public function testCreatePaymentByPix(): void
    {
        Http::fake([
            '*' => Http::response(['id' => '1234'], 200)
        ]);

        $paymentRepository = Mockery::mock(PaymentRepository::class);
        $customerRepository = Mockery::mock(CustomerRepository::class);
        $customerGateway = Mockery::mock(CustomerGatewayInterface::class);
        $asaasServiceMock = Mockery::mock(AsaasPaymentPixService::class);
        $paymentGatewayFactory = Mockery::mock(PaymentGatewayFactory::class);

        $customerCreated = Customer::factory()->create();

        $customerRepository->shouldReceive('findById')
            ->once()
            ->andReturn($customerCreated);

        $fakePaymentPixDTO = new PaymentPixDTO(
            customer_id: $customerCreated->id,
            amount: 4540,
            due_date: '2024-01-15',
            provider: 'asaas',
            method: PaymentMethod::PIX->value
        );

        $fakePayment = new Payment($fakePaymentPixDTO->toArray());
        $fakePayment->id = 1;

        $paymentRepository->shouldReceive("processPixPayment")
            ->once()
            ->andReturn($fakePayment);

        $paymentRepository->shouldReceive("update")
            ->once()
            ->andReturn($fakePayment);

        $asaasServiceMock->shouldReceive('createPayment')
            ->once()
            ->andReturn(['gateway_payment_id' => '1234']);

        $asaasServiceMock->shouldReceive('getPaymentDetails')
            ->once()
            ->andReturn(['qrcode' => '1234', 'pix_key' => 'pix_key_data', 'due_date' => '2024-01-15']);

        $paymentGatewayFactory->shouldReceive('handle')
            ->once()
            ->andReturn($asaasServiceMock);


        $customerService = new CustomerService($customerRepository, $customerGateway);
        $paymentService = new PaymentService($paymentRepository, $customerService, $paymentGatewayFactory);

        $result = $paymentService->processPixPayment($fakePaymentPixDTO);

        $this->assertEquals($fakePayment, $result);
        $this->assertInstanceOf(Payment::class, $result);
    }

    public function testErrorInReturnIdPaymentByPix(): void
    {
        $this->expectException(Exception::class);

        $paymentRepository = Mockery::mock(PaymentRepository::class);
        $customerRepositoryMock = Mockery::mock(CustomerRepository::class);
        $customerGateway = Mockery::mock(CustomerGatewayInterface::class);
        $asaasServiceMock = Mockery::mock(AsaasPaymentPixService::class);
        $paymentGatewayFactory = Mockery::mock(PaymentGatewayFactory::class);

        $customerCreated = Customer::factory()->create();

        $customerRepositoryMock->shouldReceive('findById')
            ->once()
            ->andReturn($customerCreated);

        $fakePaymentPixDTO = new PaymentPixDTO(
            customer_id: 1,
            amount: 4540,
            due_date: '2024-01-15',
            provider: 'asaas',
            method: PaymentMethod::PIX->value
        );

        $fakePayment = new Payment($fakePaymentPixDTO->toArray());

        $paymentRepository->shouldReceive("processPixPayment")
            ->once()
            ->andReturn($fakePayment);

        $asaasServiceMock->shouldReceive('createPayment')
            ->once()
            ->andThrow(Exception::class);

        $paymentGatewayFactory->shouldReceive('handle')
            ->once()
            ->andReturn($asaasServiceMock);


        $customerService = new CustomerService($customerRepositoryMock, $customerGateway);
        $paymentService = new PaymentService($paymentRepository, $customerService, $paymentGatewayFactory);

        $paymentService->processPixPayment($fakePaymentPixDTO);
    }

    public function testClientNotFoundInPaymentPorcess(): void
    {
        $customerServiceMock = Mockery::mock(CustomerService::class);
        $paymentRepositoryMock = Mockery::mock(PaymentRepository::class);
        $paymentGatewayFactory = new PaymentGatewayFactory();

        $customerServiceMock->shouldReceive('findById')
            ->with('id_inexistente')
            ->once()
            ->andThrow(new ModelNotFoundException());

        $pixDTO = new PaymentPixDTO(
            customer_id: 'id_inexistente',
            amount: 4540,
            due_date: '2024-01-15',
            provider: 'asaas',
            method: PaymentMethod::PIX->value
        );

        $paymentService = new PaymentService($paymentRepositoryMock, $customerServiceMock, $paymentGatewayFactory);

        $this->expectException(ModelNotFoundException::class);
        $paymentService->processPixPayment($pixDTO);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
