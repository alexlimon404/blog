<?php

namespace App\Services\AiGenerator\Drivers;

interface Driver
{
    public function getText(array $params): array;

    public function createText(array $params): array;

    public function getModels(array $params = []): array;
}
