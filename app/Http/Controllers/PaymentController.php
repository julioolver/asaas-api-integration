<?php

namespace App\Http\Controllers;

use App\DTOs\Payment\PaymentPixDTO;
use App\Factory\PaymentGatewayFactory;
use App\Http\Requests\PaymentPixRequest;
use App\Integrations\Payments\Asaas\AsaasPaymentPixService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $service)
    {
    }

    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function processPixPayment(PaymentPixRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['provider'] = 'asaas';

        $paymentPixDTO = new PaymentPixDTO(...$validatedData);
        $payment = $this->service->processPixPayment($paymentPixDTO);

        return response()->json($payment);
    }
}
