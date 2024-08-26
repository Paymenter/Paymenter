@if (config('settings.logo'))
    <img src="/{{ config('settings.logo') }}" alt="{{ config('app.name') }}" class="h-12 w-fit" />
@endif
