<?php

namespace App\Filament\Resources\Posts\Tables;

use App\Actions\Post\GetToGenerateAction;
use App\Actions\Post\RegenerateAction;
use App\Actions\Post\SendToGenerateAction;
use App\Models\Post;
use App\Services\AiGenerator\AiGenerator;
use App\Services\AiGenerator\AiGeneratorEnum;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Category;
use App\Models\Author;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),

                TextColumn::make('uuid')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('published_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('slug')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                TextColumn::make('title')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('basePrompt.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('category.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('author.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('driver')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge()->color('success'),
                TextColumn::make('model')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge()->color('success'),
                IconColumn::make('isPublished')->boolean()
                    ->state(function (Post $record) {
                        return $record->isPublished();
                    }),
                TextColumn::make('today_visits_count')
                    ->label('Today visits')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable()
                    ->color('info'),
                TextColumn::make('visits_count')
                    ->label('Visits')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->counts('visits')
                    ->sortable()
                    ->color('success'),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(Category::pluck('name', 'id')),

                SelectFilter::make('author_id')
                    ->label('Author')
                    ->options(Author::pluck('name', 'id')),

                Filter::make('published')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('published_at')->where('published_at', '<=', now()))
                    ->label('Published Only'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                Action::make('visit')
                    ->label('')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Post $record): string => route('blog.post', $record->slug))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkAction::make('send_to_generate')
                    ->color('warning')
                    ->action(function (array $data, $records) {
                        foreach ($records as $record) {
                            SendToGenerateAction::run($record, $data);
                        }
                    }),
                BulkAction::make('get_from_generate')
                    ->color('success')
                    ->action(function (array $data, $records) {
                        foreach ($records as $record) {
                            GetToGenerateAction::run($record, $data);
                        }
                    }),
                BulkAction::make('regenerate')
                    ->color('warning')
                    ->action(function (array $data, $records) {
                        foreach ($records as $record) {
                            RegenerateAction::run($record, $data);
                        }
                    }),
                BulkAction::make('update_driver_and_model')
                    ->label('Update Driver')
                    ->color('info')
                    ->schema([
                        Select::make('driver')
                            ->options(AiGeneratorEnum::toSelectArray())
                            ->default(AiGeneratorEnum::TEST->value)
                            ->live(),
                        Select::make('model')
                            ->options(function (callable $get) {
                                $driver = $get('driver');
                                return $driver ? AiGenerator::driver($driver)->getModels() : [];
                            }),
                    ])
                    ->action(function (array $data, $records) {
                        foreach ($records as $record) {
                            $record->update([
                                'driver' => $data['driver'],
                                'model' => $data['model'],
                            ]);
                        }
                    }),
                DeleteBulkAction::make()->label(''),
            ]);
    }
}
