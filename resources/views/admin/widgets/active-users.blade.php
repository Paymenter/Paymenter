<x-filament-widgets::widget>
    <x-filament::section heading="Active Users" class="!p-4">
        <div class="space-y-3">

            {{-- This was there before, should be kept i think --}}
            @foreach($sessions as $userSession)
                
                @if(!$userSession->user)
                    @continue
                @endif

        <div class="flex justify-between items-center w-full">
            <div class="flex-1 min-w-0 space-y-1">
                
                {{-- Username links to user edit page as Corwin wanted--}}
                <a 
                    href="{{ \App\Admin\Resources\UserResource::getUrl('edit', ['record' => $userSession->user]) }}" 
                    wire:navigate
                    class="font-bold truncate cursor-pointer hover:text-primary-500 transition-colors block"
                >
                    {{ $userSession->user->name }}
                </a>


                        {{-- Email copy stuff --}}
                        <p 
                            class="text-sm text-gray-500 dark:text-gray-400 truncate cursor-pointer hover:text-primary-500 transition-colors"
                            x-data="{ copied: false }"
                            x-on:click="
                                navigator.clipboard.writeText('{{ $userSession->user->email }}');
                                copied = true;
                                setTimeout(() => copied = false, 2000);
                            "
                            x-text="copied ? 'Copied!' : '{{ $userSession->user->email }}'"
                        ></p>
                    </div>

                    <div class="ml-4 text-right space-y-1">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $userSession->last_activity->diffForHumans() }}
                        </p>

                        {{-- IP address also copyable, might be a bit overkill.....--}}
                        <p 
                            class="text-xs text-gray-400 dark:text-gray-500 cursor-pointer hover:text-primary-500 transition-colors"
                            x-data="{ copied: false }"
                            x-on:click="
                                navigator.clipboard.writeText('{{ $userSession->ip_address }}');
                                copied = true;
                                setTimeout(() => copied = false, 2000);
                            "
                            x-text="copied ? 'Copied!' : '{{ $userSession->ip_address }}'"
                        ></p>
                    </div>  
                </div>
            @endforeach

            {{-- For the future: Please add pagination. I'm to lazy to add it --}}
            {{-- For now this will show like 4 users and their information --}}
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
