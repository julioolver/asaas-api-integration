<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'amount',
        'method',
        'status',
        'gateway_payment_id',
        'bank_url',
        'invoice_url',
        'pix_data',
        'card_authorization_number',
        'nosso_numero',
        'bar_code',
        'identification_field',
        'due_date'
    ];

    protected $casts = [
        'method' => PaymentMethod::class,
        'status' => PaymentStatus::class,
    ];

    protected function pixData(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? json_decode($value, true) : null,
        );
    }
}
