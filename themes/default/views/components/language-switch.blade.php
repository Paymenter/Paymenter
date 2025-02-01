<x-select
    wire:model.live="currentLocale"
    :options="collect($locales)->map(fn($locale, $code) => [
        'value' => $code,
        'label' => $locale
    ])->values()->toArray()"
    placeholder="Select language"
/>