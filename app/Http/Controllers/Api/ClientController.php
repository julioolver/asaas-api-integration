<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Client\ClientDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateClient;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;

class ClientController extends Controller
{

    public function __construct(protected ClientService $clientService)
    {
    }

    public function store(CreateClient $request)
    {
        try {
            $clientDTO = new ClientDTO(...$request->validated());

            $client = $this->clientService->create($clientDTO);

            return new ClientResource($client);
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

    public function show(Client $client)
    {
        //TODO: find client by e-mail
    }
}
