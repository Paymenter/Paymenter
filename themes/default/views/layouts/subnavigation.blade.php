
@if (config('settings::sidebar') == 0)
    <div class="bg-secondary-50 dark:bg-secondary-100 dark:border-0 dark:border-t-2 border-b-2 border-secondary-200">
        <div class="max-w-[1650px] mx-auto block md:flex items-center gap-x-10 px-5">
            <a href="{{ route('clients.home') }}" class="md:px-2 py-3 flex items-center gap-x-2 hover:text-secondary-800 duration-300">
                <i class="ri-layout-2-line @if (request()->routeIs('clients.home')) text-primary-400 @endif"></i> {{ __('Dashboard') }}
            </a>
            <a href="#" class="md:px-2 py-3 flex items-center gap-x-2 hover:text-secondary-800 duration-300">
                <i class="ri-instance-line"></i> {{ __('Services') }}
            </a>
            <a href="#" class="md:px-2 py-3 flex items-center gap-x-2 hover:text-secondary-800 duration-300">
                <i class="ri-file-paper-line"></i> {{ __('Invoices') }}
            </a>
            <a href="{{ route('clients.tickets.index') }}" class="md:px-2 py-3 flex items-center gap-x-2 hover:text-secondary-800 duration-300">
                <i class="ri-customer-service-2-line @if (request()->routeIs('clients.tickets*')) text-primary-400 @endif"></i> {{ __('Tickets') }}
            </a>
            <a href="{{ route('clients.profile') }}" class="md:px-2 py-3 flex items-center gap-x-2 hover:text-secondary-800 duration-300">
                <i class="ri-user-6-line @if (request()->routeIs('clients.profile')) text-primary-400 @endif"></i> {{ __('Profile Settings') }}
            </a>
        </div>
    </div>
@else
    <div class="shrink-0 w-64">
        <div class="bg-secondary-50 dark:bg-secondary-200 dark:border-0 border-r-2 border-secondary-200 h-full min-h-[calc(100vh-60px)]">
        we
        </div>
    </div>
@endif