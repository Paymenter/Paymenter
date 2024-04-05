<x-admin-layout title="Edit client">
    <div class="w-full h-full rounded mb-4">
        <div class="mx-auto">
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
            <div class="absolute hidden w-max origin-top-right bg-white rounded-md shadow-lg dark:bg-darkmode ring-1 ring-black ring-opacity-5 z-20"
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
                            <x-input name="first_name" id="first_name" label="{{ __('First name') }}" type="text"
                                value="{{ $user->first_name }}" />
                            <x-input name="last_name" id="last_name" label="{{ __('Last name') }}" type="text"
                                value="{{ $user->last_name }}" />

                            <x-input name="email" id="email" label="{{ __('Email') }}" type="email"
                                value="{{ $user->email }}" />

                            <x-input name="phone" id="phone" label="{{ __('Phone') }}" type="text"
                                value="{{ $user->phone }}" />

                            <x-input name="companyname" id="companyname" label="{{ __('Company Name') }}"
                                type="text" value="{{ $user->companyname }}" />
                        </div>

                        <hr class="my-6 border-b-1 border-gray-300 dark:border-gray-600" />

                        <div class="grid grid-cols-3 gap-4">
                            <x-input name="address" id="address" label="{{ __('Address') }}" type="text"
                                value="{{ $user->address }}" />

                            <x-input name="city" id="city" label="{{ __('City') }}" type="text"
                                value="{{ $user->city }}" />

                            <x-input name="state" id="state" label="{{ __('State') }}" type="text"
                                value="{{ $user->state }}" />
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <x-input name="country" id="country" label="{{ __('Country') }}" type="select">
                                <option value="">{{ __('None') }}</option>
                                @foreach (App\Classes\Constants::countries() as $key => $country)
                                    <option value="{{ $key }}" @if ($user->country == $key) selected @endif>
                                        {{ $country }}
                                    </option>
                                @endforeach
                            </x-input>

                            <x-input name="zip" id="zip" label="{{ __('Zip') }}" type="text"
                                value="{{ $user->zip }}" />
                        </div>

                        <hr class="my-6 border-b-1 border-gray-300 dark:border-gray-600" />

                        <x-input type="number" name="credits" step="0.01" id="credits"
                            label="{{ __('Credits') }}" value="{{ $user->credits }}" />

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
        </div>
    </div>
</x-admin-layout>
