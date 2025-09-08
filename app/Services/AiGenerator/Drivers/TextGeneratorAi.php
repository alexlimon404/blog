<?php

namespace App\Services\AiGenerator\Drivers;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class TextGeneratorAi implements Driver
{
    protected array $service;

    public function __construct()
    {
        $this->service = config('services.ai_chat');
    }

    public function getText(array $params): array
    {
        $response = $this->client()->get("prompt-requests/{$params['uuid']}");

        if ($response->status() === 200) {
            return $response->json();
        }

        $response->throw();
    }

    public function createText(array $params): array
    {
        $response = $this->client()->post("prompt-requests", $params);

        if ($response->status() === 201) {
            return $response->json();
        }

        $response->throw();
    }

    public function regenerateText(string $uuid, array $params): array
    {
        $response = $this->client()->put("prompt-requests/$uuid/regenerate", $params);

        if ($response->status() === 200) {
            return $response->json();
        }

        $response->throw();
    }

    public function getModels(array $params = []): array
    {
        try {
            $response = $this->client()->get('models', $params);

            if ($response->status() === 200) {

                return collect($response->json()['data'])->pluck('model_name', 'model_name')->toArray();
            }
        } catch (\Exception $exception) {
            return ['not_work' => 'Not work'];
        }
    }

    private function client(): PendingRequest
    {
        return Http::acceptJson()
            ->withHeaders([
                'service-uuid' => $this->service['uuid'],
                'service-url' => config('app.url'),
            ])
            ->withoutVerifying()
            ->timeout(3)
            ->baseUrl("{$this->service['url']}/api");
    }
}