<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'amount' => $this->amount,
            'method' => $this->method,
            'pix_details' => $this->pix_data,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'bank_url' => $this->bank_url,
            'invoice_url' => $this->invoice_url,
            'bar_code' => $this->bar_code,
        ];
    }
}
