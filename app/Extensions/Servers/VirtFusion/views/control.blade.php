<div class="grid grid-cols-1 md:grid-cols-2">
    <div class="flex">
        <div class="flex-col">
            <div class="font-bold">Name:</div>
            <div class="font-bold">Hostname:</div>
            <div class="font-bold">Ram:</div>
            <div class="font-bold">CPU:</div>
        </div>
        <div class="flex-col ml-4">
            <div>@empty($details->name) N/A @else {{ $details->name }} @endempty</div>
            <div>{{ $details->hostname ?? 'N/A'}}</div>
            <div>{{ $details->settings['resources']['memory'] ?? 'N/A'}} MB</div>
            <div>{{ $details->settings['resources']['cpuCores'] ?? 'N/A' }} @if($details->settings['resources']['cpuCores'] > 1)cores @else core @endif</div>
        </div>
    </div>
    <div class="flex">
        <div class="flex-col">
            <div class="font-bold">IPv4:</div>
            <div class="font-bold">IPv6:</div>
            <div class="font-bold">Storage:</div>
            <div class="font-bold">Traffic:</div>
        </div>
        <div class="flex-col ml-4">
            <div>{{ $details->network['interfaces'][0]['ipv4'][0]['address'] ?? 'N/A' }}</div>
            <div>{{ $details->network['interfaces'][0]['ipv6'][0]['address'] ?? 'N/A' }}</div>  
            <div>{{ $details->settings['resources']['storage'] ?? 'N/A' }} GB</div>
            <div>{{ $details->settings['resources']['traffic'] ?? 'N/A'}} GB</div>
        </div>
    </div>

    
</div>

<p class="mt-8">Manage your server via our dedicated control panel. You will be automatically authenticated and the control panel will open in a new tab.</p>
<a class="button button-primary mt-2" href="{{ route('extensions.virtfusion.login', $orderProduct->id) }}" target="_blank">
    Login to control panel
</a>
