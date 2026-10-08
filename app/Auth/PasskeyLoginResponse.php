<?php

namespace App\Auth;

use App\Actions\Auth\Login;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Passkeys\Contracts\PasskeyLoginResponse as PasskeyLoginResponseContract;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class PasskeyLoginResponse implements PasskeyLoginResponseContract
{
    public function __construct(private Login $loginAction) {}

    public function toResponse($request): Response
    {
        /** @var User $user */
        $user = Auth::user();
        $remember = $request->boolean('remember');

        if ($user->tfa_secret) {
            Auth::logout();
            Session::put('2fa', [
                'user_id' => $user->id,
                'remember' => $remember,
                'expires' => now()->addMinutes(5),
            ]);

            $redirect = route('2fa');
        } else {
            Auth::logout();
            $this->loginAction->execute($user, $remember);
            $redirect = session()->pull('url.intended', route('dashboard'));
        }

        if ($request->wantsJson()) {
            return new JsonResponse(['redirect' => $redirect]);
        }

        return redirect()->to($redirect);
    }
}
