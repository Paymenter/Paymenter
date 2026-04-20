<div class="space-y-4">
    @foreach ($tickets as $ticket)
        <x-ticket-card :ticket="$ticket" />
    @endforeach
</div>
