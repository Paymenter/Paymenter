@php
$slides = cyber_marketing('banner_slides');
$words = collect(cyber_words());
$interval = max(1500, (int) cyber_cfg('banner_interval', 6000));
$align = cyber_align();
$shopUrl = null;
try {
    $firstCategory = \App\Models\Category::whereNull('parent_id')
        ->where(fn ($q) => $q->whereHas('children')->orWhereHas('products', fn ($p) => $p->where('hidden', false)))
        ->orderBy('sort')->first();
    $shopUrl = $firstCategory ? route('category.show', ['category' => $firstCategory->slug]) : null;
} catch (\Throwable $e) {
    $shopUrl = null;
}
@endphp

@if(count($slides) > 0)
<section
    class="relative overflow-hidden border-b border-neutral"
    x-data="{
        slide: 0,
        total: {{ count($slides) }},
        word: 0,
        words: {{ Js::from($words) }},
        start() {
            if (this.total > 1) {
                setInterval(() => { this.slide = (this.slide + 1) % this.total }, {{ $interval }});
            }
            if (this.words.length > 1) {
                setInterval(() => { this.word = (this.word + 1) % this.words.length }, 2600);
            }
        }
    }"
    x-init="start()">

    {{-- Fondos de cada slide --}}
    @foreach($slides as $index => $slide)
    @php $image = cyber_media($slide['image'] ?? null); @endphp
    <div
        x-show="slide === {{ $index }}"
        x-transition:enter="transition ease-out duration-700"
        x-transition:enter-start="opacity-0 scale-105"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-500"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0"
        aria-hidden="true">
        @if($image)
        <img src="{{ $image }}" alt="" class="w-full h-full object-cover object-center">
        <div class="absolute inset-0 bg-gradient-to-r from-background via-background/85 to-background/40"></div>
        @else
        <div class="absolute inset-0 cyber-gradient opacity-25"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-background via-background/90 to-background/60"></div>
        @endif
    </div>
    @endforeach

    {{-- Contenido del slide --}}
    {{-- Todas las diapositivas se apilan en la misma celda de la rejilla, así
         que todas comparten exactamente el mismo ancho y la alineación
         (izquierda / centro / derecha) se aplica igual a todas. --}}
    <div class="relative container py-16 md:py-24 min-h-[440px] flex flex-col justify-center">
        <div class="grid flex-grow">
        @foreach($slides as $index => $slide)
        <div x-show="slide === {{ $index }}" x-cloak
            x-transition:enter="transition ease-out duration-700 delay-100"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="col-start-1 row-start-1 w-full self-center flex flex-col {{ $align['col'] }}">

            <div class="w-full max-w-3xl {{ $align['wrapper'] }} {{ $align['text'] }} flex flex-col {{ $align['col'] }}">

            <span class="cyber-chip animate-cyber-flicker">
                <x-ri-flashlight-fill class="size-3.5" />
                {{ config('app.name') }}
            </span>

            <h1 class="mt-5 text-4xl md:text-6xl font-black leading-[1.05] cyber-neon-text">
                <span class="cyber-glitch" data-text="{{ $slide['title'] ?? config('app.name') }}">{{ $slide['title'] ?? config('app.name') }}</span>
            </h1>

            @if(!empty($slide['subtitle']))
            <p class="mt-5 text-lg md:text-xl text-base/75 max-w-2xl {{ $align['wrapper'] }}">{{ $slide['subtitle'] }}</p>
            @endif

            @if($words->count() > 0)
            <div class="mt-6 flex flex-wrap items-center gap-3 text-lg md:text-2xl font-bold {{ $align['items'] }}">
                <x-ri-terminal-box-fill class="size-6 text-primary shrink-0" />
                <span class="text-base/60 hidden sm:inline">&gt;</span>
                <template x-for="(w, i) in words" :key="i">
                    <span x-show="word === i" x-cloak
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="cyber-gradient-text"
                        x-text="w"></span>
                </template>
                <span class="w-2.5 h-6 bg-primary animate-pulse rounded-sm"></span>
            </div>
            @endif

            <div class="mt-9 flex flex-wrap gap-3 {{ $align['items'] }}">
                @php
                    $btnUrl = $slide['button_url'] ?? '';
                    $btnUrl = $btnUrl !== '' ? $btnUrl : ($shopUrl ?? route('home'));
                @endphp
                <a href="{{ $btnUrl }}" class="w-fit">
                    <x-button.primary class="!w-fit px-7 py-3 text-base cyber-sweep">
                        {{ $slide['button_label'] ?? 'Ver planes' }}
                        <x-ri-arrow-right-fill class="size-5" />
                    </x-button.primary>
                </a>
                @auth
                <a href="{{ route('dashboard') }}" wire:navigate class="w-fit">
                    <x-button.secondary class="!w-fit px-7 py-3 text-base">
                        {{ __('navigation.dashboard') }}
                    </x-button.secondary>
                </a>
                @else
                @if(!config('settings.registration_disabled', false))
                <a href="{{ route('register') }}" wire:navigate class="w-fit">
                    <x-button.secondary class="!w-fit px-7 py-3 text-base">
                        {{ __('navigation.register') }}
                    </x-button.secondary>
                </a>
                @endif
                @endauth
            </div>
            </div>
        </div>
        @endforeach
        </div>

        {{-- Indicadores --}}
        @if(count($slides) > 1)
        <div class="flex gap-2 mt-10 {{ $align['items'] }}">
            @foreach($slides as $index => $slide)
            <button type="button" @click="slide = {{ $index }}"
                class="h-1.5 rounded-full transition-all duration-300 cursor-pointer"
                :class="slide === {{ $index }} ? 'w-10 bg-primary' : 'w-4 bg-neutral hover:bg-primary/50'"
                aria-label="Slide {{ $index + 1 }}"></button>
            @endforeach
        </div>
        @endif
    </div>

    <div class="cyber-divider"></div>
</section>
@endif
