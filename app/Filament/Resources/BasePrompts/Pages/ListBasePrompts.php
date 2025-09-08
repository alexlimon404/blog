<?php

namespace App\Filament\Resources\BasePrompts\Pages;

use App\Filament\Resources\BasePrompts\BasePromptResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBasePrompts extends ListRecords
{
    protected static string $resource = BasePromptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
