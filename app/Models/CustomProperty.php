<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class CustomProperty extends Model implements Auditable
{
    use \App\Models\Traits\Auditable, HasFactory;

    public $timestamps = false;

    public $guarded = [];

    public $casts = [
        'allowed_values' => 'array',
        'condition_rules' => 'array',
        'sort_order' => 'integer',
    ];

    protected $attributes = [
        'condition_mode' => 'none',
        'sort_order' => 0,
    ];

    protected static function booted(): void
    {
        static::creating(function (CustomProperty $property): void {
            if (! is_numeric($property->sort_order) && $property->model) {
                $property->sort_order = static::nextSortOrderForModel($property->model);
            }

            if (is_numeric($property->sort_order)) {
                $property->sort_order = max(0, (int) $property->sort_order);
            }
        });

        static::saving(function (CustomProperty $property): void {
            if (! is_numeric($property->sort_order) && $property->model) {
                $property->sort_order = static::nextSortOrderForModel($property->model);
            }

            if (is_numeric($property->sort_order)) {
                $property->sort_order = max(0, (int) $property->sort_order);
            }
        });
    }

    public static function nextSortOrderForModel(string $model): int
    {
        $max = static::query()
            ->where('model', $model)
            ->max('sort_order');

        return is_numeric($max) ? ((int) $max + 1) : 0;
    }

    public function moveOrderUp(): void
    {
        $previous = $this->orderScope()
            ->where('sort_order', '<', $this->sort_order ?? 0)
            ->orderByDesc('sort_order')
            ->first();

        if (! $previous) {
            return;
        }

        $this->swapSortOrderWith($previous);
    }

    public function moveOrderDown(): void
    {
        $next = $this->orderScope()
            ->where('sort_order', '>', $this->sort_order ?? 0)
            ->orderBy('sort_order')
            ->first();

        if (! $next) {
            return;
        }

        $this->swapSortOrderWith($next);
    }

    public function canMoveUp(): bool
    {
        return $this->orderScope()
            ->where('sort_order', '<', $this->sort_order ?? 0)
            ->exists();
    }

    public function canMoveDown(): bool
    {
        return $this->orderScope()
            ->where('sort_order', '>', $this->sort_order ?? 0)
            ->exists();
    }

    protected function orderScope(): Builder
    {
        return static::query()->where('model', $this->model);
    }

    protected function swapSortOrderWith(self $other): void
    {
        $current = $this->sort_order ?? 0;
        $target = $other->sort_order ?? 0;

        static::withoutEvents(function () use ($other, $current, $target): void {
            $this->forceFill(['sort_order' => $target])->saveQuietly();
            $other->forceFill(['sort_order' => $current])->saveQuietly();
        });
    }
}
