<x-select
    id="language-switch"
    name="language"
    wire:model.live="currentLocale"
    class="text-sm text-base/50"
>
    @foreach ($locales as $locale)
        <option value="{{ $locale }}" @selected($locale === $currentLocale)>
            {{ strtoupper($locale) }}
        </option>
    @endforeach
</x-select>