<x-admin-layout title="Edit client">
    <div class="w-full h-full rounded mb-4">
        <div class="px-6 mx-auto">
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
    <div class="grid grid-cols-1 md:grid-cols-2 mt-4">
        <div class="text-2xl dark:text-darkmodetext">
            {{ __('Edit client') }}
        </div>
        <div class="relative inline-block text-left justify-end">
            <button type="button"
                class="dark:hover:bg-darkmode absolute top-0 right-0 dark:text-darkmodetext dark:bg-secondary-100 inline-flex w-max justify-end bg-white px-2 py-2 text-base font-medium rounded-md text-gray-700 mr-4"
                id="menu-button" aria-expanded="true" aria-haspopup="true" data-dropdown-toggle="moreOptions">
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z">
                    </path>
                </svg>
            </button>
            <div class="absolute hidden w-max origin-top-right bg-white rounded-md shadow-lg dark:bg-darkmode ring-1 ring-black ring-opacity-5 z-[1]"
                role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1"
                id="moreOptions">
                <div class="py-1 grid grid-cols-1" role="none">
                    <a href="{{ route('admin.clients.loginasclient', $user->id) }}"
                        class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-darkmode2 hover:bg-gray-100 hover:text-gray-900"
                        role="menuitem" tabindex="-1" id="menu-item-0">{{ __('Login as client') }}</a>
                    <button
                        class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-darkmode2 hover:bg-gray-100 hover:text-red-900 dark:hover:text-red-300"
                        role="menuitem" tabindex="-1" id="menu-item-0"
                        onclick="document.getElementById('delete').submit()">
                        {{ __('Delete') }}
                    </button>
                </div>
            </div>
            <form action="{{ route('admin.clients.delete', $user->id) }}" method="POST" id="delete">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
    <script>
        document.getElementById('delete').addEventListener('submit', function(e) {
            var form = this;
            e.preventDefault();
            Swal.fire({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this client!",
                icon: "warning",
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                showCloseButton: true,
                showCancelButton: true,
            }).then((willDelete) => {
                if (willDelete.isConfirmed) {
                    form.submit();
                } else {
                    swal.fire("Your client is safe!");
                }
            });
        });
    </script>

    <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1">
        <div class="dark:bg-secondary-100 p-6">
            <div class="flex items-center">
                <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold w-full">
                    <form method="POST" action="{{ route('admin.clients.update', $user->id) }}">
                        @csrf
                        <div class="grid grid-cols-2 gap-4 mt-4 dark:text-white">
                            <div>
                                <label for="name"
                                    class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                    {{ __('Name') }}
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="name" value="{{ $user->name }}" id="name"
                                        placeholder="John Doe" autocomplete="name" required
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                </div>
                            </div>
                            <div>
                                <label for="email"
                                    class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                    {{ __('Email') }}
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="email" id="email" value="{{ $user->email }}"
                                        placeholder="jdoe@example.com" autocomplete="email" required
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                </div>
                            </div>
                            <div>
                                <label for="phone"
                                    class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                    {{ __('Phone') }}
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="phone" value="{{ $user->phone }}"
                                        placeholder="+1-234-567-89" id="phone" autocomplete="phone"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                </div>
                            </div>
                            <div>
                                <label for="companynames"
                                    class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                    {{ __('Company Names') }}
                                </label>
                                <div class="mt-1">
                                    <input type="text" placeholder="Optional" value="{{ $user->companynames }}"
                                        name="companynames" id="companynames" autocomplete="companynames"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                </div>
                            </div>
                        </div>

                        <hr class="my-6 border-b-1 border-gray-300 dark:border-gray-600" />

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label for="address"
                                    class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                    {{ __('Address') }}
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="address" value="{{ $user->address }}" id="address"
                                        placeholder="Bobcat Lane" autocomplete="address"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                </div>
                            </div>
                            <div>
                                <label for="city"
                                    class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                    {{ __('City') }}
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="city" value="{{ $user->city }}" id="city"
                                        placeholder="St. Robert" autocomplete="city"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                </div>
                            </div>
                            <div>
                                <label for="state"
                                    class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                    {{ __('State') }}
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="state" value="{{ $user->state }}" id="state"
                                        placeholder="Missouri" autocomplete="state"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="zip"
                                    class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                    {{ __('Zip') }}
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="zip" value="{{ $user->zip }}" id="zip"
                                        placeholder="1234 NW" autocomplete="zip"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                </div>
                            </div>
                            <div>
                                <label for="country"
                                    class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                    {{ __('Country') }}
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="country" value="{{ $user->country }}"
                                        id="country" placeholder="United States" autocomplete="country"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                </div>
                            </div>
                        </div>

                        <hr class="my-6 border-b-1 border-gray-300 dark:border-gray-600" />

                        <x-input type="select" name="role" id="role" label="{{ __('Role(admin)') }}">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" @if ($user->role->id == $role->id) selected @endif>
                                    {{ $role->name }}
                                    @if ($role->id == 2)
                                        {{ __('(Default, Client)') }}
                                    @endif
                                    @if ($role->id == 1)
                                        {{ __('(Full Administrator)') }}
                                    @endif
                                </option>
                            @endforeach
                        </x-input>
                        <hr class="my-6 border-b-1 border-gray-300 dark:border-gray-600" />
                        <div class="flex items-center justify-end mt-4">
                            <button type="submit"
                                class="inline-flex justify-center w-max float-right button button-primary">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <br /> <br />
            <!-- His/her orders -->
            <div class="rounded-t mb-0 px-4 py-3 border-0">
                <div class="flex flex-wrap items-center">
                    <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                        <h3 class="font-semibold text-lg text-gray-800 dark:text-darkmodetext">
                            {{ __('Orders') }}
                        </h3>
                    </div>
                </div>
            </div>
            <table id="orders" class="table-auto w-full">
                <thead>
                    <tr>
                        <th>
                            {{ __('ID') }}
                        </th>
                        <th>
                            {{ __('Price') }}
                        </th>
                        <th>
                            {{ __('Total') }}
                        </th>
                        <th>
                            {{ __('Created At') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($user->orders as $order)
                        <tr onclick="window.location.href = '{{ route('admin.orders.show', $order->id) }}';"
                            class="cursor-pointer">
                            <td>
                                {{ $order->id }}
                            </td>
                            <td>
                                {{ $order->status }}
                            </td>
                            <td>
                                {{ $order->total() }}
                            </td>
                            <td>
                                {{ $order->created_at }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
        <script type="text/javascript"
            src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/datatables.min.js">
        </script>
        <script>
            $(document).ready(function() {
                var table = $('#orders').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ]
                });
            });
        </script>
    </div>
</x-admin-layout>
