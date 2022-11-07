<x-admin-layout>
    <x-slot name="title">
        {{ __('Edit') }}
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg dark:bg-darkmode2">
                <div class="p-6 bg-white border-b border-gray-200 sm:px-20 dark:bg-darkmode2">
                    <div class="mt-8 text-2xl dark:text-darkmodetext">
                        Edit {{ $extension->name }}
                    </div>
                    <x-success class="mb-4" />
                    <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2">
                        <form method="POST" action="{{ route('admin.extensions.update', [$extension->type, $extension->name ]) }}">
                            @csrf
                            <!-- disable or enable extension -->
                            <div class="mt-4">
                                <label for="enabled">{{ __('Enabled') }}</label>
                                <select id="enabled" class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode" name="enabled" required>
                                    @if ($extension->enabled == 1)
                                        <option value="1" selected>True</option>
                                        <option value="0">False</option>
                                    @else
                                        <option value="1">True</option>
                                        <option value="0" selected>False</option>
                                    @endif
                                </select>
                            </div>
                            @foreach ($extension->config as $setting)
                                <div class="mt-4">
                                    <label for="{{ $setting->name }}">{{ $setting->friendlyName  }}</label>
                                    @if($setting->type == 'text')
                                        <input type="text" name="{{ $setting->name }}" value="{{ App\Helpers\ExtensionHelper::getConfig($extension->name, $setting->name) }}" class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode" @if($setting->required) required @endif />
                                    @elseif($setting->type == 'boolean')
                                        <input type="checkbox" name="{{ $setting->name }}" value="1" @if( App\Helpers\ExtensionHelper::getConfig($extension->name, $setting->name) == 1) checked @endif class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode" @if($setting->required) required @endif />
                                    @elseif($setting->type == 'dropdown')
                                        <select name="{{ $setting->name }}[]" multiple class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode" @if($setting->required) required @endif>
                                            @foreach($setting->options as $option)
                                                <option value="{{ $option }}" @if( in_array($option, array(App\Helpers\ExtensionHelper::getConfig($extension->name, $setting->name))) ) selected @endif>{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            @endforeach
                            <div class="flex items-center justify-end mt-4">
                                <button type="submit"
                                    class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
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
