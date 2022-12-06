<x-admin-layout>
    <x-slot name="title">
        {{ __('Categories') }}
    </x-slot>
    <!-- list all categories -->
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden dark:bg-darkmode2 bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 dark:bg-darkmode2 bg-white border-b border-gray-200 dark:border-gray-800">
                    <h1 class="text-2xl font-bold dark:text-darkmodetext">{{ __('Categories') }}</h1>
                    <div class="flex justify-end pr-3 pt-3">
                        <a href="{{ route('admin.categories.store') }}">
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Create') }}
                            </button>
                        </a>
                    </div>
                    <div class="flex flex-wrap">
                        <div class="w-full">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead class="bg-gray-50 dark:bg-darkmode2 ">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 dark:text-darkmodetext text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Name') }}</th>
                                        <th scope="col"
                                            class="px-6 py-3 dark:text-darkmodetext text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Description') }}</th>
                                        <th scope="col"
                                            class="px-6 py-3 dark:text-darkmodetext text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Slug') }}</th>
                                        <th scope="col"
                                            class="px-6 py-3 dark:text-darkmodetext text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-darkmode2 divide-y divide-gray-200">
                                    @foreach ($categories as $category)
                                        <tr>
                                            <td class="px-6 py-4 dark:text-darkmodetext whitespace-nowrap text-sm text-gray-500">
                                                {{ $category->name }}</td>
                                            <td class="px-6 py-4 dark:text-darkmodetext whitespace-nowrap text-sm text-gray-500">
                                                {{ $category->description }}</td>
                                            <td class="px-6 py-4 dark:text-darkmodetext whitespace-nowrap text-sm text-gray-500">
                                                /{{ $category->slug }}</td>
                                            <td class="px-6 py-4 dark:text-darkmodetext whitespace-nowrap text-sm text-gray-500">
                                                <div class="flex flew-wrap">
                                                    <a href="{{ route('admin.categories.edit', $category->id) }}"
                                                        class="mr-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                        {{ __('Edit') }}
                                                    </a>
                                                    <form action="{{ route('admin.categories.delete', $category->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                                            {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                </div>
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
</x-admin-layout>
