<?php

namespace App\Filament\Resources\PushSubscriptions\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PushSubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('token')
                    ->label('Firebase Token')
                    ->required()
                    ->maxLength(255),
                TextInput::make('endpoint')
                    ->label('Endpoint')
                    ->maxLength(255),
                Textarea::make('keys')
                    ->label('Keys (JSON)')
                    ->rows(3)
                    ->helperText('JSON object containing subscription keys'),
                TextInput::make('user_agent')
                    ->label('User Agent')
                    ->maxLength(255),
                TextInput::make('ip_address')
                    ->label('IP Address')
                    ->maxLength(45),
            ]);
    }
}
