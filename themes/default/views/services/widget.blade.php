<div class="space-y-4">
    @foreach ($services as $service)
    <a href="{{ route('services.show', $service) }}" wire:navigate>
        <div class="bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg mb-4">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
            <div class="bg-secondary/10 p-2 rounded-lg">
                <x-ri-instance-line class="size-5 text-secondary" />
            </div>
            <span class="font-medium">{{ $service->product->name }}</span>
            </div>
            <div class="size-5 rounded-md p-0.5
                @if ($service->status == 'active') text-success bg-success/20 
                @elseif($service->status == 'suspended') text-inactive bg-inactive/20
                @else text-warning bg-warning/20 
                @endif">
                @if ($service->status == 'active')
                    <x-ri-checkbox-circle-fill />
                @elseif($service->status == 'suspended')
                    <x-ri-forbid-fill />
                @elseif($service->status == 'pending')
                    <x-ri-error-warning-fill />
                @endif
            </div>
        </div>
        <p class="text-base text-sm">Product(s): {{ $service->product->category->name }} {{ in_array($service->plan->type, ['recurring']) ? ' - ' . __('services.every_period', [
            'period' => $service->plan->billing_period > 1 ? $service->plan->billing_period : '',
            'unit' => trans_choice(__('services.billing_cycles.' . $service->plan->billing_unit), $service->plan->billing_period)
        ]) : '' }} {{ $service->expires_at ? '- ' . __('services.expires_at') . ': '. $service->expires_at->format('M d, Y') : ''}}</p>
        </div>
    </a>
    @endforeach
</div>
