@props(['icon' => 'ri-bar-chart-fill', 'value' => 0, 'label' => '', 'suffix' => ''])

<div class="cyber-card cyber-card-hover cyber-clip p-5 group">
    <div class="flex items-center justify-between">
        <div class="p-2.5 rounded-lg bg-primary/10 border border-primary/25 group-hover:bg-primary/20 transition">
            <x-dynamic-component :component="$icon" class="size-5 text-primary" />
        </div>
        <x-ri-arrow-right-up-line class="size-4 text-base/25 group-hover:text-primary transition" />
    </div>
    <div
        class="mt-4 text-3xl md:text-4xl font-black font-mono neon-text"
        x-data="{ shown: 0, target: {{ (int) $value }} }"
        x-init="
            let steps = 28, i = 0;
            let timer = setInterval(() => {
                i++;
                shown = Math.round(target * (i / steps));
                if (i >= steps) { shown = target; clearInterval(timer); }
            }, 28)
        "
        x-text="shown.toLocaleString() + '{{ $suffix }}'">{{ number_format((int) $value) }}{{ $suffix }}</div>
    <div class="mt-1 text-xs uppercase tracking-widest text-base/55">{{ $label }}</div>
</div>
