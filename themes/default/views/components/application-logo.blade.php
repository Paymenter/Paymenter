@if (config('settings::app_logo'))
    <img src="{{ asset(config('settings::app_logo')) }}" alt="{{ config('settings::app_name') }}" class="w-10" />
@else
    <img src="/img/logo.png" alt="Paymenter" class="w-10">
@endif
