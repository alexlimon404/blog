<?php

namespace App\Filament\Resources\PushSubscriptions\Pages;

use App\Filament\Resources\PushSubscriptions\PushSubscriptionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPushSubscription extends EditRecord
{
    protected static string $resource = PushSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
