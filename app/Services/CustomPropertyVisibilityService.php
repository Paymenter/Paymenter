<?php

namespace App\Services;

use App\Models\CustomProperty;
use Illuminate\Support\Collection;

class CustomPropertyVisibilityService
{
    public function filter(Collection $properties, array $currentValues = []): Collection
    {
        $values = $this->resolvePropertyValues($currentValues);

        return $properties
            ->values()
            ->filter(fn (CustomProperty $property) => $this->shouldDisplayProperty($property, $values))
            ->values();
    }

    protected function shouldDisplayProperty(CustomProperty $property, array $currentValues): bool
    {
        $mode = $property->condition_mode ?? 'none';

        if ($mode === 'none') {
            return true;
        }

        $conditions = $property->condition_rules;

        if (! is_array($conditions) || $conditions === []) {
            return true;
        }

        $results = collect($conditions)
            ->filter(fn ($condition) => is_array($condition) && isset($condition['operator'], $condition['property_key']))
            ->map(fn ($condition) => $this->evaluateCondition($condition, $currentValues))
            ->filter(fn ($result) => $result !== null);

        if ($results->isEmpty()) {
            return true;
        }

        if ($mode === 'any') {
            return $results->contains(true);
        }

        return $results->every(fn ($result) => $result === true);
    }

    protected function evaluateCondition(array $condition, array $currentValues): ?bool
    {
        $key = $condition['property_key'] ?? null;

        if (! is_string($key) || trim($key) === '') {
            return null;
        }

        $operator = strtolower((string) ($condition['operator'] ?? 'equals'));
        $value = $currentValues[$key] ?? null;

        return match ($operator) {
            'equals' => $this->valuesAreEqual($key, $value, $condition['value'] ?? null),
            'not_equals' => ! $this->valuesAreEqual($key, $value, $condition['value'] ?? null),
            'in' => $this->valueInSet($key, $value, $condition['values'] ?? []),
            'not_in' => ! $this->valueInSet($key, $value, $condition['values'] ?? []),
            'filled' => filled($value),
            'blank' => blank($value),
            default => null,
        };
    }

    protected function valueInSet(string $propertyKey, mixed $value, mixed $expected): bool
    {
        $expectedValues = $this->normalizeArray($expected);

        if ($propertyKey === 'country') {
            $expectedValues = collect($expectedValues)
                ->merge(collect($expectedValues)->map(fn ($item) => $this->normalizeCountryValue($item)))
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        if ($expectedValues === []) {
            return false;
        }

        if (is_array($value)) {
            $normalized = $this->normalizeArray($value);

            if ($propertyKey === 'country') {
                $normalized = collect($normalized)
                    ->merge(collect($normalized)->map(fn ($item) => $this->normalizeCountryValue($item)))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
            }

            return collect($normalized)->intersect($expectedValues)->isNotEmpty();
        }

        if ($propertyKey === 'country') {
            $candidates = array_filter([
                $this->normalizeScalar($value),
                $this->normalizeCountryValue($value),
            ]);

            foreach (array_unique($candidates) as $candidate) {
                if (in_array($candidate, $expectedValues, true)) {
                    return true;
                }
            }

            return false;
        }

        $normalizedValue = $this->normalizeScalar($value);

        if ($normalizedValue === null) {
            return false;
        }

        return in_array($normalizedValue, $expectedValues, true);
    }

    protected function valuesAreEqual(string $propertyKey, mixed $first, mixed $second): bool
    {
        $normalizedFirst = $this->normalizeScalar($first);
        $normalizedSecond = $this->normalizeScalar($second);

        if ($propertyKey === 'country') {
            $firstCountry = $this->normalizeCountryValue($first);
            $secondCountry = $this->normalizeCountryValue($second);

            if ($firstCountry !== null && $secondCountry !== null) {
                return $firstCountry === $secondCountry;
            }
        }

        return $normalizedFirst === $normalizedSecond;
    }

    protected function resolvePropertyValues(array $provided): array
    {
        if ($provided !== []) {
            return $this->normalizeProvidedValues($provided);
        }

        return $this->normalizeProvidedValues($this->resolvePropertyValuesFromRequest());
    }

    protected function resolvePropertyValuesFromRequest(): array
    {
        $values = request()->input('properties');

        if (is_array($values)) {
            return $values;
        }

        $old = old('properties');

        if (is_array($old)) {
            return $old;
        }

        return [];
    }

    protected function normalizeProvidedValues(array $values): array
    {
        return collect($values)
            ->map(function ($item) {
                if (is_array($item)) {
                    $normalized = $this->normalizeArray($item);

                    if (count($normalized) === 1) {
                        return $normalized[0];
                    }

                    return $normalized;
                }

                return $this->normalizeScalar($item) ?? $item;
            })
            ->toArray();
    }

    protected function normalizeScalar(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        if (is_string($value)) {
            $trimmed = trim($value);

            return $trimmed === '' ? null : $trimmed;
        }

        return null;
    }

    protected function normalizeArray(mixed $value): array
    {
        if (is_array($value)) {
            return collect($value)
                ->map(fn ($item) => $this->normalizeScalar($item))
                ->filter(fn ($item) => $item !== null && $item !== '')
                ->values()
                ->toArray();
        }

        if (is_string($value)) {
            return collect(preg_split('/[,;|]+/', $value))
                ->map(fn ($item) => $this->normalizeScalar($item))
                ->filter(fn ($item) => $item !== null && $item !== '')
                ->values()
                ->toArray();
        }

        return [];
    }

    protected function normalizeCountryValue(mixed $value): ?string
    {
        if (is_array($value)) {
            $value = reset($value);
        }

        $normalized = $this->normalizeScalar($value);

        if ($normalized === null) {
            return null;
        }

        $normalized = strtoupper($normalized);

        if (strlen($normalized) === 2) {
            return $normalized;
        }

        $countries = config('app.countries', []);

        foreach ($countries as $code => $name) {
            if (! is_string($code) || $code === '') {
                continue;
            }

            if (strcasecmp((string) $name, $normalized) === 0) {
                return strtoupper($code);
            }
        }

        return $normalized;
    }
}
