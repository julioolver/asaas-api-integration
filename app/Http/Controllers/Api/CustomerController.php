<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Customer\CustomerDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCustomer;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;
use Illuminate\Http\Request;


class CustomerController extends Controller
{

    public function __construct(protected CustomerService $customerService)
    {
    }

    /**
     * @OA\Post(
     *     path="/api/customers",
     *     summary="Cria um cliente no sistema",
     *     description="Cria um cliente na base de dados do sistema",
     *     tags={"customers"},
     *     @OA\Response(
     *         response=201,
     *         description="Retorna o customer criado",
     *         @OA\JsonContent(ref="#/components/schemas/customerObject"),
     *     )
     * )
     */
    public function store(CreateCustomer $request)
    {
        try {
            $customerDTO = new CustomerDTO(...$request->validated());

            $customer = $this->customerService->createCustomer($customerDTO);

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

    /**
     * @OA\Post(
     *     path="/api/customers/integrate",
     *     summary="Cria um cliente com integração externa",
     *     description="Cria um cliente e realiza integração com API de pagamentos",
     *     tags={"customers"},
     *     @OA\Response(
     *         response=201,
     *         description="Retorna o customer criado",
     *         @OA\JsonContent(ref="#/components/schemas/customerObject"),
     *     )
     * )
     */
    public function storeAndIntegrate(CreateCustomer $request)
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

    /**
     * @OA\Get(
     *     path="/api/customers/by-email",
     *     summary="Customers",
     *     description="Get a customer by e-mail",
     *     tags={"customers"},
     *     @OA\Parameter(
     *         name="email",
     *         description="Filtrar um customer por e-mail",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retorna um único customer",
     *         @OA\JsonContent(ref="#/components/schemas/customerObject"),
     *     )
     * )
     */
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
        } catch (ModelNotFoundException $modelException) {
            return response()->json([
                'error' => 'Dados não encontrados',
                'message' => $modelException->getMessage()
            ], Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro interno de servidor',
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }
}
