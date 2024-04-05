<x-admin-layout title="Products/Services of {{ $user->name }}">
    <div class="w-full h-full">
        <div class="pb-6 mx-auto">
            <div class="flex flex-row overflow-x-auto lg:flex-wrap lg:space-x-1">
                <div class="flex-none">
                    <a href="{{ route('admin.clients.edit', $user->id) }}"
                        class="inline-flex justify-center w-full p-4 px-2 py-2 text-xs font-bold text-gray-900 uppercase border-b-2 dark:text-darkmodetext dark:hover:bg-darkbutton hover:border-logo hover:text-logo @if (request()->routeIs('admin.clients.edit')) border-logo @else border-y-transparent @endif">
                        {{ __('Client Details') }}
                    </a>
                </div>
                <div class="flex-none">
                    <a href="{{ route('admin.clients.products', $user->id) }}"
                        class="inline-flex justify-center w-full p-4 px-2 py-2 text-xs font-bold text-gray-900 uppercase border-b-2 dark:text-darkmodetext dark:hover:bg-darkbutton hover:border-logo hover:text-logo @if (request()->routeIs('admin.clients.products*')) border-logo @else border-y-transparent @endif">
                        {{ __('Products/Services') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script>
        function change() {
            window.location.href = "{{ route('admin.clients.products', $user->id) }}/" + document.getElementById('product')
                .value;
        }
    </script>
    <div class="mb-4">
        <x-input type="select" name="product" id="product" onchange="change()">
            <option value="0" selected disabled>{{ __('Select Product/Service') }}</option>
            @foreach ($orderProducts as $product)
                <option value="{{ $product->id }}" @if ($product->id === $orderProduct->id) selected @endif>
                    {{ $product->product->name }} ({{ $product->id }})
                </option>
            @endforeach
        </x-input>
    </div>
    <h1 class="text-2xl my-2">Order Product Details:</h1>
    @if($orderProduct->cancellation)
        <h3 class="text-lg border-b mb-1 border-gray-500 fon">Cancellation Request:</h3>
        <div class="flex flex-row gap-4 flex-wrap mb-2 items-center">
            <x-input type="text" disabled name="created_at" id="created_at"
                value="{{ $orderProduct->cancellation->created_at }}" label="Created At" />
            <x-input type="text" disabled name="reason" id="reason" value="{{ $orderProduct->cancellation->reason }}"
                label="Reason" />
            <form action="{{ route('admin.clients.products.removecancellation', [$user->id, $orderProduct->id]) }}"
                class="mt-6" method="POST">
                @csrf
                <button class="button button-danger">{{ __('Remove Cancellation Request') }}</button>
            </form>
        </div>
    @endif
    <form action="{{ route('admin.clients.products.update', [$user->id, $orderProduct->id]) }}" method="POST"
        class="flex flex-col gap-2">
        @csrf
        <div class="grid grid-cols-2 gap-8">
            <div class="flex flex-col gap-4">
                <h3 class="text-lg border-b mb-1 border-gray-500 fon">Order ID: <a
                        href="{{ route('admin.orders.show', $orderProduct->order->id) }}"
                        class="text-logo">#{{ $orderProduct->order->id }}</a></h3>
                <div class="flex gap-2">
                    <x-input type="select" name="action" id="action" label="Extension Settings" class="w-full"
                        onchange="document.getElementById('statusinput').value = this.value">
                        <option selected disabled>{{ __('Select Action') }}</option>
                        <option value="create">{{ __('Create') }}</option>
                        <option value="suspend">{{ __('Suspend') }}</option>
                        <option value="unsuspend">{{ __('Unsuspend') }}</option>
                        <option value="terminate">{{ __('Terminate') }}</option>
                        <option value="upgrade">{{ __('Upgrade') }}</option>
                    </x-input>
                    <button class="button button-primary h-fit self-end"
                        onclick="event.preventDefault(); document.getElementById('changestatus').submit();">{{ __('Go') }}</button>
                </div>
                <x-input type="text" disabled name="created_at" id="created_at"
                    value="{{ $orderProduct->created_at }}" label="Created At" />
                <x-input type="select" name="status" id="status" label="Status">
                    <option value="0" selected disabled>{{ __('Select Status') }}</option>
                    <option value="pending" {{ $orderProduct->status === 'pending' ? 'selected' : '' }}>
                        {{ __('Pending') }}</option>
                    <option value="paid" {{ $orderProduct->status === 'paid' ? 'selected' : '' }}>
                        {{ __('Paid') }}</option>
                    <option value="cancelled" {{ $orderProduct->status === 'cancelled' ? 'selected' : '' }}>
                        {{ __('Cancelled') }}</option>
                    <option value="suspended" {{ $orderProduct->status === 'suspended' ? 'selected' : '' }}>
                        {{ __('Suspended') }}</option>
                </x-input>
            </div>
            <div class="flex flex-col gap-4">
                <h3 class="text-lg border-b mb-1 border-gray-500 fon">Product ID: <a
                        href="{{ route('admin.products.edit', $orderProduct->product->id) }}"
                        class="text-logo">#{{ $orderProduct->product->id }}</a></h3>
                <x-input type="date" name="expiry_date" id="expiry_date"
                    value="{{ $orderProduct->expiry_date ? $orderProduct->expiry_date->format('Y-m-d') : '' }}"
                    label="Expiry Date" />
                <x-input type="number" name="quantity" id="quantity" value="{{ $orderProduct->quantity }}"
                    label="Quantity" />
                <x-input type="text" name="price" id="price" value="{{ $orderProduct->price }}"
                    label="Price" />
            </div>
        </div>
        <button class="button button-primary self-end">{{ __('Save') }}</button>
    </form>
    <form action="{{ route('admin.clients.products.changestatus', [$user->id, $orderProduct->id]) }}" method="POST"
        class="hidden" id="changestatus">
        @csrf
        <input type="hidden" name="status" id="statusinput">
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-4">
        <div>
            <h1 class="text-2xl my-2">{{ __('Invoices') }}</h1>
            <div class="flex flex-col gap-2">
                <table>
                    <thead>
                        <tr>
                            <th class="border-b border-gray-500">{{ __('Invoice ID') }}</th>
                            <th class="border-b border-gray-500">{{ __('Status') }}</th>
                            <th class="border-b border-gray-500">{{ __('Created At') }}</th>
                            <th class="border-b border-gray-500">{{ __('Total') }}</th>
                            <th class="border-b border-gray-500">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($orderProduct->getInvoices as $invoice)
                            <tr>
                                <td class="border-b border-gray-500"><a
                                        href="{{ route('admin.invoices.show', $invoice->id) }}"
                                        class="text-logo">#{{ $invoice->id }}</a></td>
                                <td class="border-b border-gray-500">{{ $invoice->status }}</td>
                                <td class="border-b border-gray-500">{{ $invoice->created_at }}</td>
                                <td class="border-b border-gray-500">
                                    <x-money :amount="$invoice->total()" />
                                </td>
                                <td class="border-b border-gray-500">
                                    <a href="{{ route('admin.invoices.show', $invoice->id) }}"
                                        class="button button-primary">{{ __('View') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
        <div>
            <div class="flex flex-row justify-between items-center">
                <h1 class="text-2xl my-2">{{ __('Configurable Options') }}</h1>
                <form action="{{ route('admin.clients.products.config.create', [$user->id, $orderProduct->id]) }}"
                    method="POST">
                    @csrf
                    <button class="button button-primary">{{ __('Add New') }}</button>
                </form>
            </div>
            @foreach ($configurableOptions as $configurableOption)
                <div class="flex flex-col md:flex-row justify-between items-center border-b border-gray-500 py-2">
                    @if ($configurableOption->configurableOption() && $configurableOption->configurableOption->type !== 'text')
                        <form
                            action="{{ route('admin.clients.products.config.update', [$user->id, $orderProduct->id, $configurableOption->id]) }}"
                            method="POST" class="flex-row flex items-center w-full gap-6">
                            @csrf
                            <x-input type="text" name="key" id="key"
                                value="{{ $configurableOption->configurableOption->name }}" disabled
                                label="Key" />
                            <x-input type="select" name="value" id="value"
                                value="{{ $configurableOption->configurableOptionInput->name }}" label="Value">
                                <option value="0" selected disabled>{{ __('Select Value') }}</option>
                                @foreach ($configurableOption->configurableOption->configurableOptionInputs as $configurableOptionInput)
                                    <option value="{{ $configurableOptionInput->id }}"
                                        @if ($configurableOption->configurableOptionInput->id === $configurableOptionInput->id) selected @endif>
                                        {{ $configurableOptionInput->name }}
                                    </option>
                                @endforeach
                            </x-input>
                            <button class="button button-primary self-end">Update</button>
                        </form>
                    @else
                        <form
                            action="{{ route('admin.clients.products.config.update', [$user->id, $orderProduct->id, $configurableOption->id]) }}"
                            method="POST" class="flex-row flex items-center w-full gap-6">
                            @csrf
                            <x-input type="text" name="key" id="key"
                                value="{{ $configurableOption->key }}" label="Key" />
                            <x-input type="text" name="value" id="value"
                                value="{{ $configurableOption->value }}" label="Value" />
                            <div class="flex flex-row gap-2 mt-6">
                                <button class="button button-primary self-end">Update</button>
                                <button class="button button-danger self-end"
                                    onclick="event.preventDefault(); document.getElementById('delete-form-{{ $configurableOption->id }}').submit();">Delete</button>
                            </div>
                        </form>
                        <form
                            action="{{ route('admin.clients.products.config.delete', [$user->id, $orderProduct->id, $configurableOption->id]) }}"
                            id="delete-form-{{ $configurableOption->id }}" method="POST">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</x-admin-layout>
