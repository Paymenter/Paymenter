<x-admin-layout>
    <x-slot name="title">
        {{ __('Tickets') }}
    </x-slot>
    <h1 class="text-2xl font-bold dark:text-darkmodetext text-center">{{ __('Tickets') }}</h1>
    <div class="flex items-center justify-end mt-4">
        <a href="{{ route('admin.tickets.create') }}"
            class="mr-4 bg-logo hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            {{ __('Create') }}
        </a>
    </div>
    <div class="dark:bg-darkmode2 p-6 bg-white ">
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="overflow-hidden sm:rounded-lg">
                        @if ($tickets->count())
                            <h3 class="dark:text-darkmodetext text-center my-4 text-xl"> {{ __('Open Tickets') }} </h3>
                            <table class="min-w-full divide-y divide-gray-200" id="tickets">
                                <thead class="dark:bg-darkmode">
                                    <tr>
                                        <th scope="col"
                                            class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Subject') }}
                                        </th>
                                        <th scope="col"
                                            class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Priority') }}
                                        </th>
                                        <th scope="col"
                                            class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Client') }}
                                        </th>
                                        <th scope="col"
                                            class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Status') }}
                                        </th>
                                        <th scope="col"
                                            class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Created at') }}
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="dark:text-darkmodetext sr-only">Reply</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="dark:bg-darkmode bg-white divide-y divide-gray-200">
                                    @foreach ($tickets as $service)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="dark:text-darkmodetext text-sm text-gray-900">
                                                    {{ $service->title }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($service->priority == 'low')
                                                    <span
                                                        class="dark:text-darkmodetext px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-600 text-green-800">
                                                        {{ __('Low') }}
                                                    </span>
                                                @elseif($service->priority == 'medium')
                                                    <span
                                                        class="dark:text-darkmodetext px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-600 text-yellow-800">
                                                        {{ __('medium') }}
                                                    </span>
                                                @elseif($service->priority == 'high')
                                                    <span
                                                        class="dark:text-darkmodetext px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-600 text-red-800">
                                                        {{ __('High') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td
                                                class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $service->client()->get()[0]->name }}
                                            </td>
                                            <td
                                                class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $service->status }}
                                            </td>
                                            <td
                                                class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $service->created_at }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('admin.tickets.show', $service->id) }}"
                                                    class="text-indigo-600 hover:text-indigo-900">{{ __('View') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <h3 class="dark:text-darkmodetext text-center my-4 text-xl"> No Open Tickets </h3>
                        @endif
                        @if ($closed->count())
                            <h3 class="dark:text-darkmodetext text-center my-4 text-xl"> Closed Tickets </h3>
                            <table class="min-w-full divide-y divide-gray-200" id="tickets">
                                <thead class="dark:bg-darkmode">
                                    <tr>
                                        <th scope="col"
                                            class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Subject') }}
                                        </th>
                                        <th scope="col"
                                            class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Priority') }}
                                        </th>
                                        <th scope="col"
                                            class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Client') }}
                                        </th>
                                        <th scope="col"
                                            class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Status') }}
                                        </th>
                                        <th scope="col"
                                            class="dark:text-darkmodetext px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Created at') }}
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="dark:text-darkmodetext sr-only">Reply</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="dark:bg-darkmode bg-white divide-y divide-gray-200">
                                    @foreach ($closed as $service)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="dark:text-darkmodetext text-sm text-gray-900">
                                                    {{ $service->title }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($service->priority == 'low')
                                                    <span
                                                        class="dark:text-darkmodetext px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-600 text-green-800">
                                                        {{ __('Low') }}
                                                    </span>
                                                @elseif($service->priority == 'medium')
                                                    <span
                                                        class="dark:text-darkmodetext px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-600 text-yellow-800">
                                                        {{ __('medium') }}
                                                    </span>
                                                @elseif($service->priority == 'high')
                                                    <span
                                                        class="dark:text-darkmodetext px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-600 text-red-800">
                                                        {{ __('High') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td
                                                class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $service->client()->get()[0]->name }}
                                            </td>
                                            <td
                                                class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $service->status }}
                                            </td>
                                            <td
                                                class="dark:text-darkmodetext px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $service->created_at }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('admin.tickets.show', $service->id) }}"
                                                    class="text-indigo-600 hover:text-indigo-900">{{ __('View') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <h3 class="dark:text-darkmodetext text-center my-4 text-xl"> No Closed Tickets </h3>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('tickets').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        });
    </script>
</x-admin-layout>
