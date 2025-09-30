<?php

namespace App\Filament\Widgets;

use App\Models\Visit;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class VisitChart extends ChartWidget
{
    protected ?string $heading = 'Клики';

    protected int | string | array $columnSpan = '1';

    protected function getFilters(): ?array
    {
        $filters = [];
        $now = Carbon::now();
        $currentYear = $now->year;
        $currentMonth = $now->month;

        // Генерируем список: от текущего месяца до следующего года включительно
        for ($year = $currentYear; $year <= $currentYear + 1; $year++) {
            $startMonth = ($year === $currentYear) ? $currentMonth : 1;
            $endMonth = ($year === $currentYear + 1) ? $currentMonth : 12;

            for ($month = $startMonth; $month <= $endMonth; $month++) {
                $date = Carbon::create($year, $month, 1);
                $filters[$date->format('Y-m')] = $date->translatedFormat('F Y');
            }
        }

        return $filters;
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter ?? Carbon::now()->format('Y-m');
        $selectedDate = Carbon::createFromFormat('Y-m', $activeFilter);

        $data = [];
        $labels = [];

        $startOfMonth = $selectedDate->startOfMonth()->copy();
        $endOfMonth = $selectedDate->endOfMonth()->copy();

        for ($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {
            $labels[] = $date->format('d');
            $data[] = Visit::whereDate('created_at', $date)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Клики',
                    'data' => $data,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'ticks' => [
                        'stepSize' => 1,
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
