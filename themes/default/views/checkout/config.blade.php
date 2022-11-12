<x-app-layout>
    <x-slot name="title">
        {{ __('Checkout') }}
    </x-slot>
    <!-- form to configure the product -->
    <x-success class="m-2 mb-4" />

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg dark:bg-darkmode2 dark:shadow-gray-700">
                <div class="p-6 bg-white sm:px-20 dark:bg-darkmode2">
                    <form method="POST" action="{{ route('checkout.config', $product->id) }}">
                        @csrf
                        <div
                            class="items-center px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-darkmodetext">
                            @foreach ($userConfig as $config)
                                @if ($config->type == 'text')
                                    <div class="flex flex-col w-full">
                                        <label class="font-medium" for="{{ $config->name }}">{{ ucfirst($config->name) }}</label>
                                        <input
                                            class="w-full mt-1 rounded-md shadow-sm form-input dark:text-darkmodetext dark:bg-darkmode"
                                            id="{{ $config->name }}" type="text" name="{{ $config->name }}"
                                            value="{{ old($config->name) }}" required />
                                    </div>
                                @elseif($config->type == 'textarea')
                                    <div class="flex flex-col w-full">
                                        <label class="font-medium" for="{{ $config->name }}">{{ ucfirst($config->name) }}</label>
                                        <textarea class="w-full mt-1 rounded-md shadow-sm form-input dark:text-darkmodetext dark:bg-darkmode"
                                            id="{{ $config->name }}" name="{{ $config->name }}" required>{{ old($config->name) }}</textarea>
                                    </div>
                                @elseif($config->type == 'dropdown')
                                    <div class="flex flex-col w-full">
                                        <label class="font-medium"
                                            for="{{ $config->name }}">{{ ucfirst($config->name) }}</label>
                                        <select id="{{ $config->name }}" name="{{ $config->name }}"
                                            class="w-full mt-1 rounded-md shadow-sm form-input dark:text-darkmodetext dark:bg-darkmode">
                                            @foreach ($config->options as $option)
                                                <option value="{{ $option->value }}"
                                                    @if (old($config->name) == $option) selected @endif>
                                                    {{ $option->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            @endforeach
                            <button type="submit"
                                class="w-full px-4 py-2 mt-4 font-semibold text-white bg-blue-500 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75">
                                {{ __('Continue') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</x-app-layout>
