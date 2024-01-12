<?php

namespace App\Http\Controllers;

use App\DTOs\Payment\PaymentPixDTO;
use App\Http\Requests\PaymentPixRequest;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $service)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function processPixPayment(PaymentPixRequest $request)
    {
        $paymentPixDTO = new PaymentPixDTO(...$request->validated());

        $payment = $this->service->processPixPayment($paymentPixDTO);

    }
}
