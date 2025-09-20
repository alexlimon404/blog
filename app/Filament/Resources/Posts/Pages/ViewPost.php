<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Actions\Post\GetToGenerateAction;
use App\Actions\Post\RegenerateAction;
use App\Actions\Post\SendToGenerateAction;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Traits\HasPreviewNext;
use App\Models\Post;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    use HasPreviewNext;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send_to_generate')
                ->visible(function (Post $post) {
                    return $post->status === Post::STATUS_CREATED;
                })
                ->label('Send to Generate')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->action(function () {
                    try {
                        SendToGenerateAction::run($this->record);
                        Notification::make()
                            ->title('Post sent to AI generator')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Failed to send to generator')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Send to AI Generator')
                ->modalDescription('This will send the post to AI generator for content creation.'),
            Action::make('get_from_generate')
                ->label('Get from Generate')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    try {
                        GetToGenerateAction::run($this->record);
                        Notification::make()
                            ->title('Content retrieved from generator')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Failed to get from generator')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Action::make('regenerate')
                ->label('Regenerate')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->action(function () {
                    try {
                        RegenerateAction::run($this->record);
                        Notification::make()
                            ->title('Post regeneration started')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Failed to regenerate')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Regenerate Content')
                ->modalDescription('This will regenerate the post content using AI.'),
            Action::make('publish')
                ->label($this->record->isPublished() ? 'Unpublish' : 'Publish')
                ->icon($this->record->isPublished() ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                ->color($this->record->isPublished() ? 'danger' : 'success')
                ->action(function () {
                    if ($this->record->isPublished()) {
                        $this->record->update(['published_at' => null]);
                        Notification::make()
                            ->title('Post unpublished successfully')
                            ->danger()
                            ->send();
                    } else {
                        $this->record->update(['published_at' => now()]);
                        Notification::make()
                            ->title('Post published successfully')
                            ->success()
                            ->send();
                    }
                }),
            EditAction::make(),
            ...self::getPreviewNextActions(),
        ];
    }
}