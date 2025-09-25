<?php

namespace App\Filament\Resources\Visits\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VisitInfoList
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Информация о посещении')->inlineLabel()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Время посещения')
                            ->dateTime('d.m.Y H:i:s'),

                        TextEntry::make('url')
                            ->label('URL')
                            ->copyable()
                            ->columnSpanFull(),

                        TextEntry::make('page_title')
                            ->label('Заголовок страницы')
                            ->placeholder('Не указан'),

                        TextEntry::make('ip_address')
                            ->label('IP адрес'),

                        TextEntry::make('session_id')
                            ->label('ID сессии')
                            ->copyable(),
                    ])
                    ->columns(1),

                Section::make('Дополнительная информация')
                    ->schema([
                        TextEntry::make('referrer')
                            ->label('Источник (Referrer)')
                            ->placeholder('Прямой переход')
                            ->copyable(),

                        TextEntry::make('user_agent')
                            ->label('User Agent'),

                        TextEntry::make('metadata')
                            ->label('Метаданные')
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state)
                            ->copyable(),
                    ]),
            ]);
    }
}