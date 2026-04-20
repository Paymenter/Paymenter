@props(['service'])

@php
    $statusClass = match(true) {
        $service->status === 'active'                                      => 'text-success bg-success/20',
        in_array($service->status, ['suspended', 'cancelled'])             => 'text-inactive bg-inactive/20',
        default                                                            => 'text-warning bg-warning/20',
    };

    $billingDetail = in_array($service->plan->type, ['recurring'])
        ? __('services.every_period', [
            'period' => $service->plan->billing_period > 1 ? $service->plan->billing_period : '',
            'unit'   => trans_choice(__('services.billing_cycles.' . $service->plan->billing_unit), $service->plan->billing_period),
        ])
        : '';
@endphp

<x-entity-card :href="route('services.show', $service)" :statusClass="$statusClass">
    <x-slot:icon><x-ri-instance-line class="size-5 text-secondary" /></x-slot:icon>

    <x-slot:heading>
        <span class="font-medium">{{ $service->label }}</span>
    </x-slot:heading>

    <x-slot:status>
        @if ($service->status === 'active')
            <x-ri-checkbox-circle-fill />
        @elseif(in_array($service->status, ['suspended', 'cancelled']))
            <x-ri-forbid-fill />
        @else
            <x-ri-error-warning-fill />
        @endif
    </x-slot:status>

    <x-slot:detail>
        <div class="text-base/50 text-sm flex gap-1">
            {{ $billingDetail }}
            @if($service->expires_at && $service->expires_at > now())
                @if($billingDetail) - @endif
                {{ __('services.renews_in') }}
                <x-tooltip :message="$service->expires_at->format('M d, Y')">
                    {{ $service->expires_at->longAbsoluteDiffForHumans() }}
                </x-tooltip>
            @endif
        </div>
    </x-slot:detail>
</x-entity-card>
