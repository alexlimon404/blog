<?php

namespace App\Services\AiGenerator;

enum AiGeneratorEnum: string
{
    case TEXT_GENERATOR_AI = 'text_generator_ai';
    case TEST = 'test';

    public function label(): string
    {
        return match($this) {
            self::TEXT_GENERATOR_AI => 'Text Generator AI',
            self::TEST => 'Test',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function toSelectArray(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }
}