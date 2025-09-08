<?php

namespace App\Filament\Resources\BasePrompts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class BasePromptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('prompt')
                            ->autosize()
                            ->required(),

                        Toggle::make('active')
                            ->default(true),
                    ])
                    ->columns(1)
            ]);
    }
}
