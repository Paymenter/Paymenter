@if (config('settings.logo'))
    <img src="{{ Storage::url(config('settings.logo')) }}" alt="{{ config('app.name') }}" {{ $attributes->merge(['class' => 'h-12 w-auto inline-block']) }}>
@endif
