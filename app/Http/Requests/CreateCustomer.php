<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCustomer extends FormRequest
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
     * $id = $this->id;
        // "unique:companies,youtube,${uuid},uuid"
        //  "unique:customer,email,{$id},id"
     */
    public function rules(): array
    {

        return [
            "name" => "required|string",
            "email" => "required|email",
            "phone" => "string",
            "document_number" => "required|cpf",
        ];
    }
}
