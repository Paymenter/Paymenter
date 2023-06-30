<!-- Display $stats -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<canvas id="cpu" width="400" height="400" style="max-height: 400px;"></canvas>
<canvas id="mem" width="400" height="400" style="max-height: 400px;"></canvas>
<canvas id="disk" width="400" height="400" style="max-height: 400px;"></canvas>
<script>
    var ctx = document.getElementById('cpu').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [
                @foreach ($stats as $stat)
                    @isset($stat['cpu'])
                        new Date('{{ $stat['time'] }}' * 1000).toLocaleTimeString(),
                    @endisset
                @endforeach
            ],
            datasets: [{
                label: 'CPU Usage',
                data: [
                    @foreach ($stats as $stat)
                        @isset($stat['cpu'])
                            '{{ $stat['cpu'] }}',
                        @endisset
                    @endforeach
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                ],
                borderWidth: 1
            }]
        },
    });

    var ctx = document.getElementById('mem').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [
                @foreach ($stats as $stat)
                    @isset($stat['mem'])
                        new Date('{{ $stat['time'] }}' * 1000).toLocaleTimeString(),
                    @endisset
                @endforeach
            ],
            datasets: [{
                label: 'Memory Usage',
                data: [
                    @foreach ($stats as $stat)
                        @isset($stat['mem'])
                            '{{ $stat['mem'] / 1024 / 1024 }}',
                        @endisset
                    @endforeach
                ],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.2)',
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                ],
                borderWidth: 1
            }]
        },
    });

    var ctx = document.getElementById('disk').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [
                @foreach ($stats as $stat)
                    @isset($stat['disk'])
                        new Date('{{ $stat['time'] }}' * 1000).toLocaleTimeString(),
                    @endisset
                @endforeach
            ],
            datasets: [{
                label: 'Disk Usage',
                data: [
                    @foreach ($stats as $stat)
                        @isset($stat['disk'])
                            '{{ $stat['disk'] }}',
                        @endisset
                    @endforeach
                ],
                backgroundColor: [
                    'rgba(255, 206, 86, 0.2)',
                ],
                borderColor: [
                    'rgba(255, 206, 86, 1)',
                ],
                borderWidth: 1
            }]
        },
    });
</script>
