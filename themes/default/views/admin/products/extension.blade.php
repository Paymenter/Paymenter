<x-admin-layout>
    <!-- edit extension settings -->
    <x-slot name="title">
        {{ __('Products') }}
    </x-slot>

    <!-- edit extension settings -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg dark:bg-darkmode2 dark:shadow-gray-700">
                <div class="p-6 sm:px-20 bg-white dark:bg-darkmode2">
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
                                <select id="server" class="block mt-1 w-full dark:bg-darkmode" name="server_id"
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
                                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md" />
                                            @elseif($setting->type == 'boolean')
                                                <input type="checkbox" name="{{ $setting->name }}" value="1"
                                                    @if (App\Helpers\ExtensionHelper::getProductConfig($extension->name, $setting->name, $product->id) == 1) checked @endif
                                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block sm:text-sm dark:bg-darkmode rounded-md" />
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endisset
                        </form>

                        <div class="flex items-center justify-end mt-4">
                            <button
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded dark:text-darkmodetext">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
