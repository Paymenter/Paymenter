<div class="mt-14 w-64 h-screen flex flex-col justify-between border-e border-neutral bg-background-secondary fixed top-0 left-0 z-10">
    <div class="px-4 py-6 overflow-y-auto">
        <ul class="space-y-1">
            <li>
                <x-navigation.link class="hover:bg-background-secondary/80 flex items-center justify-start rounded-lg" :href="route('home')">
                    <x-ri-home-2-fill class="w-5 h-5 {{ request()->url() === '/' ? 'text-primary' : ' hover:text-base/80' }}" />
                    <span>Home</span>
                </x-navigation.link>
            </li>
            <li>
                <x-navigation.link class="hover:bg-background-secondary/80 flex items-center justify-start rounded-lg" :href="route('dashboard')">
                    <x-ri-layout-row-fill class="w-5 h-5 {{ request()->url() === '/dashboard' ? 'text-primary' : ' hover:text-base/80' }}" />
                    <span>Dashboard</span>
                </x-navigation.link>
            </li>
            <li>
                <x-navigation.link class="hover:bg-background-secondary/80 flex items-center justify-start rounded-lg" :href="route('services')">
                    <x-ri-server-fill class="w-5 h-5 {{ request()->url() === '/services' ? 'text-primary' : ' hover:text-base/80' }}" />
                    <span>Services</span>
                </x-navigation.link>
            </li>
            <div class="h-px w-full bg-neutral"></div>
            <li class="rounded-lg {{ request()->is('tickets') ? 'bg-primary/10' : '' }}">
                <x-navigation.link class="hover:bg-background-secondary/80 flex items-center justify-start rounded-lg" :href="route('tickets')">
                    <x-ri-customer-service-2-fill class="w-5 h-5 {{ request()->url() === '/services' ? 'text-primary' : ' hover:text-base/80' }}" />
                    <span>Support</span>
                </x-navigation.link>
            </li>
            <div class="h-px w-full bg-neutral"></div>
            <li class="rounded-lg {{ request()->is('tickets') ? 'bg-primary/10' : '' }}">
                <x-navigation.link class="hover:bg-background-secondary/80 flex items-center justify-start rounded-lg" :href="route('tickets')">
                    <x-ri-settings-3-fill class="w-5 h-5 {{ request()->url() === '/services' ? 'text-primary' : ' hover:text-base/80' }}" />
                    <span>Account Details</span>
                </x-navigation.link>
            </li>
       
    </ul>
  </div>

  <div class="sticky inset-x-0 bottom-0">

  </div>
</div>