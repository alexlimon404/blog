<?php

use Carbon\Carbon;

if (! function_exists('to_bool')) {
    function to_bool(bool|string|int $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            if ($value === 'true') {
                return true;
            }

            if ($value === 'false') {
                return false;
            }
        }

        return (bool) $value;
    }
}

if (! function_exists('cast_value')) {
    function cast_value(string $cast, mixed $value = null): mixed
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        return match ($cast) {
            'bool', 'boolean' => to_bool($value),
            'date', 'datetime' => new Carbon($value),
            'array' => is_string($value) ? json_decode($value, true) : (array) $value,
            'int', 'integer' => (int) $value,
            'float' => (float) $value,
            default => $value,
        };
    }
}