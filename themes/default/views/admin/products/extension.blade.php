<x-admin-layout>
    <!-- edit extension settings -->
    <x-slot name="title">
        {{ __('Products') }}
    </x-slot>

    <!-- edit extension settings -->
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg dark:bg-darkmode2 dark:shadow-gray-700">
                <div class="p-6 bg-white sm:px-20 dark:bg-darkmode2">
                    <div class="mt-8 text-2xl dark:text-darkmodetext">
                        Update product server: {{ $product->name }}
                    </div>
                    <x-success class="mb-4" />
                    <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2">
                        <form method="POST" action="{{ route('admin.products.extension.update', $product->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div>
                                <label for="server">{{ __('Server') }}</label>
                                <select id="server" class="block w-full mt-1 dark:bg-darkmode" name="server_id"
                                    required>
                                    @if ($extensions->count())
                                        @foreach ($extensions as $server)
                                            @if ($server->id == $product->server_id)
                                                <option value="{{ $server->id }}" selected>{{ $server->name }}
                                                </option>
                                            @else
                                                <option value="{{ $server->id }}">{{ $server->name }}</option>
                                            @endif
                                        @endforeach
                                    @else
                                        <option value="">No servers found</option>
                                    @endif
                                </select>
                            </div>
                            @isset($extension)
                                <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2">
                                    @foreach ($extension->productConfig as $setting)
                                        <div class="mt-4">
                                            <label for="{{ $setting->name }}">{{ $setting->friendlyName }}</label>
                                            @if ($setting->type == 'text')
                                                <input type="text" name="{{ $setting->name }}"
                                                    value="{{ App\Helpers\ExtensionHelper::getProductConfig($extension->name, $setting->name, $product->id) }}"
                                                    class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode" />
                                            @elseif($setting->type == 'boolean')
                                                <input type="checkbox" name="{{ $setting->name }}" value="1"
                                                    @if (App\Helpers\ExtensionHelper::getProductConfig($extension->name, $setting->name, $product->id) == 1) checked @endif
                                                    class="block rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode" />
                                            @elseif($setting->type == 'dropdown')
                                                <select name="{{ $setting->name }}"
                                                    class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode">
                                                    @foreach ($setting->options as $option)
                                                        <option value="{{ $option->value }}"
                                                            @if (App\Helpers\ExtensionHelper::getProductConfig($extension->name, $setting->name, $product->id) == $option->value) selected @endif>
                                                            {{ $option->name }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endisset

                            <div class="flex items-center justify-end mt-4" type="submit">
                                <button
                                    class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700 dark:text-darkmodetext">
                                    {{ __('Update') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
