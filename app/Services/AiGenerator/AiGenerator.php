<?php

namespace App\Services\AiGenerator;

use App\Services\AiGenerator\Drivers\Driver;
use InvalidArgumentException;

class AiGenerator
{
    public function __construct()
    {
    }

    public static function driver(string $driver): Driver
    {
        $class = __NAMESPACE__ . '\\Drivers\\' . str_replace('_', '', ucwords($driver, '_'));

        if (class_exists($class)) {
            return new $class($driver);
        }

        throw new InvalidArgumentException("AiGenerator driver [{$driver}] is not supported.");
    }
}