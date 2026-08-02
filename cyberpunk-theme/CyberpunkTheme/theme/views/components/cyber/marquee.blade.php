@php
$words = collect(cyber_words());
$duration = max(12, $words->count() * 4);
@endphp

@if($words->count() > 0)
<div class="cyber-marquee border-y border-neutral bg-background-secondary/60 py-3" style="--marquee-duration: {{ $duration }}s">
    <div class="cyber-marquee__track">
        @for($i = 0; $i < 2; $i++)
        <div class="flex items-center gap-8 pr-8" aria-hidden="{{ $i === 1 ? 'true' : 'false' }}">
            @foreach($words as $word)
            <span class="flex items-center gap-3 text-sm md:text-base font-semibold uppercase tracking-widest whitespace-nowrap">
                <x-ri-flashlight-fill class="size-4 text-primary" />
                <span class="text-base/80">{{ $word }}</span>
            </span>
            @endforeach
        </div>
        @endfor
    </div>
</div>
@endif
