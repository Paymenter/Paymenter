<div>
    {{-- In work, do what you enjoy. --}}
    <x-dropdown width="w-84" :showArrow="false">
        <x-slot:trigger>
            <div class="relative w-10 h-10 flex items-center justify-center rounded-lg hover:bg-neutral cursor-pointer transition" x-data="{ hasNew: false }" x-on:new-notification.window="hasNew = true"
                @click="hasNew = false">
                <x-ri-notification-3-fill class="size-4" ::class="{'animate-wiggle': hasNew}"/>
                @if($this->notifications->where('read_at', null)->count() > 0)
                <span
                    class="absolute top-0 right-0 w-4 h-4 inline-flex items-center justify-center text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">
                    {{ $this->notifications->where('read_at', null)->count() }}
                </span>
                @endif
            </div>
        </x-slot:trigger>
        <x-slot:content>
            <div class="w-full max-h-96 overflow-y-auto">
                @if ($this->notifications->isEmpty())
                <div class="p-4 text-center text-sm text-base/80">
                    {{ __('No new notifications') }}
                </div>
                @else
                @foreach ($this->notifications as $notification)
                <div wire:click="goToNotification({{ $notification->id }})"
                    class="block px-4 py-3 hover:bg-background-secondary/50 cursor-pointer @if (!$loop->last) border-b border-neutral/50 @endif">
                    <div class="flex items-start gap-3">
                        <x-ri-notification-3-fill
                            class="size-5 mt-1 flex-shrink-0 {{ $notification->read_at ? 'text-base/80' : 'text-primary' }}" />
                        <div class="flex flex-col">
                            <span class="font-medium">{{ $notification->title }}</span>
                            <span class="text-sm text-base/80">{{ $notification->body }}</span>
                            <div class="flex flex-row justify-between mt-1 text-xs text-base/60">
                                <p>
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>

                                <button wire:click.stop="markAsRead({{ $notification->id }})" class="cursor-pointer"
                                    type="button">
                                    {{ __('Mark as read') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </x-slot:content>
    </x-dropdown>
</div>
