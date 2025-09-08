<?php

namespace App\Filament\Resources\BasePrompts\Pages;

use App\Filament\Resources\BasePrompts\BasePromptResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBasePrompt extends EditRecord
{
    protected static string $resource = BasePromptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
