<x-admin-layout>
    <x-slot name="title">
        {{ __('Extensions') }}
    </x-slot>

    <div class="m-10 flex items-baseline relative">
        <h2 class="text-2xl font-semibold leading-tight text-gray-800 dark:text-darkmodetext">
            {{ __('Extensions and Gateways') }}
        </h2>
        <!-- Download Extension -->
        <div class="ml-4 absolute right-0">
            <a class="form-submit float-right" href="{{ route('admin.extensions.browse') }}">
                {{ __('Browse Extensions') }}
            </a>
        </div>
    </div>
    @if (!$servers)
        <p class="dark:bg-secondary-100 dark:text-darkmodetext text-gray-600 px-3 rounded-md text-xl m-4">
            {{ __('No extensions found') }}
        </p>
    @else
        <table class="min-w-full divide-y divide-gray-200 w-full" id="table">
            <thead class="bg-secondary-100 ">
                <tr>
                    <th scope="col"
                        class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('Name') }}</th>
                    <th scope="col"
                        class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('Enabled?') }}</th>
                    <th scope="col"
                        class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('Version') }}</th>

                    <th scope="col"
                        class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('Edit') }}</th>
                </tr>
            </thead>
            <tbody class="bg-secondary-100 divide-y divide-gray-200">
                <tr class="bg-secondary-100">
                    <td colspan="3"
                        class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-gray-500 font-bold text-lg text-center">
                        Servers</td>
                </tr>
                @foreach ($servers as $extensio)
                    @if ($extensio == '.' || $extensio == '..')
                        @continue
                    @endif
                    @php
                        $extension = App\Models\Extension::where('name', $extensio)
                            ->get()
                            ->first();
                    @endphp
                    @php $metadata = App\Helpers\ExtensionHelper::getMetadata($extension); @endphp
                    <tr class="bg-secondary-100">
                        <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $metadata->display_name ?? $extensio }}</td>
                        <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if ($extension->enabled)
                                {{ __('Yes') }}
                            @else
                                {{ __('No') }}
                            @endif
                        </td>
                        <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500 flex flex-row items-center gap-4">
                            {{ $metadata->version ?? 'Unknown Version' }}

                            @if($extension->update_available)
                                <form action="{{ route('admin.extensions.updateExtension', $extension) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="button button-success animate-pulse">{{ __('Update') }}</button>
                                </form>
                            @endif
                        </td>
                        <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <a href="{{ route('admin.extensions.edit', ['server', $extensio]) }}"
                                class="text-indigo-600 hover:text-indigo-900 hover:bg-button p-2 rounded-lg">{{ __('Edit') }}</a>
                        </td>
                    </tr>
                @endforeach
                <tr class="bg-secondary-100">
                    <td colspan="3"
                        class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-gray-500 font-bold text-lg text-center">
                        Gateways</td>
                </tr>
                @foreach ($gateways as $gateway)
                    @if ($gateway == '.' || $gateway == '..')
                        @continue
                    @endif
                    @php
                        $extension = App\Models\Extension::where('name', $gateway)
                            ->get()
                            ->first();
                    @endphp
                    @php $metadata = App\Helpers\ExtensionHelper::getMetadata($extension); @endphp
                    <tr>
                        <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $metadata->display_name ?? $gateway }}</td>
                        <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if ($extension->enabled)
                                {{ __('Yes') }}
                            @else
                                {{ __('No') }}
                            @endif
                        </td>
                        <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $metadata->version ?? 'Unknown Version' }}
                        </td>
                        <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <a href="{{ route('admin.extensions.edit', ['gateway', $gateway]) }}"
                                class="dark:bg-darkmodebutton text-indigo-600 hover:text-indigo-900 hover:bg-button p-2 rounded-lg">{{ __('Edit') }}</a>
                        </td>
                    </tr>
                @endforeach
                <tr class="bg-secondary-100">
                    <td colspan="3"
                        class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-gray-500 font-bold text-lg text-center">
                        Events</td>
                </tr>
                @foreach ($events as $event)
                    @if ($event == '.' || $event == '..')
                        @continue
                    @endif
                    @php
                        $extension = App\Models\Extension::where('name', $event)
                            ->get()
                            ->first();
                    @endphp
                    @php $metadata = App\Helpers\ExtensionHelper::getMetadata($extension); @endphp
                    <tr>
                        <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $event }}</td>
                        <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if ($extension->enabled)
                                {{ __('Yes') }}
                            @else
                                {{ __('No') }}
                            @endif
                        </td>
                        <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $metadata->version ?? 'Unknown Version' }}
                        </td>
                        <td class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <a href="{{ route('admin.extensions.edit', ['event', $event]) }}"
                                class="dark:bg-darkmodebutton text-indigo-600 hover:text-indigo-900 hover:bg-button p-2 rounded-lg">{{ __('Edit') }}</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endif
</x-admin-layout>
