<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('customers/by-email', [CustomerController::class, 'showByEmail']);
Route::resource('customers', CustomerController::class);

Route::group(['prefix' => 'payments'], function () {
    Route::post('pix', [PaymentController::class, 'processPixPayment']);
});
