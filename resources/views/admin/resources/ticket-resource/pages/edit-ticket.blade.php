<x-filament-panels::page>
    <div class="grid grid-cols-4 gap-4" wire:poll.5s>
        <div class="col-span-3 ">
            <div class="flex flex-col gap-4 max-h-[60vh] overflow-y-auto pr-4">
                @foreach ($this->record->messages()->orderBy('created_at', 'desc')->with('user')->get() as $message)
                    <div
                        class="p-4 rounded-lg w-full bg-gray-900 max-w-[80%]  {{ $message->user_id === $this->record->user_id ? 'ml-auto' : 'mr-auto' }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold">{{ $message->user->name }}</h2>
                                <p class="text-sm text-gray-500">{{ $message->created_at->diffForHumans() }}</p>
                            </div>
                            <div>
                                <button wire:click="deleteMessage({{ $message->id }})" color="red" size="sm">
                                    Delete
                                </button>
                            </div>
                        </div>
                        <div class="mt-2 prose dark:prose-invert">{!! Str::markdown($message->message, [
                            'html_input' => 'strip',
                            'allow_unsafe_links' => false,
                        ]) !!}</div>
                    </div>
                @endforeach
            </div>
            <!-- Reply Form -->
            <div class="mt-4">
                <x-filament-panels::form id="form"
                    :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()" wire:submit="save">

                    {{ $this->form }}

                    <x-filament-panels::form.actions :actions="$this->getCachedFormActions()" :full-width="$this->hasFullWidthFormActions()" />
                </x-filament-panels::form>
            </div>
        </div>

        {{ $this->infolist }}

    </div>
</x-filament-panels::page>
