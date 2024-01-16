<?php

namespace App\Integrations\Payments\Asaas;

use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AsaasHttpClient
{
    protected PendingRequest $client;
    protected string $baseURI;

    public function __construct()
    {
        $apiKey = config("integrations.asaas.api_key");
        $this->baseURI = config("integrations.asaas.base_uri");

        $this->client = Http::withHeaders([
            'accept' => 'application/json',
            'Content-Type' => 'application/json',
            'access_token' => $apiKey,
        ]);
    }

    public function get(string $uri, array $options = [])
    {
        try {
            $response = $this->client->get("{$this->baseURI}/{$uri}", $uri, $options);

            return $this->responseTratament($response);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function post($uri, array $data)
    {
        $response = $this->client->post("{$this->baseURI}/{$uri}", $data);

        return $this->responseTratament($response);
    }
    private function responseTratament(Response $response)
    {
        try {
            if ($response->successful()) {
                return $response->json();
            }

            $errorDetails = $response->json();

            if (isset($errorDetails['errors'])) {
                $errorDetails = $errorDetails['errors'];
            }

            throw new Exception(
                'Erro na comunicação com a API externa: ' . json_encode($errorDetails, JSON_UNESCAPED_UNICODE),
                $response->status()
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}
