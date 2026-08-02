@if(session()->has('impersonating'))
    <div class="fixed bottom-0 right-0 z-50 flex gap-2 justify-center items-center bg-background-secondary shadow-lg p-4 w-full border-t border-neutral">
        <p>
            {{ __('You are currently impersonating') }}: 
            <strong>{{  auth()->user()->name }}</strong>
        </p>
        <a href="/admin/users/{{  auth()->user()->id }}/edit">
            <x-button.primary>
                {{ __('Leave') }}
            </x-button.primary>
        </a>
    </div>
@endif