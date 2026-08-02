@php
$slides = cyber_marketing('banner_slides');
$words = collect(cyber_words());
$interval = max(1500, (int) cyber_cfg('banner_interval', 6000));
$socials = cyber_socials();
@endphp

<div class="relative hidden lg:flex flex-col justify-between overflow-hidden rounded-2xl border border-neutral cyber-clip min-h-[560px] p-10"
    x-data="{
        slide: 0,
        total: {{ max(1, count($slides)) }},
        word: 0,
        words: {{ Js::from($words) }},
        start() {
            if (this.total > 1) setInterval(() => { this.slide = (this.slide + 1) % this.total }, {{ $interval }});
            if (this.words.length > 1) setInterval(() => { this.word = (this.word + 1) % this.words.length }, 2400);
        }
    }"
    x-init="start()">

    @forelse($slides as $index => $slide)
    @php $image = cyber_media($slide['image'] ?? null); @endphp
    <div x-show="slide === {{ $index }}"
        x-transition:enter="transition ease-out duration-700"
        x-transition:enter-start="opacity-0 scale-105"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-500"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 -z-10" aria-hidden="true">
        @if($image)
        <img src="{{ $image }}" alt="" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-background via-background/85 to-background/50"></div>
        @else
        <div class="absolute inset-0 cyber-gradient opacity-30"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-background via-background/90 to-background/60"></div>
        @endif
    </div>
    @empty
    <div class="absolute inset-0 -z-10 cyber-gradient opacity-25" aria-hidden="true"></div>
    @endforelse

    <div>
        <x-logo class="h-12" />
        <div class="mt-8 cyber-chip">
            <x-ri-shield-flash-fill class="size-3.5" />
            {{ config('app.name') }}
        </div>
        <h2 class="mt-5 text-4xl font-black leading-tight cyber-neon-text">
            <span class="cyber-glitch" data-text="{{ $slides[0]['title'] ?? config('app.name') }}">{{ $slides[0]['title'] ?? config('app.name') }}</span>
        </h2>
        @if(!empty($slides[0]['subtitle']))
        <p class="mt-4 text-base/70 max-w-md">{{ $slides[0]['subtitle'] }}</p>
        @endif
    </div>

    @if($words->count() > 0)
    <div class="my-8">
        <div class="flex items-center gap-3 text-xl font-bold min-h-[2rem]">
            <x-ri-terminal-box-fill class="size-5 text-primary shrink-0" />
            <template x-for="(w, i) in words" :key="i">
                <span x-show="word === i" x-cloak
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-x-3"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    class="cyber-gradient-text" x-text="w"></span>
            </template>
            <span class="w-2 h-5 bg-primary animate-pulse rounded-sm"></span>
        </div>
    </div>
    @endif

    <div class="flex flex-col gap-5">
        @php $stats = cyber_stats(); @endphp
        <div class="grid grid-cols-3 gap-3">
            <div class="rounded-lg border border-neutral bg-background/50 p-3 text-center">
                <div class="text-xl font-black neon-text">{{ number_format($stats['clients']) }}</div>
                <div class="text-[10px] uppercase tracking-widest text-base/50">Clientes</div>
            </div>
            <div class="rounded-lg border border-neutral bg-background/50 p-3 text-center">
                <div class="text-xl font-black neon-text">{{ number_format($stats['services']) }}</div>
                <div class="text-[10px] uppercase tracking-widest text-base/50">Activos</div>
            </div>
            <div class="rounded-lg border border-neutral bg-background/50 p-3 text-center">
                <div class="text-xl font-black neon-text-accent">24/7</div>
                <div class="text-[10px] uppercase tracking-widest text-base/50">Soporte</div>
            </div>
        </div>
        @if(count($socials) > 0 && cyber_bool('socials_enabled', true))
        <x-cyber.socials :compact="true" />
        @endif
    </div>
</div>
