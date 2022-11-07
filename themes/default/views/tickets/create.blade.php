    <x-app-layout>
        <x-slot name="title">
            {{ __('Create Ticket') }}
        </x-slot>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg dark:bg-darkmode2">
                    <x-success class="mb-4" />
                    <div class="grid grid-cols-1 bg-gray-200 bg-opacity-25 dark:bg-darkmode2">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="ml-4 text-lg font-semibold leading-7 text-gray-600 dark:text-darkmodetext">
                                    {{ __('Ticket') }}</div>
                            </div>
                            <div class="ml-5">
                                <div class="mt-2 text-sm text-gray-500 dark:text-darkmodetext">
                                    {{ __('Create a new ticket.') }}
                                </div>
                            </div>
                            <form method="POST" action="{{ route('tickets.store') }}">
                                @csrf
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-darkmodetext"
                                        for="title">
                                        {{ __('Title') }}
                                    </label>
                                    <input
                                        class="block w-full mt-1 rounded-md shadow-sm form-input dark:text-darkmodetext dark:bg-darkmode"
                                        id="title" type="text" name="title" value="{{ old('title') }}"
                                        required autofocus />
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-darkmodetext"
                                        for="description">
                                        {{ __('normal.description') }}
                                    </label>
                                    <textarea class="block w-full mt-1 rounded-md shadow-sm form-input dark:text-darkmodetext dark:bg-darkmode"
                                        id="description" name="description" required>{{ old('description') }}</textarea>
                                </div>
                                <!-- priority high/medium/low -->
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-darkmodetext"
                                        for="priority">
                                        {{ __('tickets.priority') }}
                                    </label>
                                    <select id="priority" name="priority"
                                        class="block w-full mt-1 rounded-md shadow-sm form-input dark:text-darkmodetext dark:bg-darkmode">
                                        <option value="low" @if (old('priority') == 1) selected @endif>
                                            {{ __('tickets.priority_low') }}</option>
                                        <option value="medium" @if (old('priority') == 2) selected @endif>
                                            {{ __('tickets.priority_medium') }}</option>
                                        <option value="high" @if (old('priority') == 3) selected @endif>
                                            {{ __('tickets.priority_high') }}</option>
                                    </select>
                                </div>
                                <br>
                                @if (App\Models\Settings::first()->recaptcha == 1)
                                    <div class="g-recaptcha"
                                        data-sitekey="{{ App\Models\Settings::first()->recaptcha_site_key }}"></div>
                                @endif
                                <div class="flex items-center justify-end mt-4">
                                    <button
                                        class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                                        {{ __('normal.create') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
