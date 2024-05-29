<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class iiAdminSalesChart extends ChartWidget
{
    protected static ?string $heading = 'Sales Flow';
    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return false;
    }
    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Sales in the past 30 Days',
                    'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
