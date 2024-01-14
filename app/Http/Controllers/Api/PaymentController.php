<?php

namespace App\Http\Controllers\Api;

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
        dd('aq');
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
}
