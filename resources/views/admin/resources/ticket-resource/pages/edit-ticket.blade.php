<x-filament-panels::page>
    <div class="md:grid grid-cols-4 flex flex-col gap-4" wire:poll.5s>
        <div class="md:col-span-3">
            <div class="flex flex-col gap-4 max-h-[60vh] overflow-y-auto pr-4">
                @foreach ($this->record->messages()->with('user')->get() as $index => $message)
                    <div
                        class="bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg w-full max-w-[80%]  {{ $message->user_id === $this->record->user_id ? 'ml-auto' : 'mr-auto' }}"
                        @if ($loop->last) x-data x-init="$el.scrollIntoView()" @endif>
                        <div class="flex justify-between">
                            <div>
                                <a class="text-lg font-semibold hover:underline"
                                    href="{{ App\Admin\Resources\UserResource::getUrl('edit', ['record' => $message->user]) }}">
                                    {{ $message->user->name }}
                                </a>
                            </div>
                            <div>
                                @can('delete', $message)
                                <button wire:click="deleteMessage({{ $message->id }})"
                                    class="dark:text-danger-300 text-danger-600 p-0">
                                    Delete
                                </button>
                                @endcan
                            </div>
                        </div>
                        <p class="text-sm text-gray-500">{{ $message->created_at->diffForHumans() }}</p>
                        <div class="mt-2 prose dark:prose-invert" style="word-break: break-all;">{!! Str::markdown(e($message->message), [
                            'allow_unsafe_links' => false,
                            'renderer' => [
                                'soft_break' => "<br>"
                            ]
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
