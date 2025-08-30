<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use App\Models\Category;
use App\Models\Author;
use App\Models\Tag;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Post Information')
                    ->schema([
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
                            ->rows(3)
                            ->maxLength(500)
                            ->hint('Brief description of the post'),
                        
                        Textarea::make('content')
                            ->required()
                            ->rows(10)
                            ->hint('Main content of the post'),
                    ])
                    ->columns(1),
                
                Section::make('Relationships & Settings')
                    ->schema([
                        Select::make('category_id')
                            ->label('Category')
                            ->required()
                            ->options(Category::pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                        
                        Select::make('author_id')
                            ->label('Author')
                            ->required()
                            ->options(Author::pluck('name', 'id'))
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
                        
                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(false)
                            ->live(),
                        
                        DateTimePicker::make('published_at')
                            ->label('Published At')
                            ->visible(fn ($get) => $get('is_published'))
                            ->default(now()),
                    ])
                    ->columns(2)
            ]);
    }
}
