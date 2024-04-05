<x-admin-layout>
    <x-slot name="title">
        {{ __('Tickets ' . $ticket->title) }}
    </x-slot>
    <style>
        body {
            overflow-y: hidden;
        }

        @media screen and (max-width: 768px) {
            body {
                overflow-y: auto;
            }
        }
    </style>
    <div class="bg-white dark:bg-secondary-100 justify-between flex flex-row">
        <h1 class="text-2xl font-bold text-gray-500 dark:text-darkmodetext">{{ __('View Ticket #') }}{{ $ticket->id }}
        </h1>
        <button class="button button-primary text-sm flex flex-row gap-2" data-modal-target="{{ $ticket->id }}"
            data-modal-toggle="{{ $ticket->id }}">
            <i class="ri-edit-line"></i> <span class="hidden md:flex">{{ __('View/Edit Ticket details') }}</span>
        </button>
    </div>
    <x-modal :id="$ticket->id" title="{{__('Ticket options')}}">
        <form action="{{ route('admin.tickets.update', $ticket->id) }}" method="POST" class="pb-12">
            @csrf

            <div class="grid md:grid-cols-2 mt-4 gap-4">
                <div class="flex flex-col items-baseline">
                    <x-input type="text" id="title" :label="__('Subject')" name="title" value="{{ $ticket->title }}"
                        required class="mt-2 w-full" icon="ri-pencil-line" />

                    <x-input type="select" name="priority" :label="__('Priority')" icon="ri-bar-chart-line"
                        class="mt-2 w-full">
                        <option value="low" @if ($ticket->priority == "low") selected @endif>
                            {{ __('Low') }}</option>
                        <option value="medium" @if ($ticket->priority == "medium") selected @endif>
                            {{ __('Medium') }}</option>
                        <option value="high" @if ($ticket->priority == "high") selected @endif>
                            {{ __('High') }}</option>
                    </x-input>

                    <x-input type="select" name="status" :label="__('Status')" icon="ri-bar-chart-line"
                        class="mt-2 w-full">
                        <option value="open" @if ($ticket->status == 'open') selected @endif>
                            {{ __('Open') }}</option>
                        <option value="closed" @if ($ticket->status == 'closed') selected @endif>
                            {{ __('Closed') }}</option>
                    </x-input>
                </div>
                <div class="flex flex-col items-baseline">
                    <x-input type="text" id="user" :label="__('User')" name="user"
                        value="{{ $ticket->user->name }} (#{{ $ticket->user->id }})" required class="mt-2 w-full"
                        icon="ri-user-line" readonly />
                    <x-input type="select" id="product" name="product_id" :label="__('Product')"
                        icon="ri-checkbox-circle-line" class="mt-2 w-full">
                        <option value="">{{ __('None') }}</option>
                        @foreach ($ticket->user->orderProducts()->with('product')->get() as $product)
                        <option value="{{ $product->id }}" @if ($product->id == $ticket->order_id) selected @endif>
                            {{ $product->id }} - {{ $product->product->name }}
                            @endforeach
                    </x-input>

                    <x-input type="select" id="assigned_to" name="assigned_to" :label="__('Assigned To')"
                        icon="ri-user-line" class="mt-2 w-full">
                        <option value="">{{ __('None') }}</option>
                        @foreach (App\Models\User::where('role_id', '!=', 2)->with('role')->get() as $user)
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
    </x-modal>
    @php $messages = $ticket->messages()->with('user')->get(); @endphp
    @empty($messages)
    <div class="ml-10 flex items-baseline ">
        <p class="dark:text-darkmodetext text-gray-600 px-3 rounded-md text-xl m-4">
            {{ __('No messages yet') }}
        </p>
    </div>
    @endempty
    <livewire:admin.tickets.show :ticket="$ticket" />   
</x-admin-layout>