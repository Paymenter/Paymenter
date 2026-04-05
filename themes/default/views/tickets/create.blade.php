<div class="container mx-auto px-4 sm:px-6 mt-20 md:mt-24 mb-16">
    <div class="max-w-4xl mx-auto">
        
        {{-- Header --}}
        <div class="mb-8">
            <x-navigation.breadcrumb />
            <div class="flex items-center gap-2 mt-4">
                <div class="w-8 h-px bg-gradient-to-r from-primary-500 to-transparent"></div>
                <p class="text-[10px] font-black text-primary-600 dark:text-primary-400 uppercase tracking-[0.3em]">Support Portal</p>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black tracking-tighter bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent mt-2">
                {{ __('ticket.create_ticket') }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Submit a new support request and our team will get back to you shortly.</p>
        </div>
        
        {{-- Form Card --}}
        <div class="bg-white dark:bg-gray-900/50 backdrop-blur-sm border border-gray-200 dark:border-gray-800 rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-primary-600 to-primary-500 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <x-ri-ticket-line class="size-5 text-white" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white">{{ __('ticket.create_ticket') }}</h2>
                        <p class="text-xs text-white/80">Fill out the form below to create a new support ticket</p>
                    </div>
                </div>
            </div>
            
            <form wire:submit.prevent="create" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Subject Field --}}
                    <div class="md:col-span-2">
                        <x-form.input 
                            wire:model="subject" 
                            label="{{ __('ticket.subject') }}" 
                            name="subject" 
                            required 
                            class="!rounded-xl"
                            placeholder="Brief description of your issue"
                        />
                    </div>
                    
                    {{-- Department Field --}}
                    @if (count($departments) > 0)
                        <div>
                            <x-form.select 
                                wire:model="department" 
                                label="{{ __('ticket.department') }}" 
                                name="department" 
                                required
                                class="!rounded-xl">
                                <option value="">{{ __('ticket.select_department') }}</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department }}">{{ $department }}</option>
                                @endforeach
                            </x-form.select>
                        </div>
                    @endif
                    
                    {{-- Priority Field --}}
                    <div>
                        <x-form.select 
                            wire:model="priority" 
                            label="{{ __('ticket.priority') }}" 
                            name="priority" 
                            required
                            class="!rounded-xl">
                            <option value="">{{ __('ticket.select_priority') }}</option>
                            <option value="low" selected>
                                🔵 {{ __('ticket.low') }}
                            </option>
                            <option value="medium">
                                🟡 {{ __('ticket.medium') }}
                            </option>
                            <option value="high">
                                🔴 {{ __('ticket.high') }}
                            </option>
                        </x-form.select>
                    </div>
                    
                    {{-- Service/Product Field --}}
                    <div>
                        <x-form.select 
                            wire:model="service" 
                            label="{{ __('ticket.service') }}" 
                            name="service"
                            class="!rounded-xl">
                            <option value="">{{ __('ticket.select_service') }}</option>
                            @foreach ($services as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->product->name }} ({{ ucfirst($product->status) }})
                                    @if ($product->expires_at)
                                        - {{ $product->expires_at->format('Y-m-d') }}
                                    @endif
                                </option>
                            @endforeach
                        </x-form.select>
                    </div>
                </div>
                
                {{-- Message Editor --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('ticket.reply') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <textarea id="editor" placeholder="Describe your issue in detail..."></textarea>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        Please provide as much detail as possible to help us resolve your issue faster.
                    </p>
                </div>
                
                {{-- Attachments Section --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
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
                                this.$refs.fileInput.dispatchEvent(new Event('change'));
                            }
                        },
                        removeFile(index) {
                            this.selectedFiles.splice(index, 1);
                            const dt = new DataTransfer();
                            this.selectedFiles.forEach(file => dt.items.add(file));
                            this.$refs.fileInput.files = dt.files;
                            this.$refs.fileInput.dispatchEvent(new Event('change'));
                        },
                        init() {
                            this.$watch('$wire.attachments', (value) => {
                                if (value.length == 0) {
                                    this.selectedFiles = [];
                                }
                            });
                        }
                    }" class="space-y-3">
                        
                        {{-- Drop Zone --}}
                        <div class="flex justify-center rounded-xl bg-gray-50 dark:bg-gray-800/50 border-2 border-dashed border-gray-300 dark:border-gray-700 px-6 py-8 transition-all duration-200"
                            @dragover.prevent="drop = true" 
                            @dragleave.prevent="drop = false"
                            @drop.prevent="handleDrop($event)" 
                            :class="{'border-primary-500 bg-primary-50/50 dark:bg-primary-950/30': drop}">
                            
                            <div class="text-center">
                                <template x-if="selectedFiles.length === 0">
                                    <div>
                                        <x-ri-upload-cloud-2-line class="size-12 mx-auto text-gray-400 dark:text-gray-500 mb-3" />
                                        <div class="flex flex-wrap items-center justify-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                                            <label for="attachments" class="relative cursor-pointer rounded-md font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 transition-colors">
                                                <span>{{ __('ticket.upload_attachments') }}</span>
                                            </label>
                                            <p class="text-gray-500">{{ __('ticket.or_drag_and_drop') }}</p>
                                        </div>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                                            {{ __('ticket.files_max') }} (Max: 10MB per file)
                                        </p>
                                    </div>
                                </template>
                                
                                <div x-show="selectedFiles.length > 0">
                                    <x-ri-checkbox-circle-fill class="size-12 mx-auto text-green-500 mb-3" />
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">{{ __('ticket.selected_files') }}:</h4>
                                    <div class="flex flex-wrap items-center justify-center gap-2 max-h-32 overflow-y-auto">
                                        <template x-for="(file, index) in selectedFiles" :key="file.name">
                                            <div class="text-sm rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center gap-2 px-3 py-1.5">
                                                <x-ri-file-line class="size-4 text-gray-500" />
                                                <span class="flex-1 text-gray-700 dark:text-gray-300 max-w-[150px] truncate" x-text="file.name"></span>
                                                <button type="button" class="text-red-500 hover:text-red-700 transition-colors" @click="removeFile(index)">
                                                    <x-ri-close-line class="size-4" />
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
                </div>
                
                {{-- Form Actions --}}
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-gray-200 dark:border-gray-800">
                    <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                        <span class="flex items-center gap-1">
                            <x-ri-shield-check-line class="size-3" />
                            Secure form
                        </span>
                        <span class="w-px h-3 bg-gray-300 dark:bg-gray-700"></span>
                        <span class="flex items-center gap-1">
                            <x-ri-time-line class="size-3" />
                            Response within 24h
                        </span>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        {{-- FIXED: Changed route from 'tickets.index' to 'tickets' or your actual route name --}}
                        <a href="{{ route('tickets') }}" wire:navigate class="px-6 py-2.5 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">
                            Cancel
                        </a>
                        <x-button.primary type="submit" class="px-6 py-2.5 rounded-xl text-sm font-black uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <x-ri-send-plane-line class="size-4" />
                                {{ __('ticket.create') }}
                            </div>
                        </x-button.primary>
                    </div>
                </div>
            </form>
        </div>
        
        {{-- Help Section --}}
        <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/30 dark:to-indigo-950/30 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
            <div class="flex flex-col sm:flex-row items-start gap-4">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-ri-question-mark class="size-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-black uppercase tracking-wider text-blue-600 dark:text-blue-400 mb-1">Need Help?</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">Before creating a ticket, check out our knowledge base or FAQ section for quick answers.</p>
                    <div class="flex flex-wrap gap-3">
                        <a href="#" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                            Browse Knowledge Base
                            <x-ri-arrow-right-line class="size-3" />
                        </a>
                        <span class="w-px h-4 bg-blue-300 dark:bg-blue-700 hidden sm:block"></span>
                        <a href="#" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                            View FAQ
                            <x-ri-arrow-right-line class="size-3" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<x-easymde-editor />