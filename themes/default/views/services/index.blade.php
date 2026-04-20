<div class="container mt-14 space-y-4">
    <x-navigation.breadcrumb />

    @forelse ($services as $service)
        <x-service-card :service="$service" />
    @empty
    <div class="bg-background-secondary border border-neutral p-4 rounded-lg">
        <p class="text-base text-sm">{{ __('services.no_services') }}</p>
    </div>
    @endforelse

    {{ $services->links() }}
</div>
