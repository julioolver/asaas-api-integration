<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="vega checkout test API",
 *     version="1.0.0",
 *     description="Esse é um desafio/teste PHP com foco em Laravel",
 *     @OA\Contact(
 *         email="juliocesar.olver@gmail.com"
 *     )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
