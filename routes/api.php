<?php

use App\Http\Controllers\Api\CustomerController;
use Illuminate\Support\Facades\Route;

Route::get('customers/by-email', [CustomerController::class, 'showByEmail']);
Route::resource('customers', CustomerController::class);
