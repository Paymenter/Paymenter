<div class="space-y-4">
    <div class="text-lg font-bold pb-4">{{ __('services.services') }}</div>
    @foreach ($services as $service)
    <a href="{{ route('services.show', $service) }}" wire:navigate>
        <div class="bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg mb-4">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
            <div class="bg-secondary/10 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-secondary" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 1L21.5 6.5V17.5L12 23L2.5 17.5V6.5L12 1ZM5.49388 7.0777L12.0001 10.8444L18.5062 7.07774L12 3.311L5.49388 7.0777ZM4.5 8.81329V16.3469L11.0001 20.1101V12.5765L4.5 8.81329ZM13.0001 20.11L19.5 16.3469V8.81337L13.0001 12.5765V20.11Z"></path>
                </svg>
            </div>
            <span class="font-medium">{{ $service->product->name }}</span>
            </div>
            <div class="w-5 h-5 rounded-md p-0.5
                @if ($service->status == 'active') text-success bg-success/20 
                @elseif($service->status == 'suspended') text-info bg-info/20
                @else text-warning bg-warning/20 
                @endif"
                @if ($service->status == 'active')
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM11.0026 16L18.0737 8.92893L16.6595 7.51472L11.0026 13.1716L8.17421 10.3431L6.75999 11.7574L11.0026 16Z"></path>
                    </svg>
                @elseif($service->status == 'suspended')
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM8.52313 7.10891C8.25459 7.30029 7.99828 7.51644 7.75736 7.75736C7.51644 7.99828 7.30029 8.25459 7.10891 8.52313L15.4769 16.8911C15.7454 16.6997 16.0017 16.4836 16.2426 16.2426C16.4836 16.0017 16.6997 15.7454 16.8911 15.4769L8.52313 7.10891Z"></path>
                    </svg>
                @elseif($service->status == 'pending')
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM11 15V17H13V15H11ZM11 7V13H13V7H11Z"></path>
                    </svg>
                @endif
            </div>
        </div>
        <p class="text-base text-sm">Product(s): {{ $service->product->category->name }} - Every {{ $service->plan->billing_period > 1 ? $service->plan->billing_period : '' }}
                            {{ Str::plural($service->plan->billing_unit, $service->plan->billing_period) }}</p>
        </div>
    </a>
    @endforeach
</div>
