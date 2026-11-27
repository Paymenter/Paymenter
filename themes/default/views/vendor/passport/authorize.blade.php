<x-app-layout>

    <div
        class="mx-auto flex flex-col gap-2 mt-4 shadow-sm px-6 sm:px-14 py-10 bg-background-secondary rounded-md xl:max-w-[60%] w-full">
        <h1 class="text-2xl">
            {{ __('auth.oauth.authorization_request') }}
        </h1>
        <!-- Introduction -->
        <p class="mt-2"><strong>{{ $client->name }}</strong> {{ __('auth.oauth.requesting_permission') }}</p>

        <!-- Scope List -->
        @if (count($scopes) > 0)
        <div class="scopes">
            <p><strong>{{ __('auth.oauth.scopes_title') }}</strong></p>

            <ul class="list-disc list-inside">
                @foreach ($scopes as $scope)
                <li>{{ $scope->description }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="flex flex-row gap-2 mt-4">

            <!-- Cancel Button -->
            <form method="post" action="{{ route('passport.authorizations.deny') }}">
                @csrf
                @method('DELETE')

                <input type="hidden" name="state" value="{{ $request->state }}">
                <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                <input type="hidden" name="auth_token" value="{{ $authToken }}">
                <x-button.danger class="btn btn-danger">{{ __('common.cancel') }}</x-button.danger>
            </form>

            <!-- Authorize Button -->
            <form method="post" action="{{ route('passport.authorizations.approve') }}">
                @csrf

                <input type="hidden" name="state" value="{{ $request->state }}">
                <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                <input type="hidden" name="auth_token" value="{{ $authToken }}">
                <x-button.primary type="submit" class="btn btn-success btn-approve">{{ __('auth.oauth.authorize') }}</x-button.primary>
            </form>
        </div>

</x-app-layout>
