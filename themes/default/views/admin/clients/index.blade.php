<x-admin-layout>
    <x-slot name="title">
        {{ __('Clients') }}
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Clients') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- all clients -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                        <div class="mt-8 text-2xl">
                            {{ __('All clients') }}
                        </div>
                        <div class="mt-6 text-gray-500">
                            {{ __('Here you can see all your clients.') }}
                        </div>
                        <div class="flex justify-end mt-4 mr-4">
                            <a href="{{ route('admin.clients.create') }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Create client') }}
                            </a>
                        </div>
                    </div>
                    <!-- align right a button to create a new client -->
                    <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold w-full">
                                    <table class="divide-y divide-gray-300 w-full" id="clientdatatable">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-6 py-2 text-xs text-gray-500">
                                                    ID
                                                </th>
                                                <th class="px-6 py-2 text-xs text-gray-500">
                                                    Name
                                                </th>
                                                <th class="px-6 py-2 text-xs text-gray-500">
                                                    Email
                                                </th>
                                                <th class="px-6 py-2 text-xs text-gray-500">
                                                    Created_at
                                                </th>
                                                <th class="px-6 py-2 text-xs text-gray-500">
                                                    Edit
                                                </th>
                                                <th class="px-6 py-2 text-xs text-gray-500">
                                                    Delete
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-500">
                                            @foreach ($users as $user)
                                                <tr class="whitespace-nowrap">
                                                    <td class="px-6 py-4 text-sm text-center text-gray-500">
                                                        {{ $user->id }}
                                                    </td>
                                                    <td class="px-6 py-4 text-center">
                                                        <div class="text-sm text-gray-900">
                                                            {{ $user->name }}
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 text-center">
                                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-center text-gray-500">
                                                        {{ $user->created_at }}
                                                    </td>
                                                    <td class="px-6 py-4 text-center">
                                                        <a href="{{ route('admin.clients.edit', $user->id) }}"
                                                            class="px-4 py-1 text-sm text-blue-600 bg-blue-200 rounded-full">Edit</a>
                                                    </td>
                                                    <td class="px-6 py-4 text-center">
                                                        <form action="{{ route('admin.clients.delete', $user->id) }}"
                                                            method="POST" class="delete">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="px-4 py-1 text-sm text-red-600 bg-red-200 rounded-full">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function() {
            $('#clientdatatable').DataTable();
        });

        var form = document.getElementsByClassName('delete');
        var confirmIt = function(e) {
            if (!confirm('Are you sure you want to delete this client?\nThis will remove all data!')) e.preventDefault();
        };
        for (var i = 0, l = form.length; i < l; i++) {
            form[i].addEventListener('submit', confirmIt, false);
        }

    </script>
</x-admin-layout>
