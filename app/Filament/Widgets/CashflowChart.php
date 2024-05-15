<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class CashflowChart extends ChartWidget
{
    protected static ?string $heading = 'Cashflow Chart';
    protected int | string | array $columnSpan = 1;
    protected static ?int $sort = 2;


    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'In and Out Charts',
                    'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
