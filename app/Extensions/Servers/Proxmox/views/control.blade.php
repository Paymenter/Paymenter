<div class="flex">
    <div class="flex-1">
        The server is currently {{ ucfirst($status) }}.
        <br />
    </div>
    <div class="flex gap-2">
        <button class="button button-primary"
            onclick="proxmox_control('{{ $status == 'running' ? 'shutdown' : 'start' }}')">
            {{ $status == 'running' ? 'Stop' : 'Start' }} Server
        </button>

        <button class="button button-primary" onclick="proxmox_control('reboot')">
            Reboot Server

        </button>

        <button class="button button-primary" onclick="proxmox_control('stop')"
            {{ $status === 'stopped' ? "disabled=true" : '' }}">
            Force Stop Server
        </button>
    </div>
</div>
<script>
    function proxmox_control(action) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route('extensions.proxmox.status', $orderProduct->id) }}');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                console.log(xhr.responseText);
                var data = JSON.parse(xhr.responseText);
                console.log(data);
                if (data.status == 'success') {
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            } else {
                alert('An error occurred while trying to perform this action.');
            }
        };
        xhr.onerror = function() {
            alert('An error occurred while trying to perform this action.');
        };
        xhr.send('_token={{ csrf_token() }}&status=' + action);
    }
</script>

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
                    new Date('{{ $stat['time'] }}' * 1000).toLocaleTimeString(),
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
                    new Date('{{ $stat['time'] }}' * 1000).toLocaleTimeString(),
                @endforeach
            ],
            datasets: [{
                label: 'Memory Usage',
                data: [
                    @foreach ($stats as $stat)
                        @isset($stat['mem'])
                            '{{ $stat['mem'] }}',
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
                    new Date('{{ $stat['time'] }}' * 1000).toLocaleTimeString(),
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
