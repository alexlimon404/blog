<?php

namespace App\Filament\Resources\Visits\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VisitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Информация о посещении')
                    ->schema([
                        TextInput::make('url')
                            ->label('URL')
                            ->required()
                            ->disabled()
                            ->columnSpanFull(),

                        TextInput::make('page_title')
                            ->label('Заголовок страницы')
                            ->disabled(),

                        TextInput::make('ip_address')
                            ->label('IP адрес')
                            ->disabled(),

                        TextInput::make('session_id')
                            ->label('ID сессии')
                            ->disabled(),

                        DateTimePicker::make('created_at')
                            ->label('Время посещения')
                            ->disabled(),
                    ])
                    ->columns(2),

                Section::make('Дополнительная информация')
                    ->schema([
                        TextInput::make('referrer')
                            ->label('Источник (Referrer)')
                            ->disabled()
                            ->columnSpanFull(),

                        Textarea::make('user_agent')
                            ->label('User Agent')
                            ->disabled()
                            ->rows(3)
                            ->columnSpanFull(),

                        Textarea::make('metadata')
                            ->label('Метаданные')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state)
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}