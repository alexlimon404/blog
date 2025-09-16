<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
        ];
    }
}