<?php

namespace App\Filament\Resources\Posts\Tables;

use Database\Factories\PostFactory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Category;
use App\Models\Author;
use Filament\Notifications\Notification;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('author.name')
                    ->label('Author')
                    ->sortable()
                    ->searchable(),

                IconColumn::make('is_published')
                    ->label('Published')
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(Category::pluck('name', 'id')),

                SelectFilter::make('author_id')
                    ->label('Author')
                    ->options(Author::pluck('name', 'id')),

                Filter::make('published')
                    ->query(fn (Builder $query): Builder => $query->where('is_published', true))
                    ->label('Published Only'),

                Filter::make('draft')
                    ->query(fn (Builder $query): Builder => $query->where('is_published', false))
                    ->label('Drafts Only'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                Action::make('generate_fake_posts')
                    ->label('Generate Fake Posts')
                    ->icon('heroicon-o-sparkles')
                    ->color('success')
                    ->schema([
                        TextInput::make('count')
                            ->label('Number of posts')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1)
                            ->maxValue(10)
                    ])
                    ->action(function (array $data) {
                        $count = $data['count'];
                        PostFactory::new()->count($count)->create([
                            'category_id' => Category::query()->get()->random()->id,
                            'author_id' => Author::query()->get()->random()->id,
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Success')
                            ->body("Generated {$count} fake posts successfully!")
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Generate Fake Posts')
                    ->modalSubmitActionLabel('Generate'),

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
