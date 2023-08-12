<x-admin-layout>
    <x-slot name="title">
        {{ __('Tickets ' . $ticket->title) }}
    </x-slot>

    <div class="bg-white dark:bg-secondary-100">
        <h1 class="text-2xl font-bold text-gray-500 dark:text-darkmodetext">{{ __('View Ticket #') }}{{ $ticket->id }}
        </h1>
    </div>
    <form action="{{ route('admin.tickets.update', $ticket->id) }}" method="POST" class="pb-10">
        @csrf

        <div class="grid md:grid-cols-2 mt-4 gap-4">
            <div class="flex flex-col items-baseline">
                <x-input type="text" id="title" :label="__('Subject')" name="title" value="{{ $ticket->title }}"
                    required class="mt-2 w-full" icon="ri-pencil-line" />

                <x-input type="select" name="priority" :label="__('Priority')" icon="ri-bar-chart-line" class="mt-2 w-full">
                    <option value="low" @if ($ticket->priority == "low") selected @endif>
                        {{ __('Low') }}</option>
                    <option value="medium" @if ($ticket->priority == "medium") selected @endif>
                        {{ __('Medium') }}</option>
                    <option value="high" @if ($ticket->priority == "high") selected @endif>
                        {{ __('High') }}</option>
                </x-input>

                <x-input type="select" name="status" :label="__('Status')" icon="ri-bar-chart-line" class="mt-2 w-full">
                    <option value="open" @if ($ticket->status == 'open') selected @endif>
                        {{ __('Open') }}</option>
                    <option value="closed" @if ($ticket->status == 'closed') selected @endif>
                        {{ __('Closed') }}</option>
                </x-input>
            </div>
            <div class="flex flex-col items-baseline">
                <x-input type="text" id="user" :label="__('User')" name="user"
                    value="{{ $ticket->user->name }}" required class="mt-2 w-full" icon="ri-user-line" readonly />
                <x-input type="select" id="product" name="product_id" :label="__('Product')" icon="ri-checkbox-circle-line"
                    class="mt-2 w-full">
                    <option value="">{{ __('None') }}</option>
                    @foreach ($ticket->user->orderProducts()->get() as $product)
                        <option value="{{ $product->id }}" @if ($product->id == $ticket->order_id) selected @endif>
                            {{ $product->id }} - {{ $product->product->name }}
                    @endforeach
                </x-input>

                <x-input type="select" id="assigned_to" name="assigned_to" :label="__('Assigned To')" icon="ri-user-line"
                    class="mt-2 w-full">
                    <option value="">{{ __('None') }}</option>
                    @foreach (App\Models\User::where('role_id', '!=', 2)->get() as $user)
                        <option value="{{ $user->id }}" @if ($user->id == $ticket->assigned_to) selected @endif>
                            {{ $user->name }} - {{ $user->role->name }}
                    @endforeach
                </x-input>
            </div>
        </div>
        <button type="submit" class="button button-success float-right mt-4">
            <i class="ri-loop-left-line"></i> {{ __('Update') }}
        </button>
    </form>
    @if (empty(
            $ticket->messages()->get()->first()
        ))
        <div class="ml-10 flex items-baseline ">
            <p class="dark:text-darkmodetext text-gray-600 px-3 rounded-md text-xl m-4">
                {{ __('No messages yet') }}
            </p>
        </div>
    @else
        <div class="p-6 bg-white sm:px-20 border-b border-gray-200 dark:bg-secondary-100 dark:border-secondary-300">
            <div class="mt-6 dark:bg-secondary-100">
                <div class="grid grid-cols-3 gap-4">

                    @foreach ($ticket->messages()->get() as $message)
                        @if ($message->user_id == Auth::user()->id)
                            <div class="col-span-3 text-center w-full">
                                {{$message->messageDate()}}
                            </div>
                            <div class="col-span-1"></div>
                            <div class="w-full col-span-2" id="message">
                                <div class="grid grid-cols-12 max-w-full place-items-end">
                                    <div class="col-span-11 my-auto">
                                        <span class="text-sm flex max-w-full justify-end font-normal text-opacity-50 mr-5">{{__('You')}}</span>
                                        <div class="justify-end text-gray-100 break-all dark:text-white-400 w-fit rounded-2xl bg-indigo-600 p-2 px-4 mr-2">
                                            <p class="max-w-full text-end" style="color: white !important;">
                                                {!! Str::Markdown(str_replace("\n", "  \n", $message->message), ['html_input' => 'escape']) !!}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="justify-start w-full h-full flex col-span-1">
                                        <img src="https://www.gravatar.com/avatar/{{ md5($message->user->email) }}?s=200&d=mp"
                                             class="h-10 w-10 mt-7 shadow-md rounded-full bg-secondary-200 inline-block"
                                             onerror='this.error=null;this.src="https://d33wubrfki0l68.cloudfront.net/c0e8a3c6172bd5bebfe787d49974adcff1ec4d3a/ca6a2/img/people/joseph-jolton.png";'>
                                    </div>
                                </div>
                            </div>
                        @elseif ($message->user_id !== Auth::user()->id)
                            <div class="col-span-3 text-center w-full">
                                {{$message->messageDate()}}
                            </div>
                            <div class="w-full col-span-2" id="message">
                                <div class="grid grid-cols-12 max-w-full">
                                    <div class="justify-end w-full flex col-span-1">
                                        <img src="https://www.gravatar.com/avatar/{{ md5($message->user->email) }}?s=200&d=mp"
                                             class="h-10 w-10 mt-7 shadow-md rounded-full bg-secondary-200 inline-block"
                                             onerror='this.error=null;this.src="https://d33wubrfki0l68.cloudfront.net/c0e8a3c6172bd5bebfe787d49974adcff1ec4d3a/ca6a2/img/people/joseph-jolton.png";'>
                                    </div>
                                    <div class="col-span-11">
                                        <span class="text-sm ml-5 font-normal text-opacity-50">{{$message->user->name}} ({{ ucfirst($message->user->role->name) }})</span>
                                        <div class="my-auto text-gray-500 break-all dark:text-darkmodetext ml-2 w-fit rounded-2xl bg-gray-200 dark:bg-darkmode p-2 px-4">
                                            <p class="prose dark:prose-invert max-w-full break-word">
                                                {!! Str::Markdown(str_replace("\n", "  \n", $message->message), ['html_input' => 'escape']) !!}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-1"></div>
                        @endif
                    @endforeach

                </div>
            </div>
        </div>
    @endif
    <br>
    <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg dark:bg-secondary-100">
        <form method="POST" action="{{ route('admin.tickets.reply', $ticket->id) }}" class="mt-10" id="reply">
            @csrf
            <div class="bg-white mb-5 border-gray-200 sm:px-20 dark:bg-secondary-100 dark:border-black mt-5">
                <h1 class="text-xl text-gray-500 dark:text-darkmodetext font-bold">{{ __('Reply') }}</h1>
                <div class="grid grid-cols-1 gap-4">
                    <div class="mt-3 text-gray-500 dark:text-darkmodetext dark:bg-secondary-100">
                        <div class="flex flex-row">
                            <textarea
                                id="message"
                                class="block my-auto w-full rounded-2xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border-indigo-300 dark:border-0 sm:text-sm dark:bg-secondary-200"
                                rows="1"
                                name="message"
                                placeholder="Aa"
                                required
                            ></textarea>
                            <x-recaptcha form="reply" />
                            <button class="button-primary ml-1 rounded-full w-10 my-auto ml-2 h-10 float-right transition-all ease-in-out">
                                <i class="ri-send-plane-fill"></i>
                            </button>
                        </div>
                        <br>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- Script for auto-extend message textarea -->
    <script>
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
</x-admin-layout>
