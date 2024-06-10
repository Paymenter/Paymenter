<div class="grid grid-cols-1 md:grid-cols-2">
    <div class="flex">
        <div class="flex-col">
            <div class="font-bold">Name:</div>
            <div class="font-bold">Hostname:</div>
            <div class="font-bold">Ram:</div>
            <div class="font-bold">CPU:</div>
            <div class="font-bold">Storage:</div>
            <div class="font-bold">Traffic:</div>
        </div>
        <div class="flex-col ml-4">
            <div>@empty($details->name)
                    N/A
                @else
                    {{ $details->name }}
                @endempty</div>
            <div>{{ $details->hostname ?? 'N/A'}}</div>
            <div>{{ $details->settings['resources']['memory'] ?? 'N/A'}} MB</div>
            <div>{{ $details->settings['resources']['cpuCores'] ?? 'N/A' }} @if($details->settings['resources']['cpuCores'] > 1)
                    cores
                @else
                    core
                @endif</div>
            <div>{{ $details->settings['resources']['storage'] ?? 'N/A' }} GB</div>
            <div>{{ $details->settings['resources']['traffic'] ?? 'N/A'}} GB</div>
        </div>
    </div>
    <div class="flex">

        <div class="flex-col ml-4">
        @php
            $ipv4_addresses = array_map(function ($ipv4) {
                return $ipv4['address'] ?? 'N/A';
            }, $details->network['interfaces'][0]['ipv4']);
            $ipv6_addresses = [];
            foreach ($details->network['interfaces'][0]['ipv6'] as $ipv6) {
                if (!empty($ipv6['addresses'])) {
                    $ipv6_addresses = array_merge($ipv6_addresses, $ipv6['addresses']);
                }
            }
            $ipv4_string = implode('<br/>', $ipv4_addresses);
            $ipv6_string = implode('<br/>', $ipv6_addresses);
        @endphp

        <div>
            <p>
            <div class="font-bold">IPv4:</div>
                {!! $ipv4_string !!}</p>
        </div>
        <div>
            <p>
            <div class="font-bold">IPv6:</div>
                {!! $ipv6_string !!}</p>
        </div>

        </div>
    </div>

</div>

<p class="mt-8">Manage your server via our dedicated control panel. You will be automatically authenticated and the
    control panel will open in a new tab.</p>
<a class="button button-primary mt-2" href="{{ route('extensions.virtfusion.login', $orderProduct->id) }}"
   target="_blank">
    Login to control panel
</a>
