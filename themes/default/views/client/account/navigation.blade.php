<div class="mt-2 flex flex-col">
    <h4 class="text-2xl font-bold pb-3">Account</h4>
    <a href="{{ route('account') }}" class="flex flex-row items-center py-1 gap-2 @if (route('account') === request()->url()) text-secondary @else text-white hover:text-primary-500 @endif" wire:navigate>
        {{ __('account.personal_details') }}
    </a>
    <a href="{{ route('account.security') }}" class="flex flex-row items-center py-1 gap-2 @if (route('account.security') === request()->url()) text-secondary @else text-white hover:text-primary-500 @endif" wire:navigate>
        Security
    </a>
    @if(config('settings.credits_enabled'))
        <a href="{{ route('account.credits') }}" class="flex flex-row items-center py-1 gap-2 @if (route('account.credits') === request()->url()) text-secondary @else text-white hover:text-primary-500 @endif" wire:navigate>
            {{ __('account.credits') }}
        </a>
    @endif
</div>