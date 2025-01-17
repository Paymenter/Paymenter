<x-filament-panels::page>
    <div class="md:grid grid-cols-4 flex flex-col gap-4" wire:poll.5s>
        <div class="md:col-span-3">
            <div class="flex flex-col gap-4 max-h-[60vh] overflow-y-auto pr-4">
                @foreach ($this->record->messages()->orderBy('created_at', 'desc')->with('user')->get() as $message)
                    <div
                        class="bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg w-full max-w-[80%]  {{ $message->user_id === $this->record->user_id ? 'ml-auto' : 'mr-auto' }}">
                        <div class="flex justify-between">
                            <div>
                                <a class="text-lg font-semibold hover:underline"
                                    href="{{ App\Admin\Resources\UserResource::getUrl('edit', ['record' => $message->user]) }}">
                                    {{ $message->user->name }}
                                </a>
                            </div>
                            <div>
                                <button wire:click="deleteMessage({{ $message->id }})"
                                    class="dark:text-danger-300 text-danger-600 p-0">
                                    Delete
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500">{{ $message->created_at->diffForHumans() }}</p>
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

        <div class="md:order-last order-first w-full col-span-3 sm:col-auto">{{ $this->infolist }}</div>

    </div>
</x-filament-panels::page>
