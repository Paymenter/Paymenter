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
                        class="lg:col-span-2 p-7 bg-white border-2 dark:bg-darkmode2 dark:border-darkmodehover rounded-xl">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 pb-10">
                            <div class="p-4 rounded-md dark:bg-darkmode bg-normal flex">
                                <i class="ri-shopping-cart-2-line pl-2 pr-4 my-auto text-blue-600 text-4xl"></i>
                                <div class="flex flex-col">    
                                    <h1 class="text-lg text-gray-500 dark:text-darkmodetext">Orders today</h1>
                                    <p class="text-xl font-bold text-black dark:text-darkmodetext">
                                        {{ App\Models\Order::whereDate('created_at', '=', date('Y-m-d'))->count() }}
                                    </p>
                                </div>
                            </div>
                            <div class="p-4 rounded-md dark:bg-darkmode bg-normal flex">
                                <i class="ri-coupon-line pl-2 pr-4 my-auto text-blue-600 text-4xl"></i>
                                <div class="flex flex-col">
                                    <h1 class="text-lg text-gray-500 dark:text-darkmodetext">Tickets today</h1>
                                    <p class="text-xl font-bold text-black dark:text-darkmodetext">
                                        {{ App\Models\Ticket::whereDate('created_at', '=', date('Y-m-d'))->count() }}
                                    </p>
                                </div>
                            </div>
                            <div class="p-4 rounded-md dark:bg-darkmode bg-normal flex">
                                <i class="ri-coins-line pl-2 pr-4 my-auto text-blue-600 text-4xl"></i>
                                <div class="flex flex-col">
                                    <h1 class="text-lg text-gray-500 dark:text-darkmodetext">Revenue Total</h1>
                                    <p class="text-xl font-bold text-black dark:text-darkmodetext">
                                        {{ $revenueTotal }}
                                        {{ config('settings::currency_sign') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <canvas id="myChart" class="w-full max-h-[400px]"></canvas>
                    </div>
                    <div class="p-7 bg-white border-2 dark:bg-darkmode2 dark:border-darkmodehover rounded-xl">
                        <h2 class="text-xl font-bold mb-2 dark:text-darkmodetext">Support</h2>
                        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                            <div class="flex">
                                <i class="ri-coupon-line p-4 text-blue-600 bg-normal dark:bg-darkmode text-2xl items-center text-center h-16 w-16 rounded-lg mr-4"></i>
                                <div class="flex flex-col my-auto">
                                    <h3 class="text-gray-700 dark:text-darkmodetext">Open Tickets</h3>
                                    <span class="font-bold text-xl dark:text-darkmodetext">{{ App\Models\Ticket::where('status', '!=', 'closed')->count() }}</span>
                                </div>
                            </div>
                            
                            <div class="flex">
                                <i class="ri-coupon-line p-4 text-blue-600 bg-normal dark:bg-darkmode text-2xl items-center text-center h-16 w-16 rounded-lg mr-4"></i>
                                <div class="flex flex-col my-auto">
                                    <h3 class="text-gray-700 dark:text-darkmodetext">Closed Tickets</h3>
                                    <span class="font-bold text-xl dark:text-darkmodetext">{{ App\Models\Ticket::where('status', 'closed')->count() }}</span>
                                </div>
                            </div>
                            
                            <div class="flex">
                                <i class="ri-coupon-line p-4 text-blue-600 bg-normal dark:bg-darkmode text-2xl items-center text-center h-16 w-16 rounded-lg mr-4"></i>
                                <div class="flex flex-col my-auto">
                                    <h3 class="text-gray-700 dark:text-darkmodetext">Total Tickets</h3>
                                    <span class="font-bold text-xl dark:text-darkmodetext">{{ App\Models\Ticket::count() }}</span>
                                </div>
                            </div>
                        </div>

                        <h2 class="text-xl font-bold mt-4 mb-2 dark:text-darkmodetext">Recent Open Tickets</h2>
                        <div class="grid grid-cols-1 gap-4">
                            @foreach (App\Models\Ticket::orderByRaw('updated_at - created_at DESC')->get()->take(5) as $ticket)
                                <a href="/admin/tickets/{{ $ticket->id }}">
                                    <div class="px-4 py-2 rounded-md flex dark:hover:bg-darkmode/50 dark:bg-darkmode bg-normal hover:bg-blue-100">
                                        <div class="flex flex-col">
                                            <h1 class="text-xl text-gray-500 dark:text-darkmodetext">Ticket
                                                #{{ $ticket->id }} by
                                                <span class="dark:text-darkmodetext">
                                                    {{ $ticket->client()->get()->first()->name }}
                                                </span>
                                            </h1>
                                            <p class="text-2xl font-bold text-black dark:text-darkmodetext flex items-center">
                                                <label class="mr-2">{{ $ticket->title }}</label>
                                                @if ($ticket->priority == 'high')
                                                    <span
                                                        class="px-1.5 py-0.5 text-xs text-white bg-red-500 rounded-md mr-2">High</span>
                                                @elseif($ticket->priority == 'medium')
                                                    <span
                                                        class="px-1.5 py-0.5 text-xs text-white bg-yellow-500 rounded-md mr-2">Medium</span>
                                                @elseif($ticket->priority == 'low')
                                                    <span
                                                        class="px-1.5 py-0.5 text-xs text-white bg-green-500 rounded-md mr-2">Low</span>
                                                @endif

                                                @if ($ticket->status == 'closed')
                                                    <span
                                                        class="px-1.5 py-0.5 text-xs text-white bg-red-500 rounded-md">closed</span>
                                                @elseif($ticket->status == 'replied')
                                                    <span
                                                        class="px-1.5 py-0.5 text-xs text-white bg-yellow-500 rounded-md">replied</span>
                                                @endif
                                            </p>
                                        </div>
                                        <label class="block my-auto ml-auto dark:text-darkmodetext">
                                            {{ $ticket->updated_at->diffForHumans() }}
                                        </label>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="p-7 bg-white border-2 dark:bg-darkmode2 dark:border-darkmodehover rounded-xl">
                        <h2 class="text-xl font-bold dark:text-darkmodetext">New Users</h2>
                        <canvas id="chartBar"></canvas>
                        <!-- Required chart.js -->
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                        <!-- Chart bar -->
                    </div>
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
                        label: "Users",
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
        </div>

        <div>
            <script>
                var ctx = document.getElementById('myChart').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Revenue', 'Tickets', 'Orders', ],
                        datasets: [{
                                label: 'Yesterday',
                                data: [
                                    {{ App\Models\OrderProduct::whereDate('created_at', '=', date('Y-m-d', strtotime('-1 days')))->sum('price') }},
                                    {{ App\Models\Ticket::whereDate('created_at', '=', date('Y-m-d', strtotime('-1 days')))->count() }},
                                    {{ App\Models\Order::whereDate('created_at', '=', date('Y-m-d', strtotime('-1 days')))->count() }},
                                ],
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.2)',
                                    'rgba(54, 162, 235, 0.2)',
                                    'rgba(255, 206, 86, 0.2)',
                                ],
                                borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                ],
                                borderWidth: 1
                            },
                            {
                                label: 'Today',
                                data: [
                                    {{ App\Models\OrderProduct::whereDate('created_at', '=', date('Y-m-d'))->sum('price') }},
                                    {{ App\Models\Ticket::whereDate('created_at', '=', date('Y-m-d'))->count() }},
                                    {{ App\Models\Order::whereDate('created_at', '=', date('Y-m-d'))->count() }},
                                ],
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.2)',
                                    'rgba(54, 162, 235, 0.2)',
                                    'rgba(255, 206, 86, 0.2)',
                                ],
                                borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                ],
                                borderWidth: 1
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
