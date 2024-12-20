<x-filament-widgets::widget>
    <x-filament::section heading="Active Users">
        @foreach($sessions as $session)
        <a href="{{ \App\Admin\Resources\UserResource::getUrl('edit', ['record' => $session->user]) }}" wire:navigate class="w-fit flex flex-row justify-between">
            <div class="bg-primary-800 rounded-md">
                <h2 class="font-bold">{{ $session->user->name }}</h2>
                <div class="text-sm mb-2">
                    {{ $session->user->email }}
                </div>
            </div>
            <div>
                <p class="text-sm text-gray-400 text-right">{{ $session->last_activity->diffForHumans() }}</p>
                <p class="text-sm text-right">{{ $session->ip_address }}</p>
            </div>  
        </a>
        @endforeach
    </x-filament::section>
</x-filament-widgets::widget>