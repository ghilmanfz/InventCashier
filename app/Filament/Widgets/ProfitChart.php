<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class ProfitChart extends ChartWidget
{
    use InteractsWithPageFilters;
    protected static ?string $pollingInterval = null;
    protected static ?string $heading = 'Profit';
    protected static ?string $maxHeight = '300px';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $createdFrom = new Carbon($this->filters['start_date'] ?? now()->startOfMonth());
        $createdTo = new Carbon($this->filters['end_date'] ?? now());

        $data = Trend::query(Order::query()->whereStatus(OrderStatus::COMPLETED))
            ->between(start: $createdFrom, end: $createdTo)
            ->perDay()
            ->sum('profit');

        return [
            'datasets' => [
                [
                    'label' => 'Profit',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate)->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, .1)',
                    'fill' => 'start',
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => (new Carbon($value->date))->format('d M')),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
