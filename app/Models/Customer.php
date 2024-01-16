<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="customerBase",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", example="john.doe@example.com"),
 *     @OA\Property(property="phone", type="string", example="9999999999"),
 *     @OA\Property(property="gateway_customer_id", type="string", example="cus_99999999"),
 * )
 * @OA\Schema(
 *    schema="customerObject",
 *    type="object",
 *    @OA\Property(
 *        property="data",
 *        ref="#/components/schemas/customerBase"
 *    )
 * )
 *
 * @OA\Schema(
 *    schema="customerArray",
 *    type="object",
 *    @OA\Property(
 *        property="data",
 *        type="array",
 *        @OA\Items(ref="#/components/schemas/customerBase")
 *    )
 * )
 */
class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "email",
        "document_number",
        "phone",
        "gateway_customer_id"
    ];
}
