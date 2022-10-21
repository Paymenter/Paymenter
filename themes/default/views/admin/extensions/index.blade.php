<x-admin-layout>
    <x-slot name="title">
        {{ __('Extensions') }}
    </x-slot>
    <!-- get all server extensions -->
    <div class="dark:bg-darkmode py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="dark:bg-darkmode2 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="dark:bg-darkmode2 p-6 bg-white">
                    <div class="flex flex-wrap">
                        <table class="min-w-full divide-y divide-gray-200">
                            @if (!$servers)
                                <!-- not found -->
                                <div class="ml-10 flex items-baseline ">
                                    <p class="dark:bg-darkmode2 dark:text-darkmodetext text-gray-600 px-3 rounded-md text-xl m-4">
                                        {{ __('No extensions found') }}
                                    </p>
                                </div>
                            @else
                                <thead class="dark:bg-darkmode bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Name') }}</th>
                                        <th scope="col"
                                            class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Version') }}</th>
                                        <th scope="col"
                                            class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Description') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody class="dark:bg-darkmode bg-white divide-y divide-gray-200">
                                    @foreach ($servers as $extensio)
                                        <tr class="dark:bg-darkmode">
                                            <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $extensio->name }}</td>
                                            <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $extensio->version }}</td>
                                            <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $extensio->description }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                  <a href="{{ route('admin.extensions.edit', ['server', $extensio->name]) }}"
                                                class="text-indigo-600 hover:text-indigo-900 hover:bg-button p-2 rounded-lg">{{ __('Edit') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            @endif
                        </table>
                    </div>
                    <div class="mt-4 flex flex-wrap">
                        <br><br>
                        <!-- gateways -->
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="dark:bg-darkmode bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Name') }}</th>
                                    <th scope="col"
                                        class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Version') }}</th>
                                    <th scope="col"
                                        class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Description') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="dark:bg-darkmode bg-white divide-y divide-gray-200">
                                @foreach ($gateways as $gateway)
                                    <tr>
                                        <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $gateway->name }}</td>
                                        <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $gateway->version }}</td>
                                        <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $gateway->description }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.extensions.edit', ['gateway', $gateway->name]) }}"
                                                class="dark:bg-darkmodebutton text-indigo-600 hover:text-indigo-900 hover:bg-button p-2 rounded-lg">{{ __('Edit') }}</a>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>

</x-admin-layout>
