<div class="space-y-4">
    @foreach ($services as $service)
        <x-service-card :service="$service" />
    @endforeach
</div>
