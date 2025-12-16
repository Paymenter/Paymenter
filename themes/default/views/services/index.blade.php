<div class="container mt-14 space-y-4">
    <x-navigation.breadcrumb />
    
    @forelse ($services as $service)
        <div class="bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg mb-4 relative group transition-all">
            
            <div style="position: absolute; top: 1rem; right: 1rem; z-index: 50; display: flex; align-items: center; gap: 0.75rem;">
                
                @if($editingServiceId !== $service->id)
                    <button wire:click.prevent.stop="editLabel({{ $service->id }}, '{{ $service->custom_label }}')" 
                            class="text-neutral-400 hover:text-white transition-colors"
                            title="Label bearbeiten">
                        <x-ri-pencil-line class="size-5" />
                    </button>
                @endif

                <button wire:click.prevent.stop="toggleFavorite({{ $service->id }})" class="transition-colors hover:scale-110">
                    @if($service->is_favorite)
                        <x-ri-star-fill class="size-6 text-yellow-400" />
                    @else
                        <x-ri-star-line class="size-6 text-neutral/50 hover:text-yellow-400" />
                    @endif
                </button>
            </div>

            @if($editingServiceId === $service->id)
                <div class="block pr-20 cursor-default">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <div class="bg-secondary/10 p-2 rounded-lg">
                                <x-ri-instance-line class="size-5 text-secondary" />
                            </div>
                            
                            <div class="flex flex-col z-40">
                                <div class="flex items-center gap-2" onclick="event.preventDefault(); event.stopPropagation();">
                                    <input type="text" 
                                           wire:model="customLabelInput" 
                                           wire:keydown.enter="saveLabel"
                                           class="bg-background-primary border border-neutral rounded px-2 py-1 text-sm text-white focus:outline-none focus:border-secondary w-full max-w-[200px]" 
                                           placeholder="Label Name..."
                                           autofocus>
                                    <button wire:click.prevent.stop="saveLabel" class="text-success hover:text-success/80">
                                        <x-ri-check-line class="size-5" />
                                    </button>
                                    <button wire:click.prevent.stop="cancelEdit" class="text-red-500 hover:text-red-400">
                                        <x-ri-close-line class="size-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex gap-2 mt-3">
                        <div class="flex items-center gap-1 text-xs rounded-md px-2 py-1 w-fit
                            @if ($service->status == 'active') text-success bg-success/20 
                            @elseif($service->status == 'suspended' || $service->status == 'cancelled') text-inactive bg-inactive/20
                            @else text-warning bg-warning/20 
                            @endif">
                            @if ($service->status == 'active')
                                <x-ri-checkbox-circle-fill class="size-3.5" /> Active
                            @elseif($service->status == 'suspended' || $service->status == 'cancelled')
                                <x-ri-forbid-fill class="size-3.5" /> {{ ucfirst($service->status) }}
                            @elseif($service->status == 'pending')
                                <x-ri-error-warning-fill class="size-3.5" /> Pending
                            @endif
                        </div>
                    </div>

                    <p class="text-base text-sm mt-2 text-neutral-300">
                        Product: {{ $service->product->category->name }}
                        - Purchased: {{ $service->created_at->format('M d, Y') }}
                        {{ $service->expires_at ? '- ' . __('services.expires_at') . ': '. $service->expires_at->format('M d, Y') : ''}}
                    </p>
                </div>

            @else
                <a href="{{ route('services.show', $service) }}" wire:navigate class="block pr-20">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <div class="bg-secondary/10 p-2 rounded-lg">
                                <x-ri-instance-line class="size-5 text-secondary" />
                            </div>
                            
                            <div class="flex flex-col justify-center">
                                @if($service->custom_label)
                                    <span class="font-bold text-lg text-primary leading-tight">{{ $service->custom_label }}</span>
                                    <span class="text-xs text-neutral-400">{{ $service->product->name }}</span>
                                @else
                                    <span class="font-medium text-lg">{{ $service->product->name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2 mt-3">
                        <div class="flex items-center gap-1 text-xs rounded-md px-2 py-1 w-fit
                            @if ($service->status == 'active') text-success bg-success/20 
                            @elseif($service->status == 'suspended' || $service->status == 'cancelled') text-inactive bg-inactive/20
                            @else text-warning bg-warning/20 
                            @endif">
                            @if ($service->status == 'active')
                                <x-ri-checkbox-circle-fill class="size-3.5" /> Active
                            @elseif($service->status == 'suspended' || $service->status == 'cancelled')
                                <x-ri-forbid-fill class="size-3.5" /> {{ ucfirst($service->status) }}
                            @elseif($service->status == 'pending')
                                <x-ri-error-warning-fill class="size-3.5" /> Pending
                            @endif
                        </div>
                    </div>

                    <p class="text-base text-sm mt-2 text-neutral-300">
                        Product: {{ $service->product->category->name }} 
                        - Purchased: {{ $service->created_at->format('M d, Y') }}
                        
                        {{
                            in_array($service->plan->type, ['recurring']) ? ' - ' . __('services.every_period', [
                            'period' => $service->plan->billing_period > 1 ? $service->plan->billing_period : '',
                            'unit' => trans_choice(__('services.billing_cycles.' . $service->plan->billing_unit),
                            $service->plan->billing_period)
                            ]) : '' }} 
                        {{ $service->expires_at ? '- ' . __('services.expires_at') . ': '. $service->expires_at->format('M d, Y') : ''}}
                    </p>
                </a>
            @endif

        </div>
    @empty
        <div class="bg-background-secondary border border-neutral p-4 rounded-lg">
            <p class="text-base text-sm">{{ __('services.no_services') }}</p>
        </div>
    @endforelse

    {{ $services->links() }}
</div>
