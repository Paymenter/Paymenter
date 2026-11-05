<?php

namespace App\Enums;

use Carbon\CarbonInterface;

enum DashboardPeriod: string
{
    case Day = 'today';
    case Week = 'week';
    case Month = 'month';
    case Year = 'year';

    public static function default(): self
    {
        return self::Month;
    }

    /**
     * Resolve a period from user input, falling back to the default for unknown values.
     */
    public static function fromValue(?string $value): self
    {
        return self::tryFrom((string) $value) ?? self::default();
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $period) => [$period->value => $period->label()])
            ->all();
    }

    public function label(): string
    {
        return match ($this) {
            self::Day => 'Last 24 hours',
            self::Week => 'Last 7 days',
            self::Month => 'Last 30 days',
            self::Year => 'Last 365 days',
        };
    }

    /**
     * Start of this period, counted back from $from (defaults to now).
     * Passing the start of the current period gives the start of the previous one.
     */
    public function start(?CarbonInterface $from = null): CarbonInterface
    {
        $from = ($from ?? now())->copy();

        return match ($this) {
            self::Day => $from->subDay()->startOfDay(),
            self::Week => $from->subWeek()->startOfDay(),
            self::Month => $from->subMonth()->startOfDay(),
            self::Year => $from->subYear()->startOfDay(),
        };
    }

    /**
     * Bucket size used by laravel-trend for this period.
     */
    public function interval(): string
    {
        return match ($this) {
            self::Day => 'hour',
            self::Week, self::Month => 'day',
            self::Year => 'month',
        };
    }

    /**
     * Date format for chart axis labels.
     */
    public function dateFormat(): string
    {
        return match ($this) {
            self::Day => 'H:i',
            self::Year => 'M',
            default => 'M d',
        };
    }
}
