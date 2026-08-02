<x-filament-panels::page>
    @if(!$themeActive)
        <div class="rounded-xl border border-amber-400/40 bg-amber-400/10 p-4 text-sm">
            <p class="font-semibold text-amber-600 dark:text-amber-400">El tema Cyberpunk todavía no está activo.</p>
            <p class="mt-1 opacity-80">Pulsa <strong>Activar tema</strong> arriba para que la tienda use este diseño.</p>
        </div>
    @endif

    @if(!$hasAssets)
        <div class="rounded-xl border border-red-400/40 bg-red-400/10 p-4 text-sm">
            <p class="font-semibold text-red-600 dark:text-red-400">Faltan los assets compilados del tema.</p>
            <p class="mt-1 opacity-80">
                Pulsa <strong>Reinstalar archivos</strong>, o compílalos manualmente con
                <code class="px-1 py-0.5 rounded bg-black/10 dark:bg-white/10">npm run build cyberpunk</code>
                en la raíz de Paymenter.
            </p>
        </div>
    @endif

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($palettes as $key => $palette)
            <div class="rounded-xl border border-gray-200 dark:border-white/10 p-3">
                <p class="text-xs font-semibold truncate">{{ $palette['label'] }}</p>
                <div class="mt-2 flex gap-1">
                    @foreach($palette['preview'] as $color)
                        <span class="h-5 flex-1 rounded" style="background: {{ $color }}"></span>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{ $this->form }}
</x-filament-panels::page>
