<div class="grid grid-cols-4">
    <div class="mt-2 flex flex-col">
        <h4 class="text-2xl font-bold pb-3">Account</h4>
        <a href="{{ route('account') }}" class="flex flex-row items-center py-1 gap-2 @if (route('account') === request()->url()) text-secondary @else text-base/50 hover:text-primary-500 @endif" wire:navigate>
            {{ __('account.personal_details') }}
        </a>
        <a href="{{ route('account.security') }}" class="flex flex-row items-center py-1 gap-2 @if (route('account.security') === request()->url()) text-secondary @else text-base/50 hover:text-primary-500 @endif" wire:navigate>
            Security
        </a>
    </div>
    <div class="bg-primary-800 p-8 rounded-lg mt-2 col-span-3">
        @yield('content')
    </div>
</div>
