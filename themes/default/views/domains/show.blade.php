@php
    $status = $domain->status;
    $statusLabel = \Illuminate\Support\Str::headline($status->value);
    $registeredAt = $domain->registered_at ?? $domain->service?->created_at;
    $expiresAt = $domain->expires_at ?? $domain->service?->expires_at;
    $statusClass = match ($status) {
        \App\Enums\DomainStatus::Active => 'text-success bg-success/20',
        \App\Enums\DomainStatus::Pending,
        \App\Enums\DomainStatus::TransferInPending,
        \App\Enums\DomainStatus::TransferOutPending,
        \App\Enums\DomainStatus::Grace => 'text-warning bg-warning/20',
        \App\Enums\DomainStatus::Expired,
        \App\Enums\DomainStatus::Redemption,
        \App\Enums\DomainStatus::Failed => 'text-error bg-error/20',
        default => 'text-inactive bg-inactive/20',
    };
@endphp

<div class="container mt-14 space-y-4">
    <x-navigation.breadcrumb />

    @if ($domain->sync_error)
        <div class="bg-yellow-600/20 border-l-4 border-yellow-500 text-yellow-300 p-4 rounded-lg">
            <p class="font-medium">We could not refresh this domain's status. Please contact support if this persists.</p>
        </div>
    @endif

    <div class="bg-background-secondary border border-neutral p-6 rounded-lg">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="bg-secondary/10 p-2 rounded-lg">
                    <x-ri-global-line class="size-5 text-secondary" />
                </div>
                <h1 class="text-2xl font-semibold">{{ $domain->name }}</h1>
            </div>
            <span class="flex items-center gap-1.5 rounded-md px-2.5 py-1 text-sm font-semibold {{ $statusClass }}">
                @if ($status === \App\Enums\DomainStatus::Active)
                    <x-ri-checkbox-circle-fill class="size-4" />
                @elseif (in_array($status, [\App\Enums\DomainStatus::Expired, \App\Enums\DomainStatus::Redemption, \App\Enums\DomainStatus::Cancelled], true))
                    <x-ri-forbid-fill class="size-4" />
                @else
                    <x-ri-error-warning-fill class="size-4" />
                @endif
                {{ $statusLabel }}
            </span>
        </div>

        <div class="grid md:grid-cols-2 gap-4 my-4">
            <div>
                <h4 class="text-lg font-semibold">Domain Details:</h4>
                <div class="mt-2">
                    <div class="flex items-center text-base">
                        <span class="mr-2">Registrar:</span>
                        <span class="text-base/50">{{ $domain->registrar ?? 'Unknown' }}</span>
                    </div>
                    <div class="flex items-center text-base">
                        <span class="mr-2">Registered:</span>
                        <span class="text-base/50">{{ $registeredAt?->format('M d, Y') ?? 'Unknown' }}</span>
                    </div>
                    <div class="flex items-center text-base">
                        <span class="mr-2">Expires:</span>
                        <span class="text-base/50">
                            {{ $expiresAt?->format('M d, Y') ?? 'Unknown' }}
                            @if ($expiresAt)
                                ({{ $expiresAt->longAbsoluteDiffForHumans() }})
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center text-base">
                        <span class="mr-2">Last synced:</span>
                        <span class="text-base/50">{{ $domain->last_synced_at?->diffForHumans() ?? 'Never' }}</span>
                    </div>
                    @if ($domain->service)
                        <div class="flex items-center text-base">
                            <span class="mr-2">Service:</span>
                            <a href="{{ route('services.show', $domain->service) }}" wire:navigate
                                class="text-base/50 hover:underline underline-offset-2">
                                {{ $domain->service->label }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <h4 class="text-lg font-semibold">Actions:</h4>
                <div class="mt-2 flex flex-row gap-2 flex-wrap">
                    <x-button.primary class="h-fit !w-fit" wire:click="renewNow">
                        Renew now
                    </x-button.primary>

                    <x-button.secondary class="h-fit !w-fit" wire:click="toggleAutoRenew">
                        {{ $domain->auto_renew ? 'Disable auto-renew' : 'Enable auto-renew' }}
                    </x-button.secondary>

                    @if ($this->supports('lock'))
                        <x-button.secondary class="h-fit !w-fit" wire:click="toggleLock">
                            {{ $domain->locked ? 'Unlock domain' : 'Lock domain' }}
                        </x-button.secondary>
                    @endif

                    @if ($this->supports('privacy'))
                        <x-button.secondary class="h-fit !w-fit" wire:click="togglePrivacy">
                            {{ $domain->privacy ? 'Disable privacy' : 'Enable privacy' }}
                        </x-button.secondary>
                    @endif
                </div>
                <p class="mt-2 text-sm text-base/50">
                    Auto-renew is currently {{ $domain->auto_renew ? 'on' : 'off' }}.
                </p>
            </div>
        </div>
    </div>

    @if ($this->supports('nameservers'))
        <div class="bg-background-secondary border border-neutral p-6 rounded-lg space-y-2">
            <h2 class="text-lg font-semibold">Nameservers</h2>
            <div class="grid sm:grid-cols-2 gap-2">
                @foreach ($nameservers as $index => $nameserver)
                    <x-form.input :name="'nameservers.' . $index" wire:model="nameservers.{{ $index }}"
                        :label="'Nameserver ' . ($index + 1)"
                        :placeholder="'ns' . ($index + 1) . '.example.com'" />
                @endforeach
            </div>
            <x-button.primary class="!w-fit" wire:click="updateNameservers">Save nameservers</x-button.primary>
        </div>
    @endif

    @if ($this->supports('epp'))
        <div class="bg-background-secondary border border-neutral p-6 rounded-lg space-y-2">
            <h2 class="text-lg font-semibold">EPP Code</h2>
            @if ($eppCode)
                <code class="block p-3 bg-background rounded font-mono">{{ $eppCode }}</code>
            @else
                <p class="text-sm text-base/60">Confirm your password to reveal the transfer code.</p>
                <x-form.input type="password" name="password" wire:model="password"
                    placeholder="Confirm your password" />
                <x-button.primary class="!w-fit" wire:click="revealEpp">Reveal EPP code</x-button.primary>
            @endif
        </div>
    @endif
</div>
