<x-app-layout clients title="{{ __('Ticket') }}">
    <!-- show last messages and form to reply -->
    <div class="py-4 sm:py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div
                class="overflow-hidden bg-white shadow-xl sm:rounded-lg dark:bg-secondary-100 dark:border-secondary-300">
                <div class="p-5 bg-white sm:px-10 dark:bg-secondary-100">
                    <x-success class="mt-4" />
                    <div class="justify-between flex flex-row items-center">
                        <h1 class="text-2xl font-bold text-gray-500 dark:text-darkmodetext">{{__('Ticket')}} #{{
                            $ticket->id }}</h1>
                        @if($ticket->status !== 'closed')
                        <form action="{{ route('clients.tickets.close', $ticket->id) }} " method="POST" class="mt-3">
                            @csrf
                            <button class="button button-danger float-right"><i class="ri-close-circle-line"></i>
                                {{__('Close Ticket')}}</button>
                        </form>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="text-gray-500 dark:text-darkmodetext dark:bg-secondary-100">
                            <div class="flex flex-row gap-x-4 items-baseline">
                                <x-input disabled type="text" id="title" :label="__('Subject')" name="title"
                                    value="{{ $ticket->title }}" required class="mt-2 w-full" icon="ri-pencil-line" />

                                <x-input disabled type="text" name="status" :label="__('Status')"
                                    icon="ri-bar-chart-line" class="mt-2 w-full"
                                    value="{{ ucfirst($ticket->status) }}" />
                            </div>
                        </div>
                    </div>
                </div>

                @php $messages = $ticket->messages()->with('user')->get(); @endphp
                @empty($messages)
                <div class="ml-10 flex items-baseline ">
                    <p class="dark:text-darkmodetext text-gray-600 px-3 rounded-md text-xl m-4">
                        {{ __('No messages yet') }}
                    </p>
                </div>
                @else
                <div
                    class="md:p-6 bg-white sm:px-20 border-b border-gray-200 dark:bg-secondary-100 dark:border-secondary-300">
                    <div class="mt-6">
                        <div class="grid grid-cols-3 md:gap-4 max-h-[45vh] overflow-y-auto" id="content">

                            @foreach ($messages as $message)
                            @if ($message->user_id == Auth::user()->id)
                            <div class="md:col-span-1"></div>
                            <div class="w-full col-span-3 md:col-span-2" id="message">
                                <div class="grid grid-cols-12 place-items-end w-fit">
                                    <div class="col-span-10 md:col-span-11">
                                        <div class="text-sm flex justify-end font-normal text-opacity-50 mr-4">
                                            ({{$message->messageDate()}}) {{__('You')}}</div>
                                        <div class="w-full flex justify-end">
                                            <div
                                                class="w-full hyphens-auto supports-[overflow-wrap:anywhere]:[overflow-wrap:anywhere] supports-[not(overflow-wrap:anywhere)]:[word-break:normal] text-white dark:text-white-400 rounded-2xl bg-primary-400 p-2 px-4 mr-2">
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
                                                        <a href="{{ $attachment->url }}"
                                                            class="text-slate-200 hover:text-white" download>
                                                            @if($attachment->isImage())
                                                            <img src="{{ $attachment->url }}" class="h-10 rounded-sm"
                                                                alt="">
                                                            @else
                                                            <div
                                                                class="text-slate-200 hover:text-white text-sm break-all text-center">
                                                                <div class="justify-center flex">
                                                                    <div id="tooltip" role="tooltip"
                                                                        class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                                                        {{ $attachment->filename }}
                                                                        <div class="tooltip-arrow" data-popper-arrow>
                                                                        </div>
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
                                            <span class="text-end" style="color: white !important;">
                                                {!! Str::Markdown(str_replace("\n", " \n", $message->message),
                                                ['html_input' => 'escape']) !!}

                                                @if($message->files()->count() > 0)
                                                <br>
                                                <hr class="border-slate-100">
                                                <span class="text-slate-100 ">{{ __('Attachments') }} ({{
                                                    $message->files()->count() }})</span>
                                                <div class="flex flex-row flex-wrap gap-4">
                                                    @foreach($message->files as $attachment)
                                                    <div class="col-span-1">
                                                        <a href="{{ $attachment->url }}"
                                                            class="text-slate-200 hover:text-white" download>
                                                            @if($attachment->isImage())
                                                            <img src="{{ $attachment->url }}" class="h-10 rounded-sm"
                                                                alt="">
                                                            @else
                                                            <div
                                                                class="text-slate-200 hover:text-white text-sm break-all text-center">
                                                                <div class="justify-center flex">
                                                                    <div id="tooltip" role="tooltip"
                                                                        class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                                                        {{ $attachment->filename }}
                                                                        <div class="tooltip-arrow" data-popper-arrow>
                                                                        </div>
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
                                            </span>
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
                @endempty
                <br>
                <div class="overflow-hidden bg-white shadow-xl sm:rounded-b-lg dark:bg-secondary-100">
                    @if($ticket->status == "closed")
                    <div class="p-6 text-center text-red-500 bg-white sm:px-20 dark:bg-secondary-100 mx-auto my-auto">
                        {{__('The ticket has been closed, you cannot respond.')}}
                    </div>
                    @else
                    <form method="POST" action="{{ route('clients.tickets.reply', $ticket->id) }}" class="mt-2"
                        id="reply" enctype="multipart/form-data">
                        @csrf
                        <div
                            class="bg-white mb-5 border-gray-200 sm:px-20 dark:bg-secondary-100 dark:border-black mt-5">
                            <h1 class="text-xl text-gray-500 dark:text-darkmodetext font-bold">{{ __('Reply') }}</h1>
                            <div class="grid grid-cols-1 gap-4">
                                <div class="mt-3 text-gray-500 dark:text-darkmodetext dark:bg-secondary-100">

                                    <div id="attachments-list" class="flex flex-row gap-x-4 mb-3"></div>

                                    <div class="flex flex-row">
                                        <label for="attachments"
                                            class="button-secondary rounded-full cursor-pointer flex w-10 h-10 mr-2 transition-all ease-in-out">
                                            <i class="ri-add-line my-auto mx-auto"></i>
                                        </label>
                                        <textarea id="message"
                                            class="block my-auto w-full rounded-2xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border-indigo-300 dark:border-0 sm:text-sm dark:bg-secondary-200"
                                            rows="1" name="message" placeholder="Aa" required></textarea>
                                        <button type="submit" id="submit-button"
                                            class="button-primary rounded-full w-10 ml-2 h-10 float-right transition-all ease-in-out">
                                            <i class="ri-send-plane-fill"></i>
                                        </button>
                                    </div>
                                    <x-input type="file" id="attachments" :label="__('Attachments')"
                                        name="attachments[]" multiple class="hidden" />
                                    <x-recaptcha form="reply" />
                                    <br>
                                </div>
                            </div>
                        </div>
                    </form>
                    @endif
                </div>
                <script>
                    function slideDown() {
            const content = document.getElementById('content');
            const contentHeight = content.scrollHeight;
            content.scroll(0, contentHeight);

        }

        window.addEventListener('load', slideDown);

        const fileInput = document.getElementById('attachments');
        const fileList = document.getElementById('attachments-list');
        const submitButton = document.getElementById('submit-button');

        const files = [];

        fileInput.addEventListener('change', (e) => {
            const selectedFiles = e.target.files;

            for (let i = 0; i < selectedFiles.length; i++) {
                files.push(selectedFiles[i]);
                const listItem = createListItem(selectedFiles[i]);
                fileList.appendChild(listItem);
            }

            fileInput.value = '';
        });

        function createListItem(file) {
            const listItem = document.createElement('div');
            listItem.classList.add('bg-secondary-200', 'rounded-md', 'w-full', 'max-w-[120px]', 'p-2', 'justify-center', 'flex-col', 'items-center', 'shadow-sm', 'mb-3');

            const fileContent = document.createElement('div');
            fileContent.classList.add('justify-center', 'flex');

            if (/\.(jpe?g|png|gif|bmp)$/i.test(file.name)) {
                const image = document.createElement('img');
                image.src = URL.createObjectURL(file);
                image.alt = file.name;
                image.classList.add('max-h-10', 'rounded-sm');

                fileContent.appendChild(image);
            } else {
                const icon = document.createElement('i');
                icon.classList.add('ri-article-line', 'text-4xl', 'mx-auto', 'text-secondary-500');
                fileContent.appendChild(icon);
            }

            const fileName = document.createElement('div');
            fileName.textContent = file.name;
            fileName.classList.add('text-xs', 'text-center', 'text-secondary-500', 'w-full', 'truncate', 'mt-1');

            const removeButton = document.createElement('button');
            removeButton.textContent = 'x';
            removeButton.classList.add('text-red-500', 'hover:text-red-700', 'cursor-pointer', 'float-right', 'text-xs', 'font-bold');
            removeButton.addEventListener('click', () => {

                const index = files.indexOf(file);
                files.splice(index, 1);
                listItem.remove();
            });

            listItem.appendChild(removeButton);
            listItem.appendChild(fileContent);
            listItem.appendChild(fileName);

            return listItem;
        }

        submitButton.addEventListener('click', () => {
            if (files.length > 0) {
                const fileListArray = new DataTransfer();
                for (const file of files) {
                    fileListArray.items.add(file);
                }
                fileInput.files = fileListArray.files;
            }

        });

        const tx = document.getElementsByTagName("textarea");
        for (let i = 0; i < tx.length; i++) {
            tx[i].setAttribute("style", "height:" + (tx[i].scrollHeight) + "px;overflow-y:hidden;");
            tx[i].addEventListener("input", OnInput, false);
        }

        function OnInput() {
            this.style.height = 0;
            this.style.height = (this.scrollHeight) + "px";
        }
                </script>
</x-app-layout>