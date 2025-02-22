@if (config('settings.logo'))
    <img src="{{ Storage::url(config('settings.logo')) }}" alt="{{ config('app.name') }}" class="h-12 w-auto inline-block" />
@endif
