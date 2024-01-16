<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Payment\CreditCardDTO;
use App\DTOs\Payment\CreditCardHolderInfoDTO;
use App\DTOs\Payment\PaymentByBilletDTO;
use App\DTOs\Payment\PaymentCreditCardDTO;
use App\Http\Controllers\Controller;
use App\DTOs\Payment\PaymentPixDTO;
use App\Http\Requests\PaymentCreditCardRequest;
use App\Http\Requests\PaymentPixRequest;
use App\Http\Resources\PaymentByPixResource;
use App\Services\PaymentService;
use Illuminate\Http\Response;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $service)
    {
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

    public function processPaymentByCreditCard(PaymentCreditCardRequest $request)
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
                customer_id: $validatedData['customer_id'],
                amount: $validatedData['amount'],
                due_date: $validatedData['due_date'],
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
