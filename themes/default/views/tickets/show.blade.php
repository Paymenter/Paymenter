<div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-20 md:mt-24 mb-16 animate-in fade-in duration-700">
    
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 border-b border-gray-200 dark:border-gray-800 pb-6 gap-4">
        <div>
            <x-navigation.breadcrumb />
            <div class="flex items-center gap-2 mt-4">
                <div class="w-8 h-px bg-gradient-to-r from-primary-500 to-transparent"></div>
                <p class="text-[10px] font-black text-primary-600 dark:text-primary-400 uppercase tracking-[0.3em]">Ticket Details</p>
            </div>
            <h1 class="text-2xl md:text-3xl font-black tracking-tighter bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent mt-2">
                <span class="text-primary-600 dark:text-primary-400">#{{ $ticket->id }}</span> — {{ $ticket->subject }}
            </h1>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 rounded-full border text-[10px] font-black uppercase tracking-wider transition-all
                @if ($ticket->status == 'open') border-primary-200 dark:border-primary-800 bg-primary-50 dark:bg-primary-950/30 text-primary-700 dark:text-primary-400
                @elseif($ticket->status == 'closed') border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400
                @else border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 @endif">
                <div class="flex items-center gap-2">
                    @if ($ticket->status == 'open')
                        <div class="size-1.5 rounded-full bg-primary-500 animate-pulse"></div>
                    @elseif($ticket->status == 'closed')
                        <x-ri-forbid-fill class="size-3" />
                    @else
                        <x-ri-chat-smile-2-fill class="size-3" />
                    @endif
                    {{ strtoupper($ticket->status) }}
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        {{-- Main Content - Messages --}}
        <div class="lg:col-span-3 space-y-6">
            
            {{-- Messages Container --}}
            <div class="bg-white dark:bg-gray-900/50 backdrop-blur-sm border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden shadow-lg">
                <div class="flex flex-col gap-6 p-6 max-h-[60vh] overflow-y-auto custom-scrollbar" wire:poll.10s id="messages-container">
                    @forelse ($ticket->messages()->with('user')->get() as $index => $message)
                        @php $isUser = $message->user_id === $ticket->user_id; @endphp
                        
                        <div class="flex flex-col {{ $isUser ? 'items-end' : 'items-start' }} w-full">
                            
                            {{-- Message Header --}}
                            <div class="flex items-center gap-2 mb-1.5 px-2">
                                @if(!$isUser)
                                    <div class="w-5 h-5 rounded-full bg-primary-100 dark:bg-primary-950/50 flex items-center justify-center">
                                        <x-ri-customer-service-line class="size-3 text-primary-600 dark:text-primary-400" />
                                    </div>
                                    <span class="text-[10px] font-black text-primary-600 dark:text-primary-400 uppercase tracking-tighter">Support Staff</span>
                                @endif
                                <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $message->user->name }}</span>
                                <span class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">{{ $message->created_at->diffForHumans() }}</span>
                            </div>

                            {{-- Message Bubble --}}
                            <div class="p-4 rounded-2xl border transition-all max-w-[85%] sm:max-w-[75%] shadow-sm
                                {{ $isUser 
                                    ? 'bg-gradient-to-br from-primary-600 to-primary-500 text-white border-primary-500 shadow-primary-500/20 rounded-tr-none' 
                                    : 'bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-200 border-gray-200 dark:border-gray-700 rounded-tl-none hover:shadow-md' }}">
                                
                                <div class="prose prose-sm max-w-none break-words
                                    {{ $isUser ? 'prose-invert' : 'dark:prose-invert' }}">
                                    {!! Str::markdown($message->message, [
                                        'html_input' => 'escape',
                                        'allow_unsafe_links' => false,
                                        'renderer' => ['soft_break' => "<br>"]
                                    ]) !!}
                                </div>

                                {{-- Attachments --}}
                                @if($message->attachments->count() > 0)
                                    <div class="mt-4 flex flex-wrap gap-2 pt-3 border-t {{ $isUser ? 'border-white/20' : 'border-gray-200 dark:border-gray-700' }}">
                                        @foreach($message->attachments as $attachment)
                                            <a href="{{ route('tickets.attachments.show', $attachment) }}" 
                                               target="_blank"
                                               class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all duration-200
                                               {{ $isUser 
                                                    ? 'bg-white/10 hover:bg-white/20 text-white' 
                                                    : 'bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400' }}">
                                                <x-ri-attachment-2 class="size-3" />
                                                <span>{{ Str::limit($attachment->filename, 20) }}</span>
                                                <span class="text-[8px] opacity-60">{{ round($attachment->size / 1024, 1) }} KB</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                <x-ri-chat-1-line class="size-8 text-gray-400 dark:text-gray-600" />
                            </div>
                            <p class="text-gray-500 dark:text-gray-400">No messages yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Reply Form --}}
            @if($ticket->status !== 'closed')
            <div class="bg-white dark:bg-gray-900/50 backdrop-blur-sm border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-lg">
                <div class="flex items-center gap-2 mb-4">
                    <x-ri-reply-line class="size-4 text-primary-500" />
                    <h3 class="text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('ticket.reply') }}</h3>
                </div>
                
                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <div wire:ignore class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                            <textarea id="editor" placeholder="Type your reply here..."></textarea>
                        </div>
                        <x-easymde-editor />
                    </div>

                    {{-- Attachments Upload --}}
                    <div x-data="{ 
                        drop: false, 
                        uploading: false, 
                        progress: 0,
                        selectedFiles: [] 
                    }"
                    x-on:livewire-upload-start="uploading = true"
                    x-on:livewire-upload-finish="uploading = false; progress = 0; selectedFiles = []"
                    x-on:livewire-upload-progress="progress = $event.detail.progress"
                    class="relative">
                        
                        <div @dragover.prevent="drop = true" @dragleave.prevent="drop = false"
                             @drop.prevent="drop = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                             :class="drop ? 'border-primary-500 bg-primary-50/50 dark:bg-primary-950/30' : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50'"
                             class="border-2 border-dashed rounded-xl p-4 transition-all duration-200 text-center cursor-pointer">
                            
                            <input type="file" multiple class="hidden" x-ref="fileInput" wire:model.live="attachments"
                                   @change="selectedFiles = Array.from($event.target.files)">
                            
                            <div x-show="!uploading" class="flex flex-col items-center">
                                <label for="attachments" class="cursor-pointer group">
                                    <div class="flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                        <x-ri-upload-cloud-2-line class="size-5" />
                                        <span>{{ __('ticket.upload_attachments') }}</span>
                                        <span class="text-gray-400 text-xs font-normal">or drag & drop</span>
                                    </div>
                                    <p class="text-[9px] text-gray-400 mt-1">Max file size: 10MB per file</p>
                                </label>
                            </div>

                            <div x-show="uploading" class="w-full px-4">
                                <div class="flex justify-between text-[10px] font-bold text-primary-600 dark:text-primary-400 uppercase mb-1">
                                    <span>Uploading...</span>
                                    <span x-text="progress + '%'"></span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                    <div class="bg-gradient-to-r from-primary-500 to-primary-600 h-1.5 rounded-full transition-all duration-300" :style="'width:' + progress + '%'"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Selected Files Preview --}}
                        <div x-show="selectedFiles.length > 0" class="flex flex-wrap gap-2 mt-3">
                            <template x-for="(file, index) in selectedFiles" :key="index">
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full text-[10px] font-medium text-gray-700 dark:text-gray-300 shadow-sm">
                                    <x-ri-file-line class="size-3" />
                                    <span x-text="file.name.length > 20 ? file.name.substring(0, 20) + '...' : file.name"></span>
                                    <button type="button" @click="selectedFiles = selectedFiles.filter((_, i) => i !== index); $refs.fileInput.value = ''" class="text-red-500 hover:text-red-700 ml-1">
                                        <x-ri-close-line class="size-3" />
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                        @if (!config('settings.ticket_client_closing_disabled', false) && $ticket->status !== 'closed')
                            <button type="button" 
                                    class="w-full sm:w-auto px-6 py-2.5 text-xs font-bold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-xl transition-all duration-200"
                                    x-on:click.prevent="$store.confirmation.confirm({
                                        title: '{{ __('ticket.close_ticket') }}',
                                        callback: () => $wire.closeTicket()
                                    })">
                                <div class="flex items-center gap-2">
                                    <x-ri-forbid-line class="size-4" />
                                    {{ __('ticket.close_ticket') }}
                                </div>
                            </button>
                        @endif

                        <x-button.primary type="submit" class="w-full sm:w-auto px-8 py-2.5 shadow-lg shadow-primary-500/30" wire:target="save">
                            <div class="flex items-center gap-2 uppercase tracking-wider text-[10px] font-black">
                                <x-ri-send-plane-fill class="size-4" />
                                {{ __('ticket.reply') }}
                            </div>
                        </x-button.primary>
                    </div>
                </form>
            </div>
            @else
            {{-- Closed Ticket Message --}}
            <div class="bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 text-center">
                <x-ri-forbid-2-line class="size-12 text-gray-400 mx-auto mb-3" />
                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">This ticket is closed</p>
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">If you need further assistance, please create a new ticket.</p>
            </div>
            @endif
        </div>

        {{-- Sidebar - Ticket Details --}}
        <div class="space-y-4">
            <div class="bg-white dark:bg-gray-900/50 backdrop-blur-sm border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-lg sticky top-24">
                <div class="flex items-center gap-2 mb-6 border-b border-gray-200 dark:border-gray-800 pb-3">
                    <x-ri-information-line class="size-4 text-primary-500" />
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">
                        {{ __('ticket.ticket_details') }}
                    </h2>
                </div>
                
                <div class="space-y-5">
                    {{-- Priority --}}
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase flex items-center gap-1">
                            <x-ri-flag-line class="size-3" />
                            {{ __('ticket.priority') }}
                        </span>
                        <div class="flex items-center gap-2 mt-1.5">
                            <div class="size-2 rounded-full 
                                @if($ticket->priority == 'high') bg-red-500 animate-pulse
                                @elseif($ticket->priority == 'medium') bg-yellow-500
                                @else bg-blue-500 @endif">
                            </div>
                            <span class="text-sm font-bold text-gray-900 dark:text-white capitalize">{{ $ticket->priority }}</span>
                        </div>
                    </div>

                    {{-- Department --}}
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase flex items-center gap-1">
                            <x-ri-building-line class="size-3" />
                            {{ __('ticket.department') }}
                        </span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white mt-1.5">{{ $ticket->department ?? 'General Support' }}</span>
                    </div>

                    {{-- Created At --}}
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase flex items-center gap-1">
                            <x-ri-calendar-line class="size-3" />
                            {{ __('ticket.created_at') }}
                        </span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white mt-1.5">{{ $ticket->created_at->format('M d, Y') }}</span>
                        <span class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">{{ $ticket->created_at->diffForHumans() }}</span>
                    </div>

                    {{-- Last Updated --}}
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase flex items-center gap-1">
                            <x-ri-time-line class="size-3" />
                            Last Activity
                        </span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white mt-1.5">{{ $ticket->updated_at->format('M d, Y') }}</span>
                        <span class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">{{ $ticket->updated_at->diffForHumans() }}</span>
                    </div>

                    {{-- Message Count --}}
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase flex items-center gap-1">
                            <x-ri-chat-1-line class="size-3" />
                            Total Messages
                        </span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white mt-1.5">{{ $ticket->messages()->count() }} messages</span>
                    </div>

                    {{-- Info Box --}}
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-800">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/30 dark:to-indigo-950/30 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                            <div class="flex items-start gap-2">
                                <x-ri-information-line class="size-4 text-blue-500 flex-shrink-0 mt-0.5" />
                                <p class="text-[10px] font-medium text-blue-700 dark:text-blue-300 leading-relaxed">
                                    Our team usually responds within 24 hours. Keep this window open for real-time updates.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Inline Styles (moved inside the main div to keep single root) --}}
    <style>
        /* Custom scrollbar for messages */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }
        
        .dark .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.3);
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.5);
        }
    </style>
</div>

<script>
    // Auto-scroll to bottom of messages on load and new message
    document.addEventListener('livewire:load', function() {
        const container = document.getElementById('messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
    
    document.addEventListener('livewire:update', function() {
        const container = document.getElementById('messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
</script>

<x-easymde-editor />