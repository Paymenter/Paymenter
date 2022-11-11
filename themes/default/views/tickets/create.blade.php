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
                        <div class="flex">
                            <form method="POST" action="{{ route('tickets.store') }}" style="width: 100%">
                                @csrf
                                <div style="width: 50%; float: left;">
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-darkmodetext"
                                            for="title">
                                            {{ __('Title') }}
                                        </label>
                                        <input
                                            class="block w-full mt-1 rounded-md shadow-sm form-input dark:text-darkmodetext dark:bg-darkmode"
                                            id="title" type="text" name="title" value="{{ old('title') }}" required
                                            autofocus />
                                    </div>
                                    <!-- Category -->
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-darkmodetext"
                                            for="service">
                                            {{ __('Category') }}
                                        </label>
                                        <select id="priority" name="priority"
                                            class="block w-full mt-1 rounded-md shadow-sm form-input dark:text-darkmodetext dark:bg-darkmode">
                                            <option value="low" @if (old('priority')==1) selected @endif>
                                                {{ __('Support') }}</option>
                                            <option value="medium" @if (old('priority')==2) selected @endif>
                                                {{ __('Sales') }}</option>
                                            <option value="high" @if (old('priority')==3) selected @endif>
                                                {{ __('Other') }}</option>
                                        </select>
                                    </div>
                                    <!-- Related service -->
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-darkmodetext"
                                            for="service">
                                            {{ __('Related service') }}
                                        </label>
                                        <select id="service" name="service"
                                            class="block w-full mt-1 rounded-md shadow-sm form-input dark:text-darkmodetext dark:bg-darkmode">
                                            @if (count($services) > 0)
                                            <option value="low" @if (old('service')==1) selected @endif>
                                                {{ __('None') }}
                                            </option>
                                            @foreach($services as $service)
                                            @foreach($service->products as $product)
                                            @php $product = App\Models\Products::where("id",
                                            $product["id"])->get()->first()
                                            @endphp
                                            <option value="low" @if (old('service')==1) selected @endif>
                                                {{ ucfirst($product->name) }} - ({{ ucfirst($service->status) }})
                                            </option>
                                            </option>
                                            @endforeach
                                            @endforeach
                                            @else
                                            <option value="low" @if (old('service')==1) selected @endif>
                                                {{ __('None') }}</option>
                                            @endif
                                        </select>
                                    </div>
                                    <!-- priority high/medium/low -->
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-darkmodetext"
                                            for="priority">
                                            {{ __('Priority') }}
                                        </label>
                                        <select id="priority" name="priority"
                                            class="block w-full mt-1 rounded-md shadow-sm form-input dark:text-darkmodetext dark:bg-darkmode">
                                            <option value="low" @if (old('priority')==1) selected @endif>
                                                {{ __('Low Priority') }}</option>
                                            <option value="medium" @if (old('priority')==2) selected @endif>
                                                {{ __('Normal Priority') }}</option>
                                            <option value="high" @if (old('priority')==3) selected @endif>
                                                {{ __('High Priority') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div style="width: 50%; float: right; padding-left: 2rem;">
                                    @if (App\Models\Settings::first()->recaptcha == 1)
                                    <div class="g-recaptcha"
                                        data-sitekey="{{ App\Models\Settings::first()->recaptcha_site_key }}"></div>
                                    @endif
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-darkmodetext"
                                            for="description">
                                            {{ __('Description') }}
                                        </label>
                                        <textarea
                                            class="block w-full mt-1 rounded-md shadow-sm form-input dark:text-darkmodetext dark:bg-darkmode"
                                            style="height: 288px" id="description" name="description"
                                            required>{{ old('description') }}</textarea>
                                    </div>
                                    <div class="flex items-center justify-end mt-4">
                                        <button
                                            class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                                            {{ __('Create') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>