<?php

namespace App\Filament\Resources\Visits\Pages;

use App\Filament\Resources\Visits\VisitResource;
use App\Filament\Widgets\VisitStatsWidget;
use Filament\Resources\Pages\ListRecords;

class ListVisits extends ListRecords
{
    protected static string $resource = VisitResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            VisitStatsWidget::class,
        ];
    }
}
