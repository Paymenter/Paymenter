<x-filament-panels::page>
    <div class="md:grid grid-cols-4 flex flex-col gap-4" wire:poll.10s>
        <div class="md:col-span-3">
            <div class="flex flex-col gap-4 max-h-[60vh] overflow-y-auto pr-4">
                @foreach ($this->record->messages()->with(['user', 'ticketMailLog', 'attachments'])->get() as $index => $message)
                    <div
                        class="border border-gray-100 dark:border-gray-600 p-4 rounded-lg w-full max-w-[80%]  {{ $message->user_id === $this->record->user_id ? 'ml-auto' : 'mr-auto' }}"
                        @if ($loop->last) x-data x-init="$el.scrollIntoView()" @endif>
                        <div class="flex justify-between">
                            <div class="flex gap-2 items-center">
                                <a class="text-lg font-semibold hover:underline"
                                    href="{{ App\Admin\Resources\UserResource::getUrl('edit', ['record' => $message->user]) }}">
                                    {{ $message->user->name }}
                                </a>
                                @if($message->ticketMailLog)
                                    <x-filament::modal width="4xl">
                                        <x-slot name="trigger">
                                            <span class="text-sm text-gray-500 cursor-pointer hover:underline">
                                                Imported from email 
                                            </span>
                                        </x-slot>
                                        <x-slot name="heading">
                                            {{ $message->ticketMailLog->subject }}
                                        </x-slot>

                                        {!! Str::markdown(e($message->ticketMailLog->body), [
                                            'allow_unsafe_links' => false,
                                            'renderer' => [
                                                'soft_break' => "<br>"
                                            ]
                                        ]) !!}
                                    </x-filament::modal>
                                @endif
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
                        <div class="mt-2 prose dark:prose-invert break-words overflow-x-auto max-w-full">
                            {!! Str::markdown($message->message, [
                                'html_input' => 'escape',
                                'allow_unsafe_links' => false,
                                'renderer' => [
                                    'soft_break' => "<br>"
                                ]
                            ]) !!}
                        </div>
                        <div class="flex flex-wrap gap-x-2">
                        @foreach($message->attachments as $attachment)
                            <div class="mt-2">
                                <a href="{{ route('tickets.attachments.show', $attachment) }}" class="text-sm rounded-md bg-gray-100 flex items-center dark:bg-gray-800 p-1 w-fit">
                                    @if($attachment->canPreview())
                                        <img src="{{ route('tickets.attachments.show', $attachment) }}" alt="{{ $attachment->filename }}" class="max-w-full">
                                    @else
                                        <x-ri-attachment-2 class="inline-block mr-1 size-4" />
                                        {{ $attachment->filename }}
                                    @endif
                                </a>
                            </div>
                        @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <form wire:submit="send">
                <div class="mt-4 mb-4">
                    {{ $this->form }}
                </div>
                <div class="flex flex-wrap gap-2 justify-end">
                    @if ($this->closeTicket->isVisible())
                        {{ $this->closeTicket }}
                    @endif
                    
                    <x-filament::button type="submit" target="send">
                        Send Message
                    </x-filament::button>
                </div>
            </form>
        </div>

        <div class="md:order-last order-first w-full col-span-3 sm:col-auto">{{ $this->infolist }}</div>

    </div>
</x-filament-panels::page>
