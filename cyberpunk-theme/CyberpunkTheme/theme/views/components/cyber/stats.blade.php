@php
$stats = cyber_stats();
$visits = cyber_visits();
$showUptime = cyber_bool('uptime_enabled', true) && !empty($stats['uptime_start']);
$showVisits = cyber_bool('visitors_enabled', true) && ($visits['available'] ?? false);
@endphp

<section class="container py-10">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-cyber.stat-card icon="ri-team-fill" :value="$stats['clients']" label="Clientes registrados" />
        <x-cyber.stat-card icon="ri-server-fill" :value="$stats['services']" label="Servidores activos" />
        <x-cyber.stat-card icon="ri-shopping-bag-3-fill" :value="$stats['products']" label="Productos disponibles" />
        <x-cyber.stat-card icon="ri-award-fill" :value="$stats['orders']" label="Órdenes procesadas" />
    </div>

    @if($showUptime || $showVisits)
    <div class="grid md:grid-cols-{{ $showUptime && $showVisits ? '2' : '1' }} gap-4 mt-4">
        @if($showUptime)
        <div class="cyber-card cyber-clip p-5 cyber-neon">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 rounded-lg bg-primary/10 border border-primary/30">
                    <x-ri-pulse-fill class="size-5 text-primary" />
                </div>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-widest text-base/60">Tiempo activo</p>
                    <p class="text-xs text-base/45">Desde {{ \Illuminate\Support\Carbon::createFromTimestamp($stats['uptime_start'])->format('d/m/Y') }}</p>
                </div>
            </div>
            <div
                x-data="{
                    start: {{ (int) $stats['uptime_start'] }},
                    now: Math.floor(Date.now() / 1000),
                    get diff() { return Math.max(0, this.now - this.start) },
                    get days() { return Math.floor(this.diff / 86400) },
                    get hours() { return Math.floor((this.diff % 86400) / 3600) },
                    get minutes() { return Math.floor((this.diff % 3600) / 60) },
                    get seconds() { return this.diff % 60 },
                    pad(n) { return String(n).padStart(2, '0') }
                }"
                x-init="setInterval(() => { now = Math.floor(Date.now() / 1000) }, 1000)"
                class="grid grid-cols-4 gap-2 font-mono">
                <div class="text-center rounded-lg border border-neutral bg-background/60 py-3">
                    <div class="text-2xl md:text-3xl font-black neon-text" x-text="days"></div>
                    <div class="text-[10px] uppercase tracking-widest text-base/50 mt-1">Días</div>
                </div>
                <div class="text-center rounded-lg border border-neutral bg-background/60 py-3">
                    <div class="text-2xl md:text-3xl font-black neon-text" x-text="pad(hours)"></div>
                    <div class="text-[10px] uppercase tracking-widest text-base/50 mt-1">Horas</div>
                </div>
                <div class="text-center rounded-lg border border-neutral bg-background/60 py-3">
                    <div class="text-2xl md:text-3xl font-black neon-text" x-text="pad(minutes)"></div>
                    <div class="text-[10px] uppercase tracking-widest text-base/50 mt-1">Min</div>
                </div>
                <div class="text-center rounded-lg border border-neutral bg-background/60 py-3">
                    <div class="text-2xl md:text-3xl font-black neon-text-accent" x-text="pad(seconds)"></div>
                    <div class="text-[10px] uppercase tracking-widest text-base/50 mt-1">Seg</div>
                </div>
            </div>
        </div>
        @endif

        @if($showVisits)
        <div class="cyber-card cyber-clip p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 rounded-lg bg-accent/10 border border-accent/30">
                    <x-ri-eye-fill class="size-5 text-accent" />
                </div>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-widest text-base/60">Visitas</p>
                    <p class="text-xs text-base/45">Contadas desde que el tema está activo</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 font-mono">
                <div class="text-center rounded-lg border border-neutral bg-background/60 py-3">
                    <div class="text-2xl md:text-3xl font-black neon-text">{{ number_format($visits['today']) }}</div>
                    <div class="text-[10px] uppercase tracking-widest text-base/50 mt-1">Hoy</div>
                </div>
                <div class="text-center rounded-lg border border-neutral bg-background/60 py-3">
                    <div class="text-2xl md:text-3xl font-black neon-text-accent">{{ number_format($visits['total']) }}</div>
                    <div class="text-[10px] uppercase tracking-widest text-base/50 mt-1">Total</div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif
</section>
