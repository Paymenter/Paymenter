@php
$lightLogo = config('settings.logo');
$darkLogo = config('settings.logo_dark');
@endphp

@if ($lightLogo && $darkLogo)
<img src="{{ Storage::url($lightLogo) }}" alt="{{ config('app.name') }}" {{ $attributes->merge(['class' => 'w-auto inline-block dark:hidden']) }}>
<img src="{{ Storage::url($darkLogo) }}" alt="{{ config('app.name') }}" {{ $attributes->merge(['class' => 'w-auto hidden dark:inline-block']) }}>
@elseif ($lightLogo)
<img src="{{ Storage::url($lightLogo) }}" alt="{{ config('app.name') }}" {{ $attributes->merge(['class' => 'w-auto inline-block']) }}>
@elseif ($darkLogo)
<img src="{{ Storage::url($darkLogo) }}" alt="{{ config('app.name') }}" {{ $attributes->merge(['class' => 'w-auto inline-block']) }}>
@endif
