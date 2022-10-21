<x-admin-layout>
    <x-slot name="title">
        {{ __('Clients') }}
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Clients') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-lg">
                <div class="dark:bg-darkmode2 p-6 sm:px-20 bg-white">
                    <x-success class="mb-4"/>
                    <div class="dark:text-darkmodetext mt-8 text-2xl">
                        {{ __('Edit client') }}
                    </div>
                    <div class="dark:text-darkmodetext mt-6 text-gray-500">
                        {{ __('Here you can edit a client.') }}
                    </div>
                    <div class="flex justify-end mt-4 mr-4">
                        <form action="{{ route('admin.clients.delete', $user->id) }}" method="POST" id="delete">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Delete client') }}
                            </button>
                        </form>
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

                </div>
                <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1">
                    <div class="dark:bg-darkmode2 p-6">
                        <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold w-full">
                                <form method="POST" action="{{ route('admin.clients.update', $user->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="mt-4">
                                        <label for="name" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                            {{ __('normal.name') }}
                                        </label>
                                        <div class="mt-1">
                                            <input type="text" name="name" id="name" autocomplete="name"
                                                value="{{ $user->name }}"
                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <label for="email" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                            {{ __('Email') }}
                                        </label>
                                        <div class="mt-1">
                                            <input type="text" name="email" id="email" autocomplete="email"
                                                value="{{ $user->email }}"
                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <label for="address" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                            {{ __('Address') }}
                                        </label>
                                        <div class="mt-1">
                                            <input type="text" name="address" id="address" autocomplete="address"
                                                value="{{ $user->address }}"
                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <label for="city" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                            {{ __('City') }}
                                        </label>
                                        <div class="mt-1">
                                            <input type="text" name="city" id="city" autocomplete="city"
                                                value="{{ $user->city }}"
                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <label for="state" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                            {{ __('State') }}
                                        </label>
                                        <div class="mt-1">
                                            <input type="text" name="state" id="state" autocomplete="state"
                                                value="{{ $user->state }}"
                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <label for="zip" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                            {{ __('Zip') }}
                                        </label>
                                        <div class="mt-1">
                                            <input type="text" name="zip" id="zip" autocomplete="zip"
                                                value="{{ $user->zip }}"
                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <label for="phone" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                            {{ __('Phone') }}
                                        </label>
                                        <div class="mt-1">
                                            <input type="text" name="phone" id="phone" autocomplete="phone"
                                                value="{{ $user->phone }}"
                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                        </div>
                                    </div>

                                    <!-- companyname -->
                                    <div class="mt-4">
                                        <label for="companyname" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                            {{ __('Company Name') }}
                                        </label>
                                        <div class="mt-1">
                                            <input type="text" name="companyname" id="companyname"
                                                autocomplete="companyname" value="{{ $user->companyname }}"
                                                class="dark:bg-darkmode shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm rounded-md">
                                        </div>
                                    </div>
                                    <!-- submmit -->
                                    <div class="flex items-end justify-end mt-4">
                                        <button type="submit"
                                            class="dark:text-darkmodetext inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            {{ __('Update') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
