<?php

namespace App\Integrations\Payments\Asaas;

use Exception;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
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
        return $this->client->get("{$this->baseURI}/{$uri}", $uri, $options)->json();
    }

    public function post($uri, array $data)
    {
        try {
            $response = $this->client->post("{$this->baseURI}/{$uri}", $data);

            if ($response->failed()) {
                $errorDetails = $response->json();

                throw new Exception(
                    'Erro na comunicação com a API externa: ' . json_encode($errorDetails),
                    $response->status()
                );
            }

            return $response->json();
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function put($uri, array $data)
    {
        return $this->client->put("{$this->baseURI}/{$uri}", ['json' => $data]);
    }

    public function delete($uri, array $data)
    {
        return $this->client->delete("{$this->baseURI}/{$uri}", ['json' => $data]);
    }
}
