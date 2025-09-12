<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectToProvider($provider)
    {
        // Esta línea es genérica y funcionará para Authentik y otros
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(Request $request, $provider)
    {
        if ($request->input('error')) {
            return redirect()->route('login')->with('error', __('An error occurred: ') . $request->input('error_description'));
        }
        
        try {
            $socialiteUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'An error occurred: ' . $e->getMessage());
        }

        // Buscar al usuario por su email
        $user = User::where('email', $socialiteUser->getEmail())->first();
        
        // LÓGICA CLAVE: Si el usuario no existe en la base de datos de Paymenter, no puede entrar.
        if (!$user) {
            return redirect()->route('login')->with('error', __('Your account is not registered. Please contact an administrator.'));
        }

        // Si el usuario existe pero es su primer login con SSO, guardamos los datos del proveedor
        if (!$user->provider || !$user->provider_id) {
            $user->update([
                'provider' => $provider,
                'provider_id' => $socialiteUser->getId(),
            ]);
        }
        
        Auth::login($user, true);

        return redirect()->intended(route('home'));
    }
}