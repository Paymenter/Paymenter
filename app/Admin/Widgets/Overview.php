<?php

namespace App\Admin\Widgets;

use App\Enums\DashboardPeriod;
use App\Enums\InvoiceTransactionStatus;
use App\Models\InvoiceTransaction;
use App\Models\Service;
use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;

class Overview extends BaseWidget
{
    // Poll every 5 minutes (5m doesn't work somehow)
    protected ?string $pollingInterval = '600s';

    public ?string $period = null;

    #[On('dashboard-period-updated')]
    public function updatePeriod(string $period): void
    {
        $this->period = $period;
    }

    protected function getStats(): array
    {
        return [
            $this->invoiceTransaction(),
            // Fewer new tickets is the good outcome, so the trend color is inverted
            $this->getData(Ticket::class, 'Tickets', lowerIsBetter: true),
            $this->getData(Service::class, 'Services'),
        ];
    }

    private function invoiceTransaction(): Stat
    {
        $query = InvoiceTransaction::query()
            ->where('status', InvoiceTransactionStatus::Succeeded)
            ->where('is_credit_transaction', false);

        $chart = $this->trend(Trend::query(clone $query))->sum('amount');

        $previous = $this->previousPeriod(clone $query)->sum('amount');

        return $this->stat('Revenue', $chart, $previous);
    }

    private function getData(string $model, string $name, bool $lowerIsBetter = false): Stat
    {
        $chart = $this->trend(Trend::model($model))->count();

        $previous = $this->previousPeriod($model::query())->count();

        return $this->stat($name, $chart, $previous, $lowerIsBetter);
    }

    private function period(): DashboardPeriod
    {
        return DashboardPeriod::fromValue($this->period);
    }

    private function trend(Trend $trend): Trend
    {
        return $trend
            ->between(
                start: $this->period()->start(),
                end: now(),
            )
            ->interval($this->period()->interval());
    }

    /**
     * Scope the query to the period directly before the one shown, without overlapping it.
     */
    private function previousPeriod(Builder $query): Builder
    {
        $start = $this->period()->start();

        return $query
            ->where('created_at', '>=', $this->period()->start($start))
            ->where('created_at', '<', $start);
    }

    private function stat(string $label, Collection $chart, float|int $previous, bool $lowerIsBetter = false): Stat
    {
        $current = $chart->sum('aggregate');

        $change = $current - $previous;

        $percentage = $previous > 0 ? (abs($change) / $previous) * 100 : 0;

        $color = ($lowerIsBetter ? $change <= 0 : $change >= 0) ? 'success' : 'danger';

        return Stat::make($label, $current)
            // The sparkline only reads its color when Alpine initializes it, so key the stat on the
            // color to make Livewire replace the element when it changes (filamentphp/filament#13518)
            ->key("{$label}-{$color}")
            ->description(($change >= 0 ? 'Increased by ' : 'Decreased by ') . number_format($percentage, 2) . '% (' . strtolower($this->period()->label()) . ')')
            ->descriptionIcon($change >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->chart($chart->map(fn (TrendValue $value) => $value->aggregate)->toArray())
            ->color($color);
    }

    public static function canView(): bool
    {
        return auth()->user()->hasPermission('admin.widgets.overview');
    }
}
