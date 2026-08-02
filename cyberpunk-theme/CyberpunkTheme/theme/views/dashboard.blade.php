@php
$user = Auth::user();
$activeServices = $user->services()->where('status', 'active')->count();
$pendingInvoices = $user->invoices()->where('status', 'pending')->get();
$openTickets = config('settings.tickets_disabled', false) ? 0 : $user->tickets()->where('status', '!=', 'closed')->count();
$dueTotal = $pendingInvoices->count();
$nextExpiring = $user->services()->where('status', 'active')->whereNotNull('expires_at')->orderBy('expires_at')->first();
@endphp

<div class="container mt-10 pb-10">
    {{-- Cabecera --}}
    <div class="cyber-card cyber-clip p-6 md:p-8 relative overflow-hidden">
        <div class="absolute inset-0 cyber-gradient opacity-[0.07] pointer-events-none"></div>
        <div class="relative flex flex-col md:flex-row md:items-center gap-6 justify-between">
            <div class="flex items-center gap-4">
                <img src="{{ cyber_avatar($user) }}" alt="avatar"
                    class="size-16 rounded-xl border-2 border-primary/60 object-cover cyber-neon">
                <div>
                    <p class="text-xs uppercase tracking-widest text-base/50">Bienvenido de nuevo</p>
                    <h1 class="text-2xl md:text-3xl font-black cyber-neon-text">
                        <span class="cyber-glitch" data-text="{{ $user->name }}">{{ $user->name }}</span>
                    </h1>
                    <p class="text-sm text-base/55 mt-1">{{ __('dashboard.dashboard_description') }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('services') }}" wire:navigate>
                    <x-button.secondary class="!w-fit">
                        <x-ri-archive-stack-fill class="size-4" />
                        {{ __('navigation.services') }}
                    </x-button.secondary>
                </a>
                @if(!config('settings.tickets_disabled', false))
                <a href="{{ route('tickets.create') }}" wire:navigate>
                    <x-button.primary class="!w-fit">
                        <x-ri-customer-service-2-fill class="size-4" />
                        {{ __('navigation.tickets') }}
                    </x-button.primary>
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Resumen rápido --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
        <x-cyber.stat-card icon="ri-server-fill" :value="$activeServices" label="Servicios activos" />
        <x-cyber.stat-card icon="ri-bill-fill" :value="$dueTotal" label="Facturas por pagar" />
        <x-cyber.stat-card icon="ri-customer-service-2-fill" :value="$openTickets" label="Tickets abiertos" />
        <div class="cyber-card cyber-clip p-5">
            <div class="p-2.5 rounded-lg bg-accent/10 border border-accent/25 w-fit">
                <x-ri-calendar-schedule-fill class="size-5 text-accent" />
            </div>
            <div class="mt-4 text-lg font-bold">
                @if($nextExpiring)
                {{ $nextExpiring->expires_at->format('d/m/Y') }}
                @else
                —
                @endif
            </div>
            <div class="mt-1 text-xs uppercase tracking-widest text-base/55">
                @if($nextExpiring)
                Próxima renovación
                @else
                Sin renovaciones
                @endif
            </div>
        </div>
    </div>

    {{-- Aviso de facturas pendientes con el servidor al que pertenecen --}}
    @if($pendingInvoices->count() > 0)
    <div class="mt-6 cyber-card cyber-clip border-warning/50 p-6">
        <div class="flex items-center gap-3">
            <div class="p-2 rounded-lg bg-warning/15 border border-warning/40">
                <x-ri-alarm-warning-fill class="size-5 text-warning" />
            </div>
            <div>
                <h2 class="text-lg font-bold">Tienes {{ $pendingInvoices->count() }} {{ $pendingInvoices->count() === 1 ? 'factura pendiente' : 'facturas pendientes' }}</h2>
                <p class="text-sm text-base/60">Aquí te indicamos a qué servidor pertenece cada una.</p>
            </div>
        </div>

        <div class="mt-5 space-y-3">
            @foreach($pendingInvoices->take(5) as $invoice)
            @php $related = cyber_invoice_services($invoice); @endphp
            <a href="{{ route('invoices.show', $invoice) }}" wire:navigate
                class="block rounded-xl border border-neutral bg-background/50 p-4 hover:border-primary/60 transition">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <x-ri-bill-line class="size-5 text-primary" />
                        <span class="font-semibold">
                            {{ !$invoice->number && config('settings.invoice_proforma', false)
                                ? __('invoices.proforma_invoice', ['id' => $invoice->id])
                                : __('invoices.invoice', ['id' => $invoice->number]) }}
                        </span>
                        <span class="text-sm text-base/60">{{ $invoice->formattedTotal }}</span>
                    </div>
                    @if($invoice->due_at)
                    <span class="text-xs px-2.5 py-1 rounded-md border border-warning/40 bg-warning/10 text-warning font-semibold">
                        Vence {{ $invoice->due_at->format('d/m/Y') }}
                    </span>
                    @endif
                </div>

                @if(count($related) > 0)
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($related as $service)
                    <span class="inline-flex items-center gap-1.5 text-xs rounded-md border border-primary/35 bg-primary/10 px-2.5 py-1 text-primary font-semibold">
                        <x-ri-instance-fill class="size-3.5" />
                        Servidor: {{ $service['label'] }}
                        @if($service['product'])
                        <span class="text-base/50 font-normal">({{ $service['product'] }})</span>
                        @endif
                    </span>
                    @endforeach
                </div>
                @else
                <div class="mt-3 text-xs text-base/50">
                    @foreach($invoice->items->take(3) as $item)
                    <span class="mr-2">• {{ $item->description }}</span>
                    @endforeach
                </div>
                @endif
            </a>
            @endforeach
        </div>

        @if($pendingInvoices->count() > 5)
        <a href="{{ route('invoices') }}" wire:navigate class="mt-4 inline-flex text-sm font-semibold text-primary items-center gap-1.5">
            Ver todas <x-ri-arrow-right-line class="size-4" />
        </a>
        @endif
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8 items-start">
        <div class="grid gap-8 items-start">
            <!-- Servicios activos -->
            <div class="cyber-card cyber-clip p-6">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="bg-primary/10 border border-primary/25 p-2 rounded-lg">
                            <x-ri-archive-stack-fill class="size-5 text-primary" />
                        </div>
                        <h2 class="text-lg font-bold">{{ __('dashboard.active_services') }}</h2>
                    </div>
                    <span class="bg-primary flex items-center justify-center font-bold rounded-md size-6 text-sm text-white">
                        {{ $activeServices }}
                    </span>
                </div>
                <div class="space-y-4">
                    <livewire:services.widget status="active" />
                </div>
                <a href="{{ route('services') }}" wire:navigate class="mt-4 block">
                    <x-button.secondary>
                        {{ __('dashboard.view_all') }}
                        <x-ri-arrow-right-fill class="size-4" />
                    </x-button.secondary>
                </a>
            </div>

            <!-- Tickets -->
            @if(!config('settings.tickets_disabled', false))
            <div class="cyber-card cyber-clip p-6">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="bg-accent/10 border border-accent/25 p-2 rounded-lg">
                            <x-ri-customer-service-fill class="size-5 text-accent" />
                        </div>
                        <h2 class="text-lg font-bold">{{ __('dashboard.open_tickets') }}</h2>
                        <a href="{{ route('tickets.create') }}" wire:navigate class="text-primary hover:text-primary/70">
                            <x-ri-add-circle-fill class="size-5" />
                        </a>
                    </div>
                    <span class="bg-accent flex items-center justify-center font-bold rounded-md size-6 text-sm text-white">
                        {{ $openTickets }}
                    </span>
                </div>
                <div class="space-y-4">
                    <livewire:tickets.widget />
                </div>
                <a href="{{ route('tickets') }}" wire:navigate class="mt-4 block">
                    <x-button.secondary>
                        {{ __('dashboard.view_all') }}
                        <x-ri-arrow-right-fill class="size-4" />
                    </x-button.secondary>
                </a>
            </div>
            @endif
        </div>

        <div class="grid gap-8 items-start">
            <!-- Facturas -->
            <div class="cyber-card cyber-clip p-6">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="bg-secondary/10 border border-secondary/25 p-2 rounded-lg">
                            <x-ri-receipt-fill class="size-5 text-secondary" />
                        </div>
                        <h2 class="text-lg font-bold">{{ __('dashboard.unpaid_invoices') }}</h2>
                    </div>
                    <span class="bg-secondary flex items-center justify-center font-bold rounded-md size-6 text-sm text-white">
                        {{ $dueTotal }}
                    </span>
                </div>
                <div class="space-y-4">
                    <livewire:invoices.widget :limit="3" />
                </div>
                <a href="{{ route('invoices') }}" wire:navigate class="mt-4 block">
                    <x-button.secondary>
                        {{ __('dashboard.view_all') }}
                        <x-ri-arrow-right-fill class="size-4" />
                    </x-button.secondary>
                </a>
            </div>

            @if(cyber_bool('socials_enabled', true) && count(cyber_socials()) > 0)
            <div class="cyber-card cyber-clip p-6">
                <h2 class="text-lg font-bold mb-4">Síguenos</h2>
                <x-cyber.socials :compact="true" />
            </div>
            @endif

            {!! hook('pages.dashboard') !!}
        </div>
    </div>
</div>
