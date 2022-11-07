<x-app-layout>
    <x-slot name="title">
        {{ __('Home') }}
    </x-slot>
    <div class="py-12">
        <x-success class="mt-4" />
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8" style="padding-bottom: 20px;">
            <div class="overflow-hidden bg-white shadow-xl dark:bg-darkmode2 sm:rounded-lg">
                <div class="p-6 border-gray-200 dark:bg-darkmode2">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12" style="display: flex;">
                            <img class="w-8 h-8 rounded-md" style="align-self: center; width: 3rem; height: 3rem;"
                                src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mp" />
                        </div>
                        <div class="ml-4 text-lg font-semibold leading-7">
                            {{ __('Welcome back') }}, {{ Auth::user()->name }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3">
            <div class="items-center col-span-1 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl dark:text-white dark:bg-darkmode2 sm:rounded-lg"
                    style="height: 5%;">
                    <div class="p-6 border-b border-gray-200 dark:text-white dark:bg-darkmode2"
                        style="padding: 1%;--tw-border-opacity: 0;">
                        <div class="flex items-center">
                            <div class="ml-4 text-lg font-semibold leading-7">
                                <a style="font-size: 0.8em;">Showing 1 to 2 of 2 entries</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="items-center mx-auto max-w-7xl sm:px-6 lg:px-8 column-2">
                <table id="tableServicesList" class="items-center table table-list">
                    <thead>
                        <tr>
                            <th class="dark:text-white sorting_asc dark:bg-darkmode2">Product/Service</th>
                            <th class="dark:text-white sorting_asc dark:bg-darkmode2">Pricing</th>
                            <th class="dark:text-white sorting_asc dark:bg-darkmode2">Next Due Date</th>
                            <th class="dark:text-white sorting_asc dark:bg-darkmode2">Status</th>
                        </tr>
                    </thead>
                    <tbody>

                        <!-- If the user is not an admin, show their services -->
                        @if (count($services) > 0)
                            <!-- If the array is empty, then we don't want to show the table -->
                            @foreach ($services as $service)
                                @foreach ($service->products as $product)
                                    @php
                                        $product = App\Models\Products::where('id', $product['id'])
                                            ->get()
                                            ->first();
                                    @endphp
                                    @php $product->price = $product->price . "0" @endphp
                                    <!-- Add a zero to the end of the price -->
                                    @php $service->expiry_date = date("l jS F Y", strtotime($service->expiry_date)) @endphp
                                    <!-- Format the expiry date to be more readable -->

                                    <tr onclick="window.location.href = '/products';">
                                        <td class="dark:text-white dark:bg-darkmode2">
                                            <strong>{{ ucfirst($product->name) }}</strong>
                                        </td>
                                        <td class="text-center dark:text-white dark:bg-darkmode2" data-order="0.00">
                                            £{{ number_format((float) $product->price, 2, '.', '') }} GBP<br />Free
                                            Account</td>
                                        <td class="text-center dark:text-white dark:bg-darkmode2">
                                            {{ $service->expiry_date }}</td>
                                        <!-- <td class="text-center dark:text-white dark:bg-darkmode2"><span class="label status status-active dark:bg-darkmode2">{{ ucfirst($service->status) }}</span></td> -->
                                        <td class="text-center dark:text-white dark:bg-darkmode2">
                                            @if ($service->status === 'paid')
                                                <span class="label status status-active dark:bg-darkmode2">Active</span>
                                            @elseif($service->status === 'pending')
                                                <span
                                                    class="label status status-active dark:bg-darkmode2">Pending</span>
                                            @elseif($service->status === 'cancelled')
                                                <span
                                                    class="label status status-active dark:bg-darkmode2">Expired</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @elseif (count($services) < 0)
                            <!-- If the array is empty, then don't show any data -->
                            <tr>
                                <td colspan="4" class="dark:text-white dark:bg-darkmode2"
                                    style="text-align: center;">No
                                    services found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
