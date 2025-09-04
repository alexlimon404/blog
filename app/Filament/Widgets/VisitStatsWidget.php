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
            Stat::make('Всего посещений', Visit::getTotalVisits())
                ->description('Общее количество посещений')
                ->descriptionIcon('heroicon-m-eye')
                ->color('success'),

            Stat::make('Сегодня', Visit::getTodayVisits())
                ->description('Посещений сегодня')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            Stat::make('Уникальные посетители', Visit::getUniqueVisitors())
                ->description('По IP адресам')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),
        ];
    }
}
