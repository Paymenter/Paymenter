<div class="bg-primary-800 p-6 rounded-lg mt-2">
    <h1 class="text-2xl font-semibold mb-2">{{ __('ticket.create_ticket') }}</h1>
    <div class="grid grid-cols-2 gap-4">
        <x-form.input wire:model="subject" label="{{ __('ticket.subject') }}" name="subject" required />
        @if (count($departments) > 0)
            <x-form.select wire:model="department" label="{{ __('ticket.department') }}" name="department" required>
                <option value="">{{ __('ticket.select_department') }}</option>
                @foreach ($departments as $department)
                    <option value="{{ $department }}">{{ $department }}</option>
                @endforeach
            </x-form.select>
        @endif
        <x-form.select wire:model="priority" label="{{ __('ticket.priority') }}" name="priority" required>
            <option value="">{{ __('ticket.select_priority') }}</option>
            <option value="low" selected>{{ __('ticket.low') }}</option>
            <option value="medium">{{ __('ticket.medium') }}</option>
            <option value="high">{{ __('ticket.high') }}</option>
        </x-form.select>
        <!-- Select the product -->
        <x-form.select wire:model="service" label="{{ __('ticket.service') }}" name="service">
            <option value="">{{ __('ticket.select_service') }}</option>
            @foreach ($services as $product)
                <option value="{{ $product->id }}">{{ $product->product->name }} ({{ ucfirst($product->status) }})
                    @if ($product->expires_at)
                        - {{ $product->expires_at->format('Y-m-d') }}
                    @endif
                </option>
            @endforeach
        </x-form.select>
        <div class="col-span-2">

            <div class="mt-4">
                <form wire:submit.prevent="create" wire:ignore>
                    <label for="editor" class="block text-sm font-medium text-primary-100">
                        {{ __('ticket.reply') }}
                    </label>
                    <textarea id="editor" placeholder="Initial message"></textarea>
                    
                    <label for="attachments" class="block text-sm font-medium text-primary-100 mt-2">
                        {{ __('ticket.attachments') }}
                    </label>
                    <div x-data="{
                            drop: false,
                            selectedFiles: [],
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
                        }">
                        <div class="flex justify-center rounded-lg bg-background-secondary border border-dashed border-neutral px-6 py-2"
                            @dragover.prevent="drop = true" @dragleave.prevent="drop = false"
                            @drop.prevent="handleDrop($event)" :class="{'bg-background-secondary/50': drop}">
                            <div class="text-center">
                                <template x-if="selectedFiles.length === 0">
                                    <div>
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
                                <div x-show="selectedFiles.length > 0">
                                    <h4 class="text-sm font-semibold">{{ __('ticket.selected_files') }}:</h4>
                                    <div class="flex flex-wrap items-center justify-center gap-2 mt-1">
                                        <template x-for="file in selectedFiles" :key="file.name">
                                            <div
                                                class="text-sm rounded-md bg-gray-100 flex items-center gap-2 dark:bg-gray-800 p-1 py-0 w-fit">
                                                <span class="flex-1" x-text="file.name"></span>
                                                <button type="button"
                                                    class="text-red-500 hover:text-red-700 text-lg h-fit"
                                                    @click="selectedFiles = selectedFiles.filter(f => f !== file)">
                                                    &times;
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input id="attachments" type="file" multiple name="attachments[]" class="sr-only"
                            wire:model.live="attachments" x-ref="fileInput"
                            @change="selectedFiles = Array.from($event.target.files)" />
                    </div>
                    <x-button.primary class="mt-2 !w-fit float-right">
                        {{ __('ticket.create') }}
                    </x-button.primary>
                </form>
                <x-easymde-editor />
            </div>
        </div>
    </div>
</div>
