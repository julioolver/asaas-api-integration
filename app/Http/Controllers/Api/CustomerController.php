<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Customer\CustomerDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCustomer;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    public function __construct(protected CustomerService $customerService)
    {
    }

    public function store(CreateCustomer $request)
    {
        try {
            $customerDTO = new CustomerDTO(...$request->validated());

            $customer = $this->customerService->create($customerDTO);

            return (new CustomerResource($customer))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);

        } catch (QueryException $e) {
            return response()->json([
                'error' => 'Erro na solicitação de dados',
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Erro interno de servidor',
                'message' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showByEmail(Request $request)
    {
        try {
            $email = $request->query('email');

            $customer = $this->customerService->findByEmail($email);

            return (new CustomerResource($customer))
                ->response()
                ->setStatusCode(Response::HTTP_OK);

        } catch (QueryException $e) {
            return response()->json([
                'error' => 'Erro na solicitação de dados',
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro interno de servidor',
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }
}
