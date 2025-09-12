<?php

namespace App\Filament\Widgets;

use App\Models\Visit;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VisitStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Всего кликов', Visit::getTotalVisits())
                ->color('success'),

            Stat::make('Сегодня кликов', Visit::getTodayVisits())
                ->color('primary'),

            Stat::make('Уникальные посетители', Visit::getUniqueVisitors())
                ->color('warning'),

            Stat::make('Сегодня посетители', Visit::getTodayUniqueVisits())
                ->color('primary'),
        ];
    }
}
