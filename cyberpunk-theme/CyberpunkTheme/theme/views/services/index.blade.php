<div class="container mt-10 pb-10 space-y-4">
    <x-navigation.breadcrumb />

    <div class="cyber-card cyber-clip p-5 flex flex-wrap items-center gap-4 justify-between">
        <div class="flex items-center gap-3">
            <div class="p-2.5 rounded-lg bg-primary/10 border border-primary/25">
                <x-ri-server-fill class="size-5 text-primary" />
            </div>
            <div>
                <h1 class="text-xl font-black">{{ __('navigation.services') }}</h1>
                <p class="text-sm text-base/55">Estado, renovación y facturas de cada servidor.</p>
            </div>
        </div>
    </div>

    @forelse ($services as $service)
    @php $pending = $service->invoices()->where('status', 'pending')->first(); @endphp
    <a href="{{ route('services.show', $service) }}" wire:navigate class="block">
        <div class="cyber-card cyber-card-hover cyber-clip p-5 mb-4">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                <div class="flex items-center gap-3">
                    <div class="bg-secondary/10 border border-secondary/25 p-2 rounded-lg">
                        <x-ri-instance-line class="size-5 text-secondary" />
                    </div>
                    <div>
                        <span class="font-bold block">{{ $service->label }}</span>
                        <span class="text-xs text-base/50">{{ $service->product?->name }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs px-2.5 py-1 rounded-md font-semibold border
                        @if ($service->status == 'active') text-success bg-success/10 border-success/35
                        @elseif($service->status == 'suspended' || $service->status == 'cancelled') text-inactive bg-inactive/10 border-inactive/35
                        @else text-warning bg-warning/10 border-warning/35 @endif">
                        {{ __('services.statuses.' . $service->status) }}
                    </span>
                    <div class="size-6 rounded-md p-0.5
                        @if ($service->status == 'active') text-success bg-success/20
                        @elseif($service->status == 'suspended' || $service->status == 'cancelled') text-inactive bg-inactive/20
                        @else text-warning bg-warning/20 @endif">
                        @if ($service->status == 'active')
                            <x-ri-checkbox-circle-fill />
                        @elseif($service->status == 'suspended' || $service->status == 'cancelled')
                            <x-ri-forbid-fill />
                        @else
                            <x-ri-error-warning-fill />
                        @endif
                    </div>
                </div>
            </div>

            <div class="text-sm text-base/60 flex flex-wrap items-center gap-x-2 gap-y-1">
                <span class="font-mono text-base/75">{{ $service->formattedPrice }}</span>
                @if(in_array($service->plan->type, ['recurring']))
                <x-ri-circle-fill class="size-1 text-base/20" />
                <span>{{ __('services.every_period', [
                    'period' => $service->plan->billing_period > 1 ? $service->plan->billing_period : '',
                    'unit' => trans_choice(__('services.billing_cycles.' . $service->plan->billing_unit), $service->plan->billing_period)
                ]) }}</span>
                @endif
                @if($service->expires_at && $service->expires_at > now())
                <x-ri-circle-fill class="size-1 text-base/20" />
                <span>{{ __('services.renews_in') }}
                    <x-tooltip :message="$service->expires_at->format('M d, Y')">
                        <span class="text-primary font-semibold">{{ $service->expires_at->longAbsoluteDiffForHumans() }}</span>
                    </x-tooltip>
                </span>
                @endif
            </div>

            @if($pending)
            <div class="mt-3 inline-flex items-center gap-2 text-xs rounded-md border border-warning/40 bg-warning/10 px-3 py-1.5 text-warning font-semibold">
                <x-ri-alarm-warning-fill class="size-4" />
                Factura pendiente de este servidor
                @if($pending->due_at) · vence {{ $pending->due_at->format('d/m/Y') }} @endif
            </div>
            @endif
        </div>
    </a>
    @empty
    <div class="cyber-card cyber-clip p-8 text-center">
        <x-ri-inbox-2-line class="size-10 mx-auto text-base/30" />
        <p class="mt-3 text-base/60">{{ __('services.no_services') }}</p>
    </div>
    @endforelse

    {{ $services->links() }}
</div>
