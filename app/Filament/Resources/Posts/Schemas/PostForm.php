<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Services\AiGenerator\AiGenerator;
use App\Services\AiGenerator\AiGeneratorEnum;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Post Information')
                    ->schema([
                        DateTimePicker::make('published_at')
                            ->inlineLabel()
                            ->label('Published At')
                            ->default(null),

                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $context, $state, callable $set) => $context === 'create' ? $set('slug', str($state)->slug()) : null),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Textarea::make('excerpt')
                            ->autosize()
                            ->maxLength(500),

                        Textarea::make('description')
                            ->autosize()
                            ->maxLength(500),
                    ])->columns(1),

                Section::make('Relationships & Settings')
                    ->schema([
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),

                        Select::make('author_id')
                            ->label('Author')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload(),

                        Select::make('base_prompt_id')
                            ->label('BasePrompt')
                            ->required()
                            ->relationship('basePrompt', 'name')
                            ->searchable()
                            ->preload(),

                        Select::make('tags')
                            ->label('Tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique('tags', 'slug'),
                                \Filament\Forms\Components\ColorPicker::make('color')
                                    ->default('#6B7280'),
                            ]),
                        Select::make('driver')
                            ->options(AiGeneratorEnum::toSelectArray())
                            ->default(AiGeneratorEnum::TEST->value)
                            ->live(),

                        Select::make('model')
                            ->options(function (callable $get) {
                                $driver = $get('driver');
                                return $driver ? AiGenerator::driver($driver)->getModels() : '';
                            }),
                    ])
                    ->columns(2),

                RichEditor::make('content')->columnSpanFull(),
            ]);
    }
}
