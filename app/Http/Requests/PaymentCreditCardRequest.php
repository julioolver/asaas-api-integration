<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentCreditCardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric',
            'customer_id' => 'required|exists:customers,id',
            'due_date' => 'required|string|date',
            'method' => 'required|string',
            'credit_card.card_number' => 'required|max:16',
            'credit_card.card_holder_name' => 'required|max:255',
            'credit_card.expiry_month' => 'required|digits:2',
            'credit_card.expiry_year' => 'required|digits:4',
            'credit_card.cvv' => 'required|numeric|digits:3',
            'holder_info.name' => 'required|string',
            'holder_info.email' => 'required|string',
            'holder_info.document_number' => 'required|string',
            'holder_info.postal_code' => 'required|string',
            'holder_info.address_number' => 'required|string',
            'holder_info.phone' => 'required|string',
        ];
    }
}
