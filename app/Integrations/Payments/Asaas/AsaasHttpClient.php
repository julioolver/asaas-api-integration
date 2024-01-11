<?php
namespace App\Integrations\Payments\Asaas;

use Illuminate\Http\Client\PendingRequest;
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
        return $this->client->get("{$this->baseURI}/{$uri}", $uri, $options);
    }

    public function post($uri, array $data): array
    {
        return $this->client->post("{$this->baseURI}/{$uri}", $data)->json();
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
