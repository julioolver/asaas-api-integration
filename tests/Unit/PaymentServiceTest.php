<?php

namespace Tests\Unit;

use App\DTOs\Payment\PaymentPixDTO;
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
            ->with($customerCreated->id)
            ->andReturn($customerCreated);

        $payloadPix = new PaymentPixDTO(
            customer_id: 1,
            amount: 4540,
            due_date: '2024-01-15',
            provider: 'asaas',

        );

        $responsePix = new Payment($payloadPix->toArray());

        $paymentRepository->shouldReceive("processPixPayment")
            ->once()
            ->with([
                'customer_id' => '1',
                'amount' => 4540,
                'due_date' => '2024-01-15',
                'provider' => 'asaas',
                'method' => 'pix',
                'status' => 'pending',
            ])
            ->andReturn($responsePix);

        $asaasServiceMock->shouldReceive('createPayment')
            ->once()
            ->with([
                'customer_id' => '1', // Ou o valor apropriado
                'amount' => 4540,
                'due_date' => '2024-01-15',
                'provider' => 'asaas',
                'method' => 'pix',
                'status' => 'pending',
                'gateway_id' => $customerCreated->payment_gateway_id
            ])
            ->andReturn(['id' => '1234']);

        // $asaasServiceMock->shouldReceive('post')
        //     ->once()
        //     ->with('payments', [
        //         'customer' => $customerCreated->payment_gateway_id,
        //         'amount' => 4540,
        //         'due_date' => '2024-01-15',
        //         'provider' => 'asaas',
        //         'method' => 'pix',
        //         'status' => 'pending',
        //     ])
        //     ->andReturn(['id' => '1234']);

        $paymentGatewayFactory->shouldReceive('handle')
            ->with('asaas', 'pix')
            ->once()
            ->andReturn($asaasServiceMock);


        $customerService = new CustomerService($customerRepository, $customerGateway);
        $paymentService = new PaymentService($paymentRepository, $customerService, $paymentGatewayFactory);

        $result = $paymentService->processPixPayment($payloadPix);

        $this->assertEquals($responsePix, $result);
    }

    public function testErrorInReturnIdPaymentByPix(): void
    {
        $this->expectException(Exception::class);
        //$this->expectExceptionMessage('Sem ID de integração, tente novamente mais tarde');

        $paymentRepository = Mockery::mock(PaymentRepository::class);
        $customerRepository = Mockery::mock(CustomerRepository::class);
        $customerGateway = Mockery::mock(CustomerGatewayInterface::class);
        $asaasServiceMock = Mockery::mock(AsaasPaymentPixService::class);
        $paymentGatewayFactory = Mockery::mock(PaymentGatewayFactory::class);

        $customerCreated = Customer::factory()->create();

        $customerRepository->shouldReceive('findById')
            ->once()
            ->with('1')
            ->andReturn($customerCreated);

        $payloadPix = new PaymentPixDTO(
            customer_id: 1,
            amount: 4540,
            due_date: '2024-01-15',
            provider: 'asaas',

        );

        $responsePix = new Payment($payloadPix->toArray());

        $paymentRepository->shouldReceive("processPixPayment")
            ->once()
            ->with([
                'customer_id' => 1,
                'amount' => 4540,
                'due_date' => '2024-01-15',
                'provider' => 'asaas',
                'method' => 'pix',
                'status' => 'pending',
            ])
            ->andReturn($responsePix);

        $asaasServiceMock->shouldReceive('createPayment')
            ->once()
            ->with([
                'customer_id' => 1,
                'amount' => 4540,
                'due_date' => '2024-01-15',
                'provider' => 'asaas',
                'method' => 'pix',
                'status' => 'pending',
                'gateway_id' => $customerCreated->payment_gateway_id
            ])
            ->andThrow(Exception::class);

        $paymentGatewayFactory->shouldReceive('handle')
            ->with('asaas', 'pix')
            ->once()
            ->andReturn($asaasServiceMock);


        $customerService = new CustomerService($customerRepository, $customerGateway);
        $paymentService = new PaymentService($paymentRepository, $customerService, $paymentGatewayFactory);

        $paymentService->processPixPayment($payloadPix);
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
        );

        $paymentService = new PaymentService($paymentRepositoryMock, $customerServiceMock, $paymentGatewayFactory);

        $this->expectException(ModelNotFoundException::class);
        $paymentService->processPixPayment($pixDTO);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
