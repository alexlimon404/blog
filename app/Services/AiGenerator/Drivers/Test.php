<?php

namespace App\Services\AiGenerator\Drivers;

class Test implements Driver
{
    public function __construct()
    {
    }

    public function getText(array $params): array
    {
        return [
            'uuid' => $params['uuid'],
            'text_response' => 'asdasd',
        ];
    }

    public function createText(array $params): array
    {
        return [
            'uuid' => $params['uuid'],
        ];
    }

    public function getModels(array $params = []): array
    {
        return [
            'test_model' => 'Test Model',
        ];
    }

    public function regenerateText(string $uuid, array $params): array
    {
        return [
            'uuid' => $params['uuid'],
        ];
    }
}
