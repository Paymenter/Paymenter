<div class="mt-2 flex flex-col">
  <h4 class="text-2xl font-bold pb-3">Account</h4>

  @foreach (\App\Classes\Navigation::getAccountLinks() as $link)
    <a href="{{ route($link['route'], $link['params'] ?? null) }}" @class([
        'text-secondary' => $link['active'],
        'text-white hover:text-primary-500' => !$link['active'],
        'flex flex-row items-center py-1 gap-2',
    ]) wire:navigate>
      {{ $link['name'] }}
    </a>
  @endforeach

  @if (config('settings.credits_enabled'))
    <a href="{{ route('account.credits') }}"
      class="flex flex-row items-center py-1 gap-2 @if (route('account.credits') === request()->url()) text-secondary @else text-white hover:text-primary-500 @endif"
      wire:navigate>
      {{ __('account.credits') }}
    </a>
  @endif
</div>
