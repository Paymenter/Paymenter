<x-admin-layout>
    <x-slot name="title">
        {{ __('General') }}
    </x-slot>
    <div class="grow">
        <div class="py-10 dark:bg-darkmode">
            <div class="mx-auto px-8 md:px-20">
                <div class="mb-4">
                    <h1 class="text-2xl font-bold dark:text-darkmodetext">Dashboard</h1>
                    <span class="text-lg dark:text-darkmodetext">An overview of everything</span>
                </div>
                <div class="grid gap-6 overflow-hidden grid-cols-1 lg:grid-cols-3">
                    <!-- show ticketclosed, tickets, orders -->
                    <div
                        class="lg:col-span-2 p-7 bg-white border-2 dark:bg-secondary-100 dark:border-darkmodehover rounded-xl">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 pb-10">
                            <div class="p-4 rounded-md dark:bg-darkmode bg-normal flex">
                                <i class="ri-shopping-cart-2-line p-4 bg-blue-400 text-blue-900 text-2xl items-center text-center h-16 w-16 rounded-lg mr-4"></i>
                                <div class="flex flex-col">
                                    <h1 class="text-lg text-gray-500 dark:text-darkmodetext">Orders today</h1>
                                    <p class="text-xl font-bold text-black dark:text-darkmodetext">
                                        {{ App\Models\Order::whereDate('created_at', '=', date('Y-m-d'))->count() }}
                                    </p>
                                </div>
                            </div>
                            <div class="p-4 rounded-md dark:bg-darkmode bg-normal flex">
                                <i class="ri-coins-line p-4 bg-green-400 text-green-900 text-2xl items-center text-center h-16 w-16 rounded-lg mr-4"></i>
                                <div class="flex flex-col">
                                    <h1 class="text-lg text-gray-500 dark:text-darkmodetext">Revenue Total</h1>
                                    <p class="text-xl font-bold text-black dark:text-darkmodetext">
                                        {{ $revenueTotal }}
                                        {{ config('settings::currency_sign') }}
                                    </p>
                                </div>
                            </div>
                            <div class="p-4 rounded-md dark:bg-darkmode bg-normal flex">
                                <i class="ri-coupon-line p-4 bg-red-400 text-red-900 text-2xl items-center text-center h-16 w-16 rounded-lg mr-4"></i>
                                <div class="flex flex-col">
                                    <h1 class="text-lg text-gray-500 dark:text-darkmodetext">Tickets today</h1>
                                    <p class="text-xl font-bold text-black dark:text-darkmodetext">
                                        {{ App\Models\Ticket::whereDate('created_at', '=', date('Y-m-d'))->count() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <canvas id="myChart" class="w-full max-h-[400px]"></canvas>
                    </div>
                    <div class="p-5 bg-white border-2 dark:bg-secondary-100 dark:border-darkmodehover rounded-xl">
                        <h2 class="text-xl font-bold mb-2 dark:text-darkmodetext">{{__('Tickets')}}</h2>
                        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                            <div class="flex">
                                <i
                                    class="ri-coupon-line p-4 bg-blue-400 text-blue-900 text-2xl items-center text-center h-16 w-16 rounded-lg mr-4"></i>
                                <div class="flex flex-col my-auto">
                                    <span class="font-bold text-xl dark:text-darkmodetext">
                                        {{ App\Models\Ticket::count() }}
                                    </span>
                                    <h3 class="text-gray-700 dark:text-darkmodetext">{{__('All')}}</h3>
                                </div>
                            </div>

                            <div class="flex">
                                <i
                                    class="ri-coupon-line p-4 bg-green-400 text-green-900 text-2xl items-center text-center h-16 w-16 rounded-lg mr-4"></i>
                                <div class="flex flex-col my-auto">
                                    <span class="font-bold text-xl dark:text-darkmodetext">
                                        {{ App\Models\Ticket::where('status', '!=', 'closed')->count() }}
                                    </span>
                                    <h3 class="text-gray-700 dark:text-darkmodetext">{{__("Open")}}</h3>
                                </div>
                            </div>

                            <div class="flex">
                                <i
                                    class="ri-coupon-line p-4 bg-red-400 text-red-900 text-2xl items-center text-center h-16 w-16 rounded-lg mr-4"></i>
                                <div class="flex flex-col my-auto">
                                    <span class="font-bold text-xl dark:text-darkmodetext">
                                        {{ App\Models\Ticket::where('status', 'closed')->count() }}
                                    </span>
                                    <h3 class="text-gray-700 dark:text-darkmodetext">{{__('Closed')}}</h3>
                                </div>
                            </div>
                        </div>

                        <h2 class="text-xl font-bold mt-4 mb-2 dark:text-darkmodetext">{{__('Last Tickets')}}</h2>
                        <div class="grid grid-cols-1 gap-4">
                            @foreach (App\Models\Ticket::orderByRaw('updated_at - created_at DESC')->get()->take(4) as $ticket)
                                <a href="/admin/tickets/{{ $ticket->id }}">
                                    <div class="px-4 py-2 rounded-md flex flex-col dark:hover:bg-darkmode/50 dark:bg-darkmode bg-normal hover:bg-blue-100">
                                        <div class="flex flex-row justify-between">
                                            <h1 class="text-xl text-gray-500 dark:text-darkmodetext flex flex-col">
                                                <b>{{__('Ticket')}} #{{ $ticket->id }}</b>
                                                <span class="dark:text-darkmodetext">
                                                    <b>{{__('Client')}}:</b> {{ $ticket->user->name }} (#{{ $ticket->user->id }})
                                                </span>
                                                @isset($ticket->assigned_to)
                                                    <div>
                                                        <b>{{__('Admin')}}:</b> <span class="dark:text-darkmodetext">
                                                        @php
                                                            if ($ticket->assigned_to !== null) {
                                                                $admin = App\Models\User::where('id', '=', $ticket->assigned_to)->get();
                                                            } else {
                                                                $admin = null;
                                                            }
                                                        @endphp
                                                        @if (!$admin)
                                                                {{__('Unassigned')}}
                                                        @else
                                                            {{ $admin[0]->name }}
                                                        @endif
                                                    </div>
                                                @endisset
                                            </h1>
                                            <div class="flex flex-col my-auto">
                                                <p class="font-bold flex mb-0">
                                                    @if($ticket->priority == 'high')
                                                        <span class="px-1.5 py-0.5 text-xs text-white bg-red-500 rounded-md mr-2 animate-pulse my-auto">
                                                            {{__('High') }}
                                                        </span>
                                                    @elseif($ticket->priority == 'medium')
                                                        <span class="px-1.5 py-0.5 text-xs text-white bg-yellow-500 rounded-md mr-2 my-auto">
                                                            {{__('Medium') }}
                                                        </span>
                                                    @elseif($ticket->priority == 'low')
                                                        <span class="px-1.5 py-0.5 text-xs text-white bg-green-500 rounded-md mr-2 my-auto">
                                                            {{__('Low') }}
                                                        </span>
                                                    @endif

                                                    @if ($ticket->status == 'closed')
                                                        <span class="px-1.5 py-0.5 text-xs text-white bg-red-500 rounded-md my-auto">
                                                            {{ __('Closed') }}
                                                        </span>
                                                    @elseif($ticket->status == 'replied')
                                                        <span class="px-1.5 py-0.5 text-xs text-white bg-yellow-500 rounded-md my-auto">
                                                            {{ __('Replied') }}
                                                            </span>
                                                    @elseif($ticket->status == 'open')
                                                        <span class="px-1.5 py-0.5 text-xs text-white bg-green-500 rounded-md my-auto">
                                                            {{ __('Open') }}
                                                        </span>
                                                    @endif
                                                </p>
                                                <div class="my-0 flex flex-col text-center dark:text-darkmodetext">
                                                    {{ $ticket->updated_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="p-7 bg-white border-2 dark:bg-secondary-100 dark:border-darkmodehover rounded-xl">
                        <h2 class="text-xl font-bold dark:text-darkmodetext">{{__('New Users')}}</h2>
                        <canvas id="chartBar"></canvas>
                        <!-- Chart bar -->
                    </div>

                    <div class="p-7 bg-white border-2 dark:bg-secondary-100 dark:border-darkmodehover rounded-xl">
                        <h2 class="text-xl font-bold dark:text-darkmodetext">{{__('New Orders')}}</h2>
                        <canvas id="chartBarOrders"></canvas>
                        <!-- Chart bar -->
                    </div>

                    <!--
                    <div class="p-7 bg-white border-2 dark:bg-secondary-100 dark:border-darkmodehover rounded-xl">
                        <h2 class="text-xl font-bold dark:text-darkmodetext">Przychody</h2>
                        <canvas id="chartBarMoney"></canvas>
                    </div>
                    -->

                    <!-- Required chart.js -->
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                </div>
            </div>
        </div>



        <!-- Script for User statistic -->
        <div>
            <script>
                const labelsBarChart = [
                    @for ($i = 1; $i <= 12; $i++)
                        "{{ date('F', mktime(0, 0, 0, $i, 1)) }}",
                    @endfor
                ];
                const dataBarChart = {
                    labels: labelsBarChart,
                    datasets: [{
                        label: "{{__('Users')}}",
                        backgroundColor: "#f87979",
                        data: [
                            @for ($i = 1; $i <= 12; $i++)
                                {{ App\Models\User::whereMonth('created_at', '=', $i)->count() }},
                            @endfor
                        ],
                    }],
                };

                const configBarChart = {
                    type: "bar",
                    data: dataBarChart,
                    options: {},
                };

                var chartBar = new Chart(
                    document.getElementById("chartBar"),
                    configBarChart
                );
            </script>

            <script>
                const labelsBarOrdersChart = [
                    @for ($i = 1; $i <= 12; $i++)
                        "{{ date('F', mktime(0, 0, 0, $i, 1)) }}",
                    @endfor
                ];
                const dataBarOrdersChart = {
                    labels: labelsBarOrdersChart,
                    datasets: [{
                        label: "{{__('Orders')}}",
                        backgroundColor: "#79b4f8",
                        data: [
                            @for ($i = 1; $i <= 12; $i++)
                                {{ App\Models\Order::whereMonth('created_at', '=', $i)->count() }},
                            @endfor
                        ],
                    }],
                };

                const configBarOrdersChart = {
                    type: "bar",
                    data: dataBarOrdersChart,
                    options: {},
                };

                var ordersChartBar = new Chart(
                    document.getElementById("chartBarOrders"),
                    configBarOrdersChart
                );
            </script>

            <!--
            <script>
                const labelsBarMoneyChart = [
                    @for ($i = 1; $i <= 12; $i++)
                        "{{ date('F', mktime(0, 0, 0, $i, 1)) }}",
                    @endfor
                ];

                const currencySign = "{{ config('settings::currency_sign') }}";

                const dataBarMoneyChart = {
                    labels: labelsBarMoneyChart,
                    datasets: [{
                        label: "Przychody",
                        backgroundColor: "#f4f879",
                        data: [
                            @for ($i = 1; $i <= 12; $i++)
                                {{ App\Models\InvoiceItem::whereMonth('created_at', '=', $i)->sum('total') }},
                            @endfor
                        ],
                    }],
                };

                const configBarMoneyChart = {
                    type: "bar",
                    data: dataBarMoneyChart,
                    options: {
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function (tooltipItem, data) {
                                        console.log(data);
                                        console.log(tooltipItem);
                                        return tooltipItem.formattedValue + ' {{ config('settings::currency_sign') }}';
                                    }
                                }
                            }
                        }
                    }
                };

                var moneyChartBar = new Chart(
                    document.getElementById("chartBarMoney"),
                    configBarMoneyChart
                );
            </script>
            -->
        </div>

        <div>
            <script>
                var ctx = document.getElementById('myChart').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [
                            @for ($i = 29; $i >= 0; $i--)
                                "{{ $i }}",
                            @endfor
                        ],
                        datasets: [{
                                label: 'Orders',
                                data: [
                                    @for ($i = 29; $i >= 0; $i--)
                                        {{ isset($orderCounts[$i]) ? $orderCounts[$i] : 0 }},
                                    @endfor
                                ],
                                backgroundColor: 'rgb(82,112,253)',
                                borderColor: 'rgb(82,112,253)',
                                tension: 0.2,
                            },
                            {
                                label: 'Users',
                                data: [
                                    @for ($i = 29; $i >= 0; $i--)
                                        {{ isset($userCounts[$i]) ? $userCounts[$i] : 0 }},
                                    @endfor
                                ],
                                backgroundColor: 'rgba(255, 99, 132, 1)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                tension: 0.2,
                            },
                            {
                                label: 'Revenue',
                                data: [
                                    @for ($i = 29; $i >= 0; $i--)
                                        {{ isset($invoiceCounts[$i]) ? $invoiceCounts[$i] : 0 }},
                                    @endfor
                                ],
                                backgroundColor: 'rgba(255, 206, 86, 1)',
                                borderColor: 'rgba(255, 206, 86, 1)',
                                tension: 0.2,
                            }
                        ],
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                            },
                            x: {
                                grid: {
                                    display: false,
                                }
                            }
                        }
                    }
                });
            </script>
        </div>
    </div>


</x-admin-layout>
