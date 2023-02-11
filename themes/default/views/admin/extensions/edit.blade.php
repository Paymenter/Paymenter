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
                                    <label for="{{ $setting->name }}" class="flex items-center space-x-1">
                                        <span>
                                        {{ $setting->friendlyName }}
                                        </span>
                                        @isset($setting->description)
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="w-5 h-5 mr-1 text-gray-400 transition duration-150 ease-in-out cursor-help fill-current dark:text-darkmodetext" viewBox="0 0 16 16" data-tooltip-target="{{ $setting->name }}">
                                              <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                              <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
                                            </svg>
                                            <div id="{{ $setting->name }}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                                {{ $setting->description }}
                                                <div class="tooltip-arrow" data-popper-arrow></div>
                                            </div>
                                        </span>
                                        @endisset
                                    </label>
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
