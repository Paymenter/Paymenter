<div class="container mt-14 space-y-4">
    <div class="flex flex-row justify-between">
        <x-navigation.breadcrumb />
        <x-navigation.link :href="route('tickets.create')" class="flex items-center gap-2">
            <x-ri-add-line class="size-5" />
            <span>{{ __('ticket.create_ticket') }}</span>
        </x-navigation.link>
    </div>

    @forelse ($tickets as $ticket)
        <x-ticket-card :ticket="$ticket" />
    @empty
    <div class="bg-background-secondary border border-neutral p-4 rounded-lg">
        <p class="text-base text-sm">{{ __('ticket.no_tickets') }}</p>
    </div>
    @endforelse

    {{ $tickets->links() }}
</div>
