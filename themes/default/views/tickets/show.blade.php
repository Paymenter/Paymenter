<div class="container mt-14">
    <div class="bg-primary-800 p-6 rounded-lg mt-2">
        <h1 class="text-2xl font-semibold">Ticket #{{ $ticket->id }} - {{ $ticket->subject }}</h1>

        <div class="md:grid grid-cols-4 flex flex-col gap-4">
            <div class="md:col-span-3">
                <div class="flex flex-col gap-4 max-h-[60vh] overflow-y-auto pr-4" wire:poll.10s>
                    @foreach ($ticket->messages()->with('user')->get() as $index => $message)
                    <div class="bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg w-full max-w-[80%]  {{ $message->user_id === $ticket->user_id ? 'ml-auto' : 'mr-auto' }}"
                        @if ($loop->last) x-data x-init="$nextTick(() => $el.scrollIntoView({ block: 'end' }))" @endif>
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold">{{ $message->user->name }}</h2>
                                <p class="text-sm text-gray-500">{{ $message->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="mt-2 prose dark:prose-invert break-words max-w-full">{!! Str::markdown($message->message, [
                            'html_input' => 'escape',
                            'allow_unsafe_links' => false,
                            'renderer' => [
                            'soft_break' => "<br>"
                            ]
                            ]) !!}</div>
                        <div class="flex flex-wrap gap-x-2">
                            @foreach($message->attachments as $attachment)
                            <div class="mt-2">
                                <a href="{{ route('tickets.attachments.show', $attachment) }}"
                                    class="text-sm rounded-md bg-gray-100 flex items-center dark:bg-gray-800 p-1 w-fit">
                                    @if($attachment->canPreview())
                                    <img src="{{ route('tickets.attachments.show', $attachment) }}"
                                        alt="{{ $attachment->filename }}" class="max-w-full">
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
                <!-- Reply Form -->
                <div class="mt-4">
                    <form wire:submit.prevent="save">
                        <label for="editor" class="block text-sm font-medium text-primary-100">
                            {{ __('ticket.reply') }}
                        </label>
                        <div wire:ignore>
                            <textarea id="editor"></textarea>
                        </div>
                        <label for="attachments" class="block text-sm font-medium text-primary-100 mt-2">
                            {{ __('ticket.attachments') }}
                        </label>
                        <div x-data="{
                            drop: false,
                            selectedFiles: [],
                            progress: 0,
                            uploading: false,
                            handleDrop(event) {
                                this.drop = false;
                                if (event.dataTransfer.files && event.dataTransfer.files.length > 0) {
                                    this.selectedFiles = Array.from(event.dataTransfer.files);
                                    this.$refs.fileInput.files = event.dataTransfer.files;

                                    // Trigger the change event to update Livewire
                                    this.$refs.fileInput.dispatchEvent(new Event('change'));
                                }
                            },
                            init() {
                                this.$watch('$wire.attachments', (value) => {
                                    if (value.length == 0) {
                                        this.selectedFiles = [];
                                    }
                                });
                            }                            
                        }"
                            x-on:livewire-upload-start="uploading = true"
                            x-on:livewire-upload-finish="uploading = false; progress = 0;"
                            x-on:livewire-upload-progress="progress = $event.detail.progress"
                            x-on:livewire-upload-error="uploading = false; selectedFiles = []; progress = 0"
                            x-on:livewire-upload-cancel="uploading = false; progress = 0;">
                            <div class="flex justify-center rounded-lg bg-background-secondary border border-dashed border-neutral px-6 py-2"
                                @dragover.prevent="drop = true" @dragleave.prevent="drop = false"
                                @drop.prevent="handleDrop($event)" :class="{'bg-background-secondary/50': drop}">
                                    <!-- Upload Progress Bar -->
                                <div x-show="uploading" class="w-full text-center">
                                    <div class="mb-2 text-sm font-medium text-primary-100">
                                        {{ __('ticket.uploading_files') }}... (<span x-text="progress"></span>%)
                                    </div>
                                    <div class="w-full bg-primary-600 rounded-lg h-3 mb-4">
                                        <div class="bg-success h-3 rounded-lg" :style="{ width: `${progress}%` }"></div>
                                    </div>
                                </div>
                                <template x-if="selectedFiles.length === 0 && !uploading">
                                    <div class="text-center">
                                        <div class="flex text-sm text-primary-100">
                                            <label for="attachments"
                                                class="relative cursor-pointer rounded-md font-semibold text-primary hover:text-primary/80">
                                                <span>
                                                    {{ __('ticket.upload_attachments') }}
                                                </span>
                                            </label>
                                            <p class="pl-1 text-base/80">{{ __('ticket.or_drag_and_drop') }}</p>
                                        </div>
                                        <p class="text-xs/5 text-base/50">{{ __('ticket.files_max') }}</p>
                                    </div>
                                </template>
                                <div x-show="selectedFiles.length > 0 && !uploading" class="mt-2">
                                    <h4 class="text-sm font-semibold">{{ __('ticket.selected_files') }}:</h4>
                                    <div class="flex flex-wrap items-center justify-center gap-2 mt-1">
                                        <template x-for="file in selectedFiles" :key="file.name">
                                            <div
                                                class="text-sm rounded-md bg-gray-100 flex items-center gap-2 dark:bg-gray-800 p-1 py-0 w-fit">
                                                <span class="flex-1" x-text="file.name"></span>
                                                <button type="button"
                                                    class="text-red-500 hover:text-red-700 text-lg h-fit"
                                                    @click="selectedFiles = selectedFiles.filter(f => f !== file); $refs.fileInput.value = ''">
                                                    &times;
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <input id="attachments" type="file" multiple name="attachments[]" class="sr-only"
                                wire:model.live="attachments" x-ref="fileInput"
                                @change="selectedFiles = Array.from($event.target.files)" />
                        </div>
                        @error('attachments.*')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                        <div class="mt-2 flex flex-col sm:flex-row gap-2 justify-end">
                            @if (!config('settings.ticket_client_closing_disabled', false) && $ticket->status !== 'closed')
                                <x-button.danger type="button" class="sm:!w-fit order-2 sm:order-1"
                                    x-on:click.prevent="$store.confirmation.confirm({
                                        title: '{{ __('ticket.close_ticket') }}',
                                        message: '{{ __('ticket.close_ticket_confirmation') }}',
                                        confirmText: '{{ __('common.confirm') }}',
                                        cancelText: '{{ __('common.cancel') }}',
                                        callback: () => $wire.closeTicket()
                                    })">
                                    {{ __('ticket.close_ticket') }}
                                </x-button.danger>
                            @endif
                            <x-button.primary type="submit" class="sm:!w-fit order-1 sm:order-2" wire:target="save">
                                {{ __('ticket.reply') }}
                            </x-button.primary>
                        </div>
                    </form>
                    <x-easymde-editor />
                </div>

            </div>

            <div class="md:order-last order-first w-full col-span-3 sm:col-auto">
                <!-- Show subject and status -->
                <div class="flex flex-col w-full col-span-1">
                    <h2 class="text-2xl font-semibold bg-primary-800 p-2 px-4 rounded-md mb-2">
                        {{ __('ticket.ticket_details') }}
                    </h2>
                    <div class="font-semibold flex md:flex-col justify-between bg-primary-800 p-2 px-4 rounded-md gap-2">
                        <h4 class="h-fit">{{ __('ticket.subject') }}:</h4> {{ $ticket->subject }}
                    </div>
                    <div class="font-semibold flex md:flex-col justify-between bg-primary-800 p-2 px-4 rounded-md">
                        <h4>{{ __('ticket.status') }}:</h4> {{ ucfirst($ticket->status) }}
                    </div>
                    <div class="font-semibold flex md:flex-col justify-between bg-primary-800 p-2 px-4 rounded-md">
                        <h4>{{ __('ticket.priority') }}:</h4> {{ ucfirst($ticket->priority) }}
                    </div>
                    <div class="font-semibold flex md:flex-col justify-between bg-primary-800 p-2 px-4 rounded-md">
                        <h4>{{ __('ticket.created_at') }}:</h4> {{ $ticket->created_at->diffForHumans() }}
                    </div>
                    @if ($ticket->department)
                    <div class="font-semibold flex md:flex-col justify-between bg-primary-800 p-2 px-4 rounded-md">
                        <h4>{{ __('ticket.department') }}:</h4>
                        {{ $ticket->department }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>