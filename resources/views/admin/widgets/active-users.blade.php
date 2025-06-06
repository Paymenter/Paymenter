<x-filament-widgets::widget>
    <x-filament::section heading="Active Users">
        @foreach($sessions as $session)
            @if(!$session->user)
                @continue
            @endif
            <div class="flex flex-row justify-between w-full">
                <div>
                    {{-- Username to user edit pagecopy thingy --}}
                    <a href="{{ \App\Admin\Resources\UserResource::getUrl('edit', ['record' => $session->user]) }}" wire:navigate>
                        <h2 class="font-bold hover:text-primary-500 transition-colors">
                            {{ $session->user->name }}
                        </h2>
                    </a>
                    
                    <!-- Email with copy functionality -->
                    <div 
                        class="text-sm mb-2 cursor-pointer hover:text-primary-500 transition-colors"
                        x-data="{ copied: false }"
                        x-on:click="
                            navigator.clipboard.writeText('{{ $session->user->email }}');
                            copied = true;
                            setTimeout(() => copied = false, 2000);
                        "
                        x-text="copied ? 'Copied!' : '{{ $session->user->email }}'"
                    ></div>
                </div>
                <div>
                    <p class="text-sm text-base text-right">{{ $session->last_activity->diffForHumans() }}</p>
                    
                    <!-- IP address with copy functionality -->
                    <p 
                        class="text-sm text-right cursor-pointer hover:text-primary-500 transition-colors"
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
