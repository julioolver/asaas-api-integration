<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Payment\CreditCardDTO;
use App\DTOs\Payment\CreditCardHolderInfoDTO;
use App\DTOs\Payment\PaymentByBilletDTO;
use App\DTOs\Payment\PaymentCreditCardDTO;
use App\Http\Controllers\Controller;
use App\DTOs\Payment\PaymentPixDTO;
use App\Http\Requests\PaymentPixRequest;
use App\Http\Resources\PaymentByPixResource;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $service)
    {
    }

    public function index()
    {
        //TODO: create index
    }

    /**
     * Store a newly created resource in storage.
     */
    public function processPixPayment(PaymentPixRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['provider'] = 'asaas';

            $paymentPixDTO = new PaymentPixDTO(...$validatedData);

            $payment = $this->service->processPixPayment($paymentPixDTO);

            return (new PaymentByPixResource($payment))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function processPaymentByBillet(PaymentPixRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['provider'] = 'asaas';

            $paymentPixDTO = new PaymentByBilletDTO(...$validatedData);

            $payment = $this->service->processBilletPayment($paymentPixDTO);

            return (new PaymentByPixResource($payment))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function processPaymentByCreditCard(PaymentPixRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['provider'] = 'asaas';

            $cardHolderInfoDTO = new CreditCardHolderInfoDTO(
                ...$validatedData['holder_info']
            );

            $creditCardDTO = new CreditCardDTO(
                ...$validatedData['credit_card']
            );

            $paymentDTO = new PaymentCreditCardDTO(
                amount: $validatedData['amount'],
                customer_id: $validatedData['customer_id'],
                due_date: $validatedData['customer_id'],
                method: $validatedData['method'],
                creditCard: $creditCardDTO,
                holderInfo: $cardHolderInfoDTO
            );
            $payment = $this->service->processCreditCardPayment($paymentDTO);

            return (new PaymentByPixResource($payment))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
