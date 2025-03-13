<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class TourCrmApi
{
    protected string $apiKey = 'crm_api_token';
    protected int $ttl = 60 * 60 * 24;
    protected PendingRequest $client;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $baseUrl = env('CRM_API_URL');
        if (!$baseUrl) {
            throw new Exception('CRM_API_URL is not set');
        }

        $this->client = Http::withHeaders(['Accept' => 'application/json'])
            ->baseUrl(env('CRM_API_URL'));
    }

    /**
     * @throws Exception
     */
    public function login(): void
    {
        $token = Cache::get($this->apiKey);
        if ($token) {
            $this->client->withToken($token);
            return;
        }

        try {
            $response = $this->client->post('/api/login', [
                'email' => env('CRM_API_EMAIL'),
                'password' => env('CRM_API_PASSWORD'),
            ]);
            $this->guardStatus($response);

            $token = Arr::get($response->json(), 'token');
            if (!$token) {
                throw new Exception('Failed to login to CRM API');
            }

            $this->client->withToken($token);
            Cache::put($this->apiKey, $token, $this->ttl);
        } catch (Throwable $e) {
            throw new Exception('Failed to login to CRM API');
        }
    }

    /**
     * @throws Exception
     */
    public function getHotels(): array
    {
        $response = $this->client->get('/api/hotels');
        $this->guardStatus($response);

        return Arr::get($response->json(), 'data');
    }

    /**
     * @throws Exception
     */
    public function getCountries(): array
    {
        $response = $this->client->get('/api/countries');
        $this->guardStatus($response);

        return Arr::get($response->json(), 'data');
    }

    /**
     * @throws Exception
     */
    public function getCities(): array
    {
        $response = $this->client->get('/api/cities');
        $this->guardStatus($response);

        return Arr::get($response->json(), 'data');
    }

    /**
     * @throws Exception
     */
    protected function guardStatus(Response $response): void
    {
        if ($response->status() !== 200) {
            throw new Exception($response->json('message'));
        }
    }
}
