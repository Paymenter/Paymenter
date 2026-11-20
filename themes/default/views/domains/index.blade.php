<div class="container mt-14 space-y-4">
    <x-navigation.breadcrumb />

    @forelse ($domains as $domain)
        <a href="{{ route('domains.show', $domain) }}" wire:navigate>
            <div class="bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg mb-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <div class="bg-secondary/10 p-2 rounded-lg">
                            <x-ri-global-line class="size-5 text-secondary" />
                        </div>
                        <span class="font-medium">{{ $domain->name }}</span>
                    </div>
                    <span class="text-sm">{{ $domain->status->value }}</span>
                </div>
                <div class="text-sm text-base flex gap-2">
                    @if ($domain->expires_at)
                        <span>{{ __('services.renews_in') }} {{ $domain->expires_at->longAbsoluteDiffForHumans() }}</span>
                    @endif
                    <span>{{ $domain->auto_renew ? 'Auto-renew on' : 'Auto-renew off' }}</span>
                </div>
            </div>
        </a>
    @empty
        <div class="bg-background-secondary border border-neutral p-4 rounded-lg">
            <p class="text-base text-sm">No domains found.</p>
        </div>
    @endforelse

    {{ $domains->links() }}
</div>
