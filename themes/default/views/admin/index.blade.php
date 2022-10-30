<x-admin-layout>
    <x-slot name="title">
        {{ __('General') }}
    </x-slot>
    <div class="dark:bg-darkmode py-10">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="dark:bg-darkmode2 overflow-hidden bg-white shadow-sm sm:rounded-lg p-4 grid md:grid-cols-3">
                <!-- show ticketclosed, tickets, orders -->
                <div class="dark:bg-darkmode dark:border-darkmode p-10 bg-white border-2 rounded-xl border-grey-600 col-span-2">
                    <div class="grid grid-cols-3 p-2 pb-10">
                        <div class="dark:bg-darkmode2 bg-normal rounded-md mr-3 p-2">
                            <h1 class="dark:text-darkmodetext text-xl text-gray-500">Revenue today</h1>
                            <p class="dark:text-darkmodetext text-black font-bold text-2xl">20€</p>
                        </div>
                        <div class="dark:bg-darkmode2 bg-normal rounded-md mr-3 p-2">
                            <h1 class="dark:text-darkmodetext text-xl text-gray-500">Tickets today</h1>
                            <p class="dark:text-darkmodetext text-black font-bold text-2xl">5</p>
                        </div>
                        <div class="dark:bg-darkmode2 bg-normal rounded-md p-2">
                            <h1 class="dark:text-darkmodetext text-xl text-gray-500">Revenue Total</h1>
                            <p class="dark:text-darkmodetext text-black font-bold text-2xl">500$</p>
                        </div>
                    </div>
                    <canvas id="myChart" style="width:100%;max-height:400px;"></canvas>
                </div>
                <div class="dark:bg-darkmode dark:border-darkmode p-10 bg-white border-2 rounded-xl border-grey-600 ml-4">
                    <h1 class="dark:text-darkmodetext text-xl text-gray-500">Recent tickets</h1>
                    <div class="grid grid-cols-1 gap-4">
                    @foreach(App\Models\Tickets::orderByRaw('updated_at - created_at DESC')->get()->take(5) as $ticket)
                    <a href="/admin/tickets/{{$ticket->id}}">   
                        <div class="dark:hover:bg-darkbutton dark:bg-darkmode2 bg-normal rounded-md p-2">
                        <h1 class="dark:text-darkmodetext text-xl text-gray-500">Ticket #{{$ticket->id}} by 
                                <span class="dark:text-darkmodetext">
                                     {{ $ticket->client()->get()->first()->name }}
                                </span> 
                            </h1>
                            <p class="dark:text-darkmodetext text-black font-bold text-2xl">{{ $ticket->title }} 
                            @if($ticket->priority == 'high')
                                <span class="bg-red-500 text-white rounded-full p-1 text-base">High</span>
                            @elseif($ticket->priority == 'medium')
                                <span class="bg-yellow-500 text-white rounded-full p-1 text-base" >Medium</span>
                            @elseif($ticket->priority == 'low')
                                <span class="bg-green-500 text-white rounded-full p-1 text-base">Low</span>
                            @endif
                            @if($ticket->status == 'closed')
                                <span class="bg-red-500 text-white rounded-full p-1 text-base">closed</span>
                            @elseif($ticket->status == 'replied')
                                <span class="bg-yellow-500 text-white rounded-full p-1 text-base" >replied</span>
                            @endif
                            </p>                          
                        </div>
                    </a> 
                    @endforeach
                    </div>
                </div>
                <div class="pt-5">
                    <div class="dark:bg-darkmode shadow-lg rounded-lg overflow-hidden">
                        <div class="dark:bg-darkmode dark:text-darkmodetext py-3 text-center bg-gray-50">Users</div>
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
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December",
            ];
            const dataBarChart = {
                labels: labelsBarChart,
                datasets: [{
                    label: "Users",
                    backgroundColor: "#f87979",
                    data: [
                        {{App\Models\User::whereMonth('created_at', '=', '1')->count()}},
                        {{App\Models\User::whereMonth('created_at', '=', '2')->count()}},
                        {{App\Models\User::whereMonth('created_at', '=', '3')->count()}},
                        {{App\Models\User::whereMonth('created_at', '=', '4')->count()}},
                        {{App\Models\User::whereMonth('created_at', '=', '5')->count()}},
                        {{App\Models\User::whereMonth('created_at', '=', '6')->count()}},
                        {{App\Models\User::whereMonth('created_at', '=', '7')->count()}},
                        {{App\Models\User::whereMonth('created_at', '=', '8')->count()}},
                        {{App\Models\User::whereMonth('created_at', '=', '9')->count()}},
                        {{App\Models\User::whereMonth('created_at', '=', '10')->count()}},
                        {{App\Models\User::whereMonth('created_at', '=', '11')->count()}},
                        {{App\Models\User::whereMonth('created_at', '=', '12')->count()}},
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


</x-admin-layout>
