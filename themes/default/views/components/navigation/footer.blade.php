<div class="w-full mb-2 px-2">
    <div class="container mx-auto grid md:grid-cols-2 gap-4 items-center">
        <div class="flex flex-col gap-2">
            <x-logo />
            <div class="text-white text-sm">
                {{ __('© :year :app_name. All rights reserved.', ['year' => date('Y'), 'app_name' => config('app.name')]) }}
                {{ __('Powered By') }} <a href="https://paymenter.org"
                    class="text-secondary-500 hover:underline">Paymenter</a>
            </div>
        </div>

        <div class="w-40">
            <livewire:components.currency-switch />
        </div>
    </div>
</div>
