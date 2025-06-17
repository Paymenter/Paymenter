{{-- Had to put a lot outside the <a> since it would always trigger redirect, layout shouldn't be changed at all --}}
<x-filament-widgets::widget>
    <x-filament::section heading="Active Users ({{ $onlineCount }})">
        @foreach($sessions as $session)
            @if(!$session->user)
                @continue
            @endif
            <div class="flex flex-row justify-between w-full items-center space-x-4">
                <a href="{{ \App\Admin\Resources\UserResource::getUrl('edit', ['record' => $session->user]) }}" wire:navigate class="flex-grow">
                    <div>
                        <h2 class="font-bold">{{ $session->user->name }}</h2>
                        <div class="text-sm mb-2">
                            {{ $session->user->email }}
                        </div>
                    </div>
                </a>
                <div class="text-right min-w-[140px]">
                    <p class="text-sm text-base">{{ $session->last_activity->diffForHumans() }}</p>
                    <p 
                        class="text-xs text-gray-400 dark:text-gray-500 cursor-pointer hover:text-primary-500 transition-colors select-none"
                        x-data="{ copied: false }"
                        x-on:click="
                            navigator.clipboard.writeText('{{ $session->ip_address }}');
                            copied = true;
                            setTimeout(() => copied = false, 2000);
                        "
                        x-text="copied ? 'Copied!' : '{{ $session->ip_address }}'"
                    ></p>
                </div>
            </div>
        @endforeach
    </x-filament::section>
</x-filament-widgets::widget>
