<x-filament-widgets::widget>
    <x-filament::section heading="Active Users">
        @foreach($sessions as $session)
        @if(!$session->user)
        @continue
        @endif
        <a href="{{ \App\Admin\Resources\UserResource::getUrl('edit', ['record' => $session->user]) }}" wire:navigate class="flex flex-row justify-between w-full">
            <div>
                <h2 class="font-bold">{{ $session->user->name }}</h2>
                <div class="text-sm mb-2">
                    {{ $session->user->email }}
                </div>
            </div>
            <div>
                <p class="text-sm text-base text-right">{{ $session->last_activity->diffForHumans() }}</p>
                <p class="text-sm text-right">{{ $session->ip_address }}</p>
            </div>  
        </a>
        @endforeach
    </x-filament::section>
</x-filament-widgets::widget>