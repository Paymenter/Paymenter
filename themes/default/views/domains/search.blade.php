<div class="container mt-14 space-y-6">
    <x-navigation.breadcrumb />

    @php
        $currency = App\Models\Currency::find(session('currency', config('settings.default_currency')));
        $formatPrice = fn ($price) => $price !== null && $currency
            ? (new App\Classes\Price(['price' => $price, 'currency' => $currency]))->format($price)
            : 'Unavailable';
    @endphp

    <div class="bg-background-secondary border border-neutral p-6 rounded-lg space-y-4">
        <div>
            <h1 class="text-2xl font-semibold">Find your domain</h1>
            <p class="text-sm text-base/70">Search availability and register your domain.</p>
        </div>

        <form wire:submit="check" class="flex flex-col md:flex-row gap-2">
            <input
                type="text"
                wire:model="sld"
                class="flex-1 rounded-md bg-background border border-neutral px-4 py-3.5 text-base"
                placeholder="example"
            />
            <select wire:model.live="tld" class="rounded-md bg-background border border-neutral px-4 py-3.5 text-base">
                @foreach ($tlds as $tld)
                    <option value="{{ $tld->tld }}">.{{ $tld->tld }}</option>
                @endforeach
            </select>
            <select wire:model.live="years" class="rounded-md bg-background border border-neutral px-4 py-3.5 text-base">
                @foreach ($availableYears as $year)
                    <option value="{{ $year }}">{{ $year }} {{ $year === 1 ? 'year' : 'years' }}</option>
                @endforeach
            </select>
            <x-button.primary type="submit" class="!w-fit self-end md:self-auto md:ml-auto">Check availability</x-button.primary>
        </form>

        @error('sld')
            <p class="text-error text-sm">{{ $message }}</p>
        @enderror

        @if ($error)
            <div class="bg-error/10 border border-error/30 text-error p-3 rounded-md">
                {{ $error }}
            </div>
        @endif

        @if ($result && ($result['available'] ?? false))
            <div class="bg-success/10 border border-success/30 p-4 rounded-md flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <p class="font-semibold text-success">{{ $result['name'] }} is available</p>
                    @php($resultYears = max(1, (int) ($result['years'] ?? 1)))
                    <p class="text-sm">
                        Registration: {{ $formatPrice($result['price'] ?? null) }} for {{ $resultYears }} {{ $resultYears === 1 ? 'year' : 'years' }}
                        @if ($resultYears > 1)
                        <span class="text-base/70">({{ $formatPrice(($result['price'] ?? 0) / $resultYears) }} / year)</span>
                        @endif
                    </p>
                </div>
                <x-button.primary wire:click="register" class="!w-fit self-end md:self-auto md:ml-auto">Register domain</x-button.primary>
            </div>
        @endif
    </div>

    <div class="bg-background-secondary border border-neutral p-6 rounded-lg">
        <h2 class="text-lg font-semibold mb-3">TLD prices</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach ($tlds as $tld)
                @php($tldPrice = $tld->prices->where('currency_code', session('currency', config('settings.default_currency')))->where('years', 1)->first())
                <div class="border border-neutral rounded-md p-3">
                    <p class="font-semibold">.{{ $tld->tld }}</p>
                    <p class="text-sm text-base/70">
                        @if ($tldPrice?->register !== null)
                            {{ $formatPrice($tldPrice->register) }} / {{ trans_choice(__('services.billing_cycles.year'), 1) }}
                        @else
                            Unavailable
                        @endif
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</div>
