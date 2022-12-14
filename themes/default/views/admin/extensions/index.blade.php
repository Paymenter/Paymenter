<x-admin-layout>
    <x-slot name="title">
        {{ __('Extensions') }}
    </x-slot>
    <!-- get all server extensions -->
    <div class="dark:bg-darkmode py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="dark:bg-darkmode2 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="dark:bg-darkmode2 p-6 bg-white">
                    <div class="w-full">
                        <table class="min-w-full divide-y divide-gray-200 w-full" id="table">
                            <div class="m-10 flex items-baseline ">
                                <h2 class="text-2xl font-semibold leading-tight text-gray-800 dark:text-darkmodetext">
                                    {{ __('Extensions and Gateways') }}
                                </h2>
                            </div>
                            @if (!$servers)
                                <!-- not found -->
                                    <p
                                        class="dark:bg-darkmode2 dark:text-darkmodetext text-gray-600 px-3 rounded-md text-xl m-4">
                                        {{ __('No extensions found') }}
                                    </p>
                            @else
                                <thead class="dark:bg-darkmode bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Name') }}</th>
                                        <th scope="col"
                                            class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Enabled?') }}</th>
                                        <th scope="col"
                                            class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Edit') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="dark:bg-darkmode bg-white divide-y divide-gray-200">
                                    <tr class="dark:bg-darkmode">
                                        <td colspan="3" class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-gray-500 font-bold text-lg text-center">Servers</td>
                                    </tr>
                                    @foreach ($servers as $extensio)
                                        @if ($extensio == '.' || $extensio == '..')
                                            @continue
                                        @endif
                                        <tr class="dark:bg-darkmode">
                                            <td
                                                class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $extensio }}</td>
                                            <td
                                                class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if (App\Models\Extensions::where('name', $extensio)->first()->enabled == 1)
                                                    {{ __('Yes') }}
                                                @else
                                                    {{ __('No') }}
                                                @endif
                                            </td>
                                            <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <a href="{{ route('admin.extensions.edit', ['server', $extensio]) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 hover:bg-button p-2 rounded-lg">{{ __('Edit') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="dark:bg-darkmode">
                                        <td colspan="3" class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-gray-500 font-bold text-lg text-center">Gateways</td>
                                    </tr>
                                    @foreach ($gateways as $gateway)
                                        @if ($gateway == '.' || $gateway == '..')
                                            @continue
                                        @endif
                                        <tr>
                                            <td
                                                class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $gateway }}</td>
                                            <td
                                                class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if (App\Models\Extensions::where('name', $extensio)->first()->enabled == 1)
                                                    {{ __('Yes') }}
                                                @else
                                                    {{ __('No') }}
                                                @endif
                                            </td>
                                            <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <a href="{{ route('admin.extensions.edit', ['gateway', $gateway]) }}"
                                                    class="dark:bg-darkmodebutton text-indigo-600 hover:text-indigo-900 hover:bg-button p-2 rounded-lg">{{ __('Edit') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
</x-admin-layout>
