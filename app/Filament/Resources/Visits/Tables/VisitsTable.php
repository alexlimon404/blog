<?php

namespace App\Filament\Resources\Visits\Tables;

use App\Models\Visit;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;

class VisitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime('d.m.Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('url')
                    ->formatStateUsing(fn (string $state): string => parse_url($state, PHP_URL_PATH) ?: $state)
                    ->searchable(isIndividual: true),

                TextColumn::make('post.title')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->color('warning'),

                TextColumn::make('page_title')
                    ->label('Заголовок страницы')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ip_address')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('IP адрес')
                    ->searchable(),

                TextColumn::make('referrer')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(30)
                    ->placeholder('Прямой переход'),

                TextColumn::make('user_agent')
                    ->limit(60)
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('bot_name')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')
                            ->label('От'),
                        DatePicker::make('created_until')
                            ->label('До'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                TernaryFilter::make('is_bot')
                    ->label('Боты')
                    ->placeholder('Все')
                    ->trueLabel('Только боты')
                    ->falseLabel('Без ботов')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('bot_name'),
                        false: fn (Builder $query) => $query->whereNull('bot_name'),
                    ),
                SelectFilter::make('bot_name')
                    ->label('Тип бота')
                    ->options(fn () => Visit::botNames())
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkAction::make('Detect bot')
                    ->color('success')
                    ->action(function (array $data, $records) {
                        foreach ($records as $record) {
                            $botInfo = Visit::detectBot($record->user_agent ?? '');
                            $record->update([
                                'bot_name' => $botInfo['bot_name'],
                            ]);
                        }
                    }),
                DeleteBulkAction::make(),
            ]);
    }
}
