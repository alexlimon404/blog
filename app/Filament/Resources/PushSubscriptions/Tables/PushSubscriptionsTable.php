<?php

namespace App\Filament\Resources\PushSubscriptions\Tables;

use App\Services\FirebaseService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PushSubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) > 50) {
                            return $state;
                        }
                        return null;
                    }),
                TextColumn::make('ip_address')
                    ->label('IP Address'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('send_notification')
                    ->label('Send')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->schema([
                        TextInput::make('title')
                            ->label('Notification Title')
                            ->required()
                            ->default('Test Notification'),
                        Textarea::make('body')
                            ->label('Notification Message')
                            ->required()
                            ->default('This is a test push notification'),
                    ])
                    ->action(function (array $data, $record) {
                        try {
                            FirebaseService::sendPushNotification(
                                $record->token,
                                $data['title'],
                                $data['body']
                            );
                            Notification::make()
                                ->title('Notification Sent Successfully')
                                ->body("Push notification sent to subscription ID: $record->id")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Failed to Send Notification')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                            info($e->getMessage());
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
