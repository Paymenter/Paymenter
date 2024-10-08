<div>
    <div class="bg-primary-800 p-6 rounded-lg mt-2">
        <div class="flex flex-col md:flex-row justify-between">
            <h1 class="text-2xl font-semibold text-white">{{ __('services.services') }}</h1>
        </div>

        <div class="grid md:grid-cols-2 gap-4 my-4">
            <div>
                <h4 class="text-lg font-semibold text-white">{{ __('services.product_details') }}:</h4>
                <div class="mt-2">
                    <div class="flex items-center text-gray-400">
                        <span class="mr-2">{{ __('services.name') }}:</span>
                        <span class="text-gray-300">{{ $service->product->name }}</span>
                    </div>
                    <div class="flex items-center text-gray-400">
                        <span class="mr-2">{{ __('services.price') }}:</span>
                        <span class="text-gray-300">{{ $service->formattedPrice }}</span>
                    </div>
                    <div class="flex items-center text-gray-400">
                        <span class="mr-2">{{ __('services.billing_cycle') }}:</span>
                        <span class="text-gray-300">Every {{ $service->plan->billing_period > 1 ? $service->plan->billing_period : '' }}
                            {{ Str::plural($service->plan->billing_unit, $service->plan->billing_period) }}</span>
                    </div>
                    <div class="flex items-center text-gray-400">
                        <span class="mr-2">{{ __('services.status') }}:</span>
                        <span
                            class="font-semibold @if ($service->status == 'active') text-green-500 @elseif($service->status == 'cancelled') text-red-500  @else text-orange-500 @endif">
                            {{ ucfirst($service->status) }}
                        </span>
                    </div>
                </div>
            </div>
            @if($service->cancellable || $service->upgradable || count($buttons) > 0)
            <div>
                <h4 class="text-lg font-semibold text-white">{{ __('services.actions') }}:</h4>
                <div class="mt-2 flex flex-row gap-2 flex-wrap">
                    @if($service->upgradable)
                        <x-button.primary class="h-fit !w-fit" wire:click="openModal('services.upgrade')">
                            <span wire:loading.remove wire:target="openModal('services.upgrade')">{{ __('services.upgrade') }}</span>
                            <x-loading target="openModal('services.upgrade')" />
                        </x-button.primary>
                    @endif
                    @if($service->cancellable)
                        <x-button.danger class="h-fit !w-fit" wire:click="openModal('services.cancel')">
                            <span wire:loading.remove wire:target="openModal('services.cancel')">{{ __('services.cancel') }}</span>
                            <x-loading target="openModal('services.cancel')" />
                        </x-button.danger>
                    @endif
                    @if($showModal != '')
                        <x-modal open="true" title="{{ __('services.' . ($showModal == 'services.upgrade' ? 'upgrade' : 'cancellation'), ['service' => $service->product->name]) }}" width="{{ $showModal == 'services.upgrade' ? 'max-w-5xl' : 'max-w-3xl' }}">
                            @if($showModal == 'services.upgrade')
                                <livewire:services.upgrade :service="$service" />
                            @elseif($showModal == 'services.cancel')
                                <livewire:services.cancel :service="$service" />
                            @endif
                            <x-slot name="closeTrigger">
                                <div class="flex gap-4">
                                    <button wire:confirm="Are you sure?" wire:click="openModal('')" @click="open = false"
                                        class="text-primary-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </x-slot>
                        </x-modal>
                    @endif
                </div>
                <div class="mt-2 flex flex-row gap-2 flex-wrap">
                    @foreach ($buttons as $button)
                        <a href="{{ $button['url'] }}">
                            <x-button.secondary class="h-fit !w-fit">
                                {{ $button['label'] }}
                            </x-button.secondary>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    @if (count($views) > 0)
        <div class="bg-primary-800 p-6 rounded-lg mt-2">
            @if (count($views) > 1)
                <div class="flex w-fit mb-2 flex-row flex-wrap">
                    @foreach ($views as $view)
                        <button wire:click="changeView('{{ $view['name'] }}')"
                            class="px-4 py-2 -mb-px focus:outline-none {{ $view['name'] == $currentView ? 'border-b-2 border-gray-400 text-white' : 'text-gray-400 border-b border-gray-500 ' }}">
                            {{ $view['label'] }}
                        </button>
                    @endforeach
                </div>
            @endif

            <!-- show loading spinner -->
            <x-loading target="changeView" />
            <div wire:loading.remove wire:target="changeView">
                {!! $extensionView !!}
            </div>
        </div>
    @endif
</div>
