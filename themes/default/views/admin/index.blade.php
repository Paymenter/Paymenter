<x-admin-layout>
    <x-slot name="title">
        {{ __('General') }}
    </x-slot>
    <div class="grow">
        <div class="py-10 dark:bg-darkmode">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="grid p-4 overflow-hidden bg-white shadow-sm dark:bg-darkmode2 sm:rounded-lg md:grid-cols-3">
                    <!-- show ticketclosed, tickets, orders -->
                    <div class="col-span-2 p-10 bg-white border-2 dark:bg-darkmode dark:border-darkmode rounded-xl border-grey-600">
                        <div class="grid grid-cols-3 p-2 pb-10">
                            <div class="p-2 mr-3 rounded-md dark:bg-darkmode2 bg-normal">
                                <h1 class="text-xl text-gray-500 dark:text-darkmodetext">Orders today</h1>
                                <p class="text-2xl font-bold text-black dark:text-darkmodetext">{{App\Models\Orders::whereDate('created_at', '=', date('Y-m-d'))->count()}}</p>
                            </div>
                            <div class="p-2 mr-3 rounded-md dark:bg-darkmode2 bg-normal">
                                <h1 class="text-xl text-gray-500 dark:text-darkmodetext">Tickets today</h1>
                                <p class="text-2xl font-bold text-black dark:text-darkmodetext">{{App\Models\Tickets::whereDate('created_at', '=', date('Y-m-d'))->count()}}</p>
                            </div>
                            <div class="p-2 rounded-md dark:bg-darkmode2 bg-normal">
                                <h1 class="text-xl text-gray-500 dark:text-darkmodetext">Revenue Total</h1>
                                <p class="text-2xl font-bold text-black dark:text-darkmodetext">{{App\Models\Orders::sum('total')}} {{App\Models\Settings::first()->currency_sign}}</p>
                            </div>
                        </div>
                        <canvas id="myChart" style="width:100%;max-height:400px;"></canvas>
                    </div>
                    <div class="p-10 ml-4 bg-white border-2 dark:bg-darkmode dark:border-darkmode rounded-xl border-grey-600">
                        <h1 class="text-xl text-gray-500 dark:text-darkmodetext">Recent tickets</h1>
                        <div class="grid grid-cols-1 gap-4">
                        @foreach(App\Models\Tickets::orderByRaw('updated_at - created_at DESC')->get()->take(5) as $ticket)
                        <a href="/admin/tickets/{{$ticket->id}}">   
                            <div class="p-2 rounded-md dark:hover:bg-darkbutton dark:bg-darkmode2 bg-normal">
                            <h1 class="text-xl text-gray-500 dark:text-darkmodetext">Ticket #{{$ticket->id}} by 
                                    <span class="dark:text-darkmodetext">
                                        {{ $ticket->client()->get()->first()->name }}
                                    </span> 
                                </h1>
                                <p class="text-2xl font-bold text-black dark:text-darkmodetext">{{ $ticket->title }} 
                                @if($ticket->priority == 'high')
                                    <span class="p-1 text-base text-white bg-red-500 rounded-full">High</span>
                                @elseif($ticket->priority == 'medium')
                                    <span class="p-1 text-base text-white bg-yellow-500 rounded-full" >Medium</span>
                                @elseif($ticket->priority == 'low')
                                    <span class="p-1 text-base text-white bg-green-500 rounded-full">Low</span>
                                @endif
                                @if($ticket->status == 'closed')
                                    <span class="p-1 text-base text-white bg-red-500 rounded-full">closed</span>
                                @elseif($ticket->status == 'replied')
                                    <span class="p-1 text-base text-white bg-yellow-500 rounded-full" >replied</span>
                                @endif
                                </p>                          
                            </div>
                        </a> 
                        @endforeach
                        </div>
                    </div>
                    <div class="pt-5">
                        <div class="overflow-hidden rounded-lg shadow-lg dark:bg-darkmode">
                            <div class="py-3 text-center dark:bg-darkmode dark:text-darkmodetext bg-gray-50">Users</div>
                            <canvas id="chartBar"></canvas>
                        </div>
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
                    @for($i = 1; $i <= 12; $i++)
                        "{{ date('F', mktime(0, 0, 0, $i, 1)) }}",
                    @endfor
                ];
                const dataBarChart = {
                    labels: labelsBarChart,
                    datasets: [{
                        label: "Users",
                        backgroundColor: "#f87979",
                        data: [
                            @for($i = 1; $i <= 12; $i++)
                                {{App\Models\User::whereMonth('created_at', '=', $i)->count()}},
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
        <!-- Script for Short Overview -->
        <div>
            <script>
                var ctx = document.getElementById('myChart').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Revenue', 'Tickets', 'Orders', 
                        ], 
                        datasets: [{
                            label: 'Yesterday',
                            data: [
                                {{App\Models\Orders::whereDate('created_at', '=', date('Y-m-d', strtotime('-1 days')))->sum('total')}}, 
                                {{App\Models\Tickets::whereDate('created_at', '=', date('Y-m-d', strtotime('-1 days')))->count()}}, 
                                {{App\Models\Orders::whereDate('created_at', '=', date('Y-m-d', strtotime('-1 days')))->count()}}, 
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
                                {{App\Models\Orders::whereDate('created_at', '=', date('Y-m-d'))->sum('total')}}, 
                                {{App\Models\Tickets::whereDate('created_at', '=', date('Y-m-d'))->count()}}, 
                                {{App\Models\Orders::whereDate('created_at', '=', date('Y-m-d'))->count()}}, 
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
                        }],
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
        </div>
    </div>


</x-admin-layout>
