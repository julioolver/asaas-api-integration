<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('customers/by-email', [CustomerController::class, 'showByEmail']);
Route::post('customers/integrate', [CustomerController::class, 'storeAndIntegrate']);
Route::resource('customers', CustomerController::class);


Route::group(['prefix' => 'payments'], function () {
    Route::post('pix', [PaymentController::class, 'processPixPayment']);
});

