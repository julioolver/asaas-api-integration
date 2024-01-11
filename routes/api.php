<?php

use App\Http\Controllers\Api\CustomerController;
use Illuminate\Support\Facades\Route;

Route::resource('customers', CustomerController::class);
Route::get('customers/by-email', [CustomerController::class, 'showByEmail']);
