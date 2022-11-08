<x-app-layout>
        <x-slot name="title">
            {{ __('Create Ticket') }}
        </x-slot>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg dark:bg-darkmode2">
                    <x-success class="mb-4" />
                    <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 dark:bg-darkmode2">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold dark:text-darkmodetext">
                                    {{ __('Ticket') }}</div>
                            </div>
                            <div class="ml-5">
                                <div class="mt-2 text-sm text-gray-500 dark:text-darkmodetext">
                                    {{ __('Create a new ticket.') }}
                                </div>
                            </div>
                            <div class="flex">
                                <form method="POST" action="{{ route('tickets.store') }}" style="width: 50%">
                                    @csrf
                                    <div class="mt-4">
                                        <label class="block font-medium text-sm text-gray-700 dark:text-darkmodetext"
                                            for="title">
                                            {{ __('Title') }}
                                        </label>
                                        <input
                                            class="form-input rounded-md shadow-sm mt-1 block w-full dark:text-darkmodetext dark:bg-darkmode"
                                            id="title" type="text" name="title" value="{{ old('title') }}"
                                            required autofocus />
                                    </div>
                                    <!-- Category -->
                                    <div class="mt-4">
                                        <label class="block font-medium text-sm text-gray-700 dark:text-darkmodetext"
                                            for="service">
                                            {{ __('tickets.category') }}
                                        </label>
                                        <select id="priority" name="priority"
                                            class="form-input rounded-md shadow-sm mt-1 block w-full dark:text-darkmodetext dark:bg-darkmode">
                                            <option value="low" @if (old('priority') == 1) selected @endif>
                                                {{ __('tickets.category_support') }}</option>
                                            <option value="medium" @if (old('priority') == 2) selected @endif>
                                                {{ __('tickets.category_sales') }}</option>
                                            <option value="high" @if (old('priority') == 3) selected @endif>
                                                {{ __('tickets.category_other') }}</option>
                                        </select>
                                    </div>
                                    <!-- Related service -->
                                    <div class="mt-4">
                                        <label class="block font-medium text-sm text-gray-700 dark:text-darkmodetext"
                                            for="service">
                                            {{ __('tickets.related_service') }}
                                        </label>
                                        <select id="service" name="service"
                                            class="form-input rounded-md shadow-sm mt-1 block w-full dark:text-darkmodetext dark:bg-darkmode">
                                            @if (count($services) > 0)
                                                <option value="low" @if (old('service') == 1) selected @endif>
                                                    {{ __('tickets.none') }}
                                                </option>
                                                @foreach($services as $service)
							    	                @foreach($service->products as $product)
							    	                	@php $product = App\Models\Products::where("id", $product["id"])->get()->first() @endphp
                                                        <option value="low" @if (old('service') == 1) selected @endif>
                                                            {{ ucfirst($product->name) }} - ({{ ucfirst($service->status) }})</option>
                                                        </option>
                                                    @endforeach
                                                @endforeach
                                            @else
                                                <option value="low" @if (old('service') == 1) selected @endif>
                                                    {{ __('tickets.none') }}</option>
                                            @endif
                                        </select>
                                    </div>
                                    <!-- priority high/medium/low -->
                                    <div class="mt-4">
                                        <label class="block font-medium text-sm text-gray-700 dark:text-darkmodetext"
                                            for="priority">
                                            {{ __('tickets.priority') }}
                                        </label>
                                        <select id="priority" name="priority"
                                            class="form-input rounded-md shadow-sm mt-1 block w-full dark:text-darkmodetext dark:bg-darkmode">
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
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                            {{ __('normal.create') }}
                                        </button>
                                    </div>
                                </form>
                                <form method="POST" action="{{ route('tickets.store') }}" style="padding-left: 20px; width: 50%">
                                    @csrf
                                    <div class="mt-4">
                                        <label class="block font-medium text-sm text-gray-700 dark:text-darkmodetext"
                                            for="description">
                                            {{ __('normal.description') }}
                                        </label>
                                        <textarea class="form-input rounded-md shadow-sm mt-1 block w-full dark:text-darkmodetext dark:bg-darkmode" style="height: 300px"
                                            id="description" name="description" required>{{ old('description') }}</textarea>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
