<div class="flex">
    <div class="flex-1">
        The server is currently {{ ucfirst($status['status']) }}.
        <br />
    </div>
    <div class="flex gap-2">
        <button class="button button-primary"
            onclick="proxmox_control('{{ $status['status'] == 'running' ? 'shutdown' : 'start' }}')">
            {{ $status == 'running' ? 'Stop' : 'Start' }} Server
        </button>

        <button class="button button-primary" onclick="proxmox_control('reboot')">
            Reboot Server

        </button>

        <button class="button button-primary" onclick="proxmox_control('stop')"
            {{ $status['status'] === 'stopped' && 'disabled=true' }}">
            Force Stop Server
        </button>
    </div>
</div>
<!-- Show bars for currently used resources -->
<div class="grid grid-cols-1 md:grid-cols-2">
    <div class="flex gap-2">
        <div class="flex-1">
            CPU Usage: <progress class="progress" value="{{ $status['cpu'] }}" max="{{ $status['cpus'] }}"></progress>
        </div>
        <div class="flex-1">
            Memory Usage: <progress class="progress" value="{{ $status['mem'] }}"
                max="{{ $status['maxmem'] }}"></progress>
        </div>
        <div class="flex-1">
            Disk Usage: <progress class="progress" value="{{ $status['disk'] }}" max="100"></progress>
        </div>
    </div>
    <div class="flex flex-col">
        <div class="flex-1">
            {{-- <h2 class="text-lg">Name: {{ $status['hostname'] }}</h2> --}}
        </div>
    </div>
</div>
<script>
    function proxmox_control(action) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route('extensions.proxmox.status', $orderProduct->id) }}');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                var data = JSON.parse(xhr.responseText);
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
@include('proxmox::stats')