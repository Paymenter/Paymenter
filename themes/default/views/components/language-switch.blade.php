<x-select
    wire:model.live="currentLocale"
    :options="collect($locales)->map(fn($locale) => [
        'value' => $locale,
        'label' => strtoupper($locale)
    ])->values()->toArray()"
    placeholder="Select language"
/>