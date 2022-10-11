<x-admin-layout>
    <x-slot name="title">
        {{ __('General') }}
    </x-slot>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- show open orders count, ticket count, montlhy earnings -->
                    <div class="flex flex-wrap">
                        <div class="w-full p-6 sm:w-1/2 xl:w-1/3 bg-gray-200">
                            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                                <div class="p-3 mr-4 text-orange-500 bg-orange-100 rounded-full">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4zm0 2a4 4 0 100-8 4 4 0 000 8z">
                                        </path>
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm0 2a10 10 0 100-20 10 10 0 000 20z"
                                            clip-rule="evenodd">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="mb-2 text-sm font-medium text-gray-600">
                                        Open Orders
                                    </p>
                                    <p class="text-lg font-semibold text-gray-700">
                                        {{ App\Models\Services::where('status', 'open')->count() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="w-full p-6 sm:w-1/2 xl:w-1/3 bg-gray-200">
                            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                                <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M3 7a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3 4a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm-3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                            clip-rule="evenodd">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="mb-2 text-sm font-medium text-gray-600">
                                        Tickets
                                    </p>
                                    <p class="text-lg font-semibold text-gray-700">
                                        {{ App\Models\Tickets::all()->count() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="w-full p-6 sm:w-1/2 xl:w-1/3 bg-gray-200">
                            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                                <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M3 7a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3 4a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm-3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                            clip-rule="evenodd">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="mb-2 text-sm font-medium text-gray-600">
                                        Monthly Earnings
                                    </p>
                                    <p class="text-lg font-semibold text-gray-700">
                                        1
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</x-admin-layout>
