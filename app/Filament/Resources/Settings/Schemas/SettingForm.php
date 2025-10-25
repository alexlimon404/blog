<?php

namespace App\Filament\Resources\Settings\Schemas;

use App\Models\Setting;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->schema([
                        TextInput::make('key')
                            ->inlineLabel()
                            ->required()
                            ->unique(ignoreRecord: true),

                        Select::make('type')
                            ->inlineLabel()
                            ->options(
                                Setting::getFields()->pluck('name', 'id')->toArray()
                            )
                            ->default(Setting::FIELD_TEXT)
                            ->required(),

                        Textarea::make('value')
                            ->nullable()
                            ->autosize(),

                        Toggle::make('active')
                            ->default(true),
                    ])->columns(1),

                Section::make('')
                    ->schema([
                        Textarea::make('description')
                            ->nullable()
                            ->autosize(),
                    ])->columns(1),
            ]);
    }
}
