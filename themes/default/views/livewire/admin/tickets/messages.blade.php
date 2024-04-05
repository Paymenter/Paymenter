<div class="md:p-6 bg-white sm:px-20 border-b border-gray-200 dark:bg-secondary-100 dark:border-secondary-300">
    <div class="mt-6">
        <div class="grid grid-cols-3 md:gap-4 max-h-[45vh] overflow-y-auto" id="content" wire:poll.15s='updateMessages'>
            @foreach ($messages as $message)
            @if ($message->user_id == Auth::user()->id)
            <div class="md:col-span-1"></div>
            <div class="w-full col-span-3 md:col-span-2" id="message">
                <div class="grid grid-cols-12 place-items-end">
                    <div class="col-span-10 md:col-span-11">
                        <div class="text-sm flex justify-end font-normal text-opacity-50 mr-4">
                            ({{$message->messageDate()}}) {{__('You')}}</div>
                        <div class="w-full flex justify-end">
                            <div
                                class="w-full hyphens-auto supports-[overflow-wrap:anywhere]:[overflow-wrap:anywhere] supports-[not(overflow-wrap:anywhere)]:[word-break:normal] text-black dark:text-white rounded-2xl bg-primary-400 p-2 px-4 mr-2">
                                @markdownify($message->message)

                                @if($message->files()->count() > 0)
                                <br>
                                <hr class="border-slate-100">

                                <div class="flex justify-end text-end">
                                    <span class="text-slate-100 ">{{ __('Attachments') }} ({{
                                        $message->files()->count() }})</span>
                                </div>

                                <div class="flex flex-row flex-wrap gap-3">
                                    @foreach($message->files as $attachment)
                                    <div class="col-span-1">
                                        <a href="{{ $attachment->url }}" class="text-slate-200 hover:text-white"
                                            download>
                                            @if($attachment->isImage())
                                            <img src="{{ $attachment->url }}" class="h-10 rounded-sm" alt="">
                                            @else
                                            <div class="text-slate-200 hover:text-white text-sm break-all text-center">
                                                <div class="justify-center flex">
                                                    <div id="tooltip" role="tooltip"
                                                        class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                                        {{ $attachment->filename }}
                                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                                    </div>
                                                    <i data-tooltip-target="tooltip"
                                                        class="ri-file-text-line text-4xl mx-auto text-slate-200"></i>
                                                </div>
                                            </div>
                                            @endif
                                        </a>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="justify-start w-full h-full flex col-span-2 md:col-span-1">
                        <img src="https://www.gravatar.com/avatar/{{ md5($message->user->email) }}?s=200&d=mp"
                            class="h-10 w-10 mt-6 shadow-md rounded-full bg-secondary-200 inline-block"
                            onerror='this.error=null;this.src="https://d33wubrfki0l68.cloudfront.net/c0e8a3c6172bd5bebfe787d49974adcff1ec4d3a/ca6a2/img/people/joseph-jolton.png";'
                            alt="">
                    </div>
                </div>
            </div>
            @elseif ($message->user_id !== Auth::user()->id)
            <div class="w-full col-span-3 md:col-span-2" id="message">
                <div class="grid grid-cols-12 max-w-full">
                    <div class="justify-end w-full flex col-span-2 md:col-span-1">
                        <img src="https://www.gravatar.com/avatar/{{ md5($message->user->email) }}?s=200&d=mp"
                            class="h-10 w-10 mt-6 shadow-md rounded-full bg-secondary-200 inline-block"
                            onerror='this.error=null;this.src="https://d33wubrfki0l68.cloudfront.net/c0e8a3c6172bd5bebfe787d49974adcff1ec4d3a/ca6a2/img/people/joseph-jolton.png";'
                            alt="">
                    </div>
                    <div class="col-span-10">
                        <span class="text-sm ml-5 font-normal text-opacity-50">{{$message->user->name}}
                            ({{$message->messageDate()}})</span>
                        <div
                            class="my-auto text-gray-500 break-all dark:text-darkmodetext ml-2 w-fit rounded-2xl bg-gray-200 dark:bg-darkmode p-2 px-4">
                            <div
                                class="w-full hyphens-auto supports-[overflow-wrap:anywhere]:[overflow-wrap:anywhere] supports-[not(overflow-wrap:anywhere)]:[word-break:normal] text-white dark:text-white-400 rounded-2xl p-2 px-4 mr-2">
                                @markdownify($message->message)

                                @if($message->files()->count() > 0)
                                <br>
                                <hr class="border-slate-100">
                                <span class="text-slate-100 ">{{ __('Attachments') }} ({{ $message->files()->count()
                                    }})</span>
                                <div class="flex flex-row flex-wrap gap-4">
                                    @foreach($message->files as $attachment)
                                    <div class="col-span-1">
                                        <a href="{{ $attachment->url }}" class="text-slate-200 hover:text-white"
                                            download>
                                            @if($attachment->isImage())
                                            <img src="{{ $attachment->url }}" class="h-10 rounded-sm" alt="">
                                            @else
                                            <div class="text-slate-200 hover:text-white text-sm break-all text-center">
                                                <div class="justify-center flex">
                                                    <div id="tooltip" role="tooltip"
                                                        class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                                        {{ $attachment->filename }}
                                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                                    </div>
                                                    <i data-tooltip-target="tooltip"
                                                        class="ri-file-text-line text-4xl mx-auto text-slate-200"></i>
                                                </div>
                                            </div>
                                            @endif
                                        </a>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="md:col-span-1"></div>
            @endif
            @endforeach
        </div>
    </div>
</div>