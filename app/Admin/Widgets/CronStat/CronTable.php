<?php

namespace App\Admin\Widgets\CronStat;

use App\Models\CronStat;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class CronTable extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    protected static bool $isLazy = true;

    public ?string $filter = 'week';

    protected ?string $maxHeight = '300px';

    protected ?string $heading = 'Cron Table';

    // Start at zero (chartjs option)
    protected ?array $options = [
        'scales' => [
            'y' => [
                'beginAtZero' => true,
            ],
        ],
    ];

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        $start = match ($activeFilter) {
            'today' => now()->subDay(),
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
        };
        $end = now();

        $invoicesCreated = $this->addDefault(Trend::query(CronStat::query()->where('key', 'invoices_created')), $start, $end);
        $servicesSuspended = $this->addDefault(Trend::query(CronStat::query()->where('key', 'services_suspended')), $start, $end);
        $servicesTerminated = $this->addDefault(Trend::query(CronStat::query()->where('key', 'services_terminated')), $start, $end);
        $invoicesCharged = $this->addDefault(Trend::query(CronStat::query()->where('key', 'invoice_charged')), $start, $end);

        return [
            'datasets' => [
                [
                    'label' => 'Invoices Created',

                    'data' => $invoicesCreated->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Services Suspended',
                    'data' => $servicesSuspended->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(255, 206, 86, 0.5)',
                    'borderColor' => 'rgba(255, 206, 86, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Services Terminated',
                    'data' => $servicesTerminated->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(255, 99, 132, 0.5)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Invoices Charged',
                    'data' => $invoicesCharged->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $invoicesCreated->map(fn (TrendValue $value) => $value->date)->toArray(),
        ];
    }

    private function addDefault(Trend $data, $start, $end)
    {
        return $data
            ->dateColumn('date')
            ->between(
                start: $start,
                end: $end,
            )
            ->perDay()
            ->sum('value');
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
